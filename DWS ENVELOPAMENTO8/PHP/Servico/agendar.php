<?php
// =============================================
// agendar.php - Processa agendamento
// REGRA: 1 agendamento por DIA (global — se alguém agendou o dia, ninguém mais agenda)
// Online não pago expira após config('agenda.expira_pendente_horas') e libera o dia
// SEM horário: grava 12:00:00 fixo (só a data importa)
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderErro('Método não permitido.', 405);
}

if (empty($_SESSION['cliid'])) {
    responderErro('Você precisa estar logado para agendar.', 401);
}

exigirCSRF();

$cliid = (int)$_SESSION['cliid'];

// =============================================
// VALIDA CLIENTE
// =============================================
try {
    $stmt = $pdo->prepare("SELECT cliid, clinome, clitel, clicpf FROM clientes WHERE cliid = :id LIMIT 1");
    $stmt->execute([':id' => $cliid]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cliente) {
        session_destroy();
        responderErro('Sessão expirada. Faça login novamente.', 401);
    }
} catch (PDOException $e) {
    error_log("Erro agendar (validar cliente): " . $e->getMessage());
    responderErro('Erro ao validar sessão.', 500);
}

$nome      = $cliente['clinome'];
$telefone  = $cliente['clitel'] ?? '';
$cpf_input = $cliente['clicpf'] ?? '';
$cpf_limpo = preg_replace('/\D/', '', $cpf_input);

$email          = trim($_POST['email'] ?? '');
$veiculo_id     = (int)($_POST['veiculo_id'] ?? 0);
$descricao      = trim($_POST['descricao'] ?? '');
$tipo           = trim($_POST['tipo_servico'] ?? '');
$acabamento     = trim($_POST['acabamento'] ?? '1.0');
$data_ag        = trim($_POST['data_agendamento'] ?? '');
$tipo_pagamento = trim($_POST['tipo_pagamento'] ?? 'online');

// =============================================
// CONFIG
// =============================================
$configs = [];
try {
    $configs = $pdo->query("
        SELECT chave, valor FROM configuracoes
        WHERE chave LIKE 'preco_%' OR chave LIKE 'acabamento_%'
    ")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    error_log("Aviso agendar (configuracoes): " . $e->getMessage());
}

$precos_base = [
    'carro'    => (float)($configs['preco_carro'] ?? 800),
    'moto'     => (float)($configs['preco_moto'] ?? 500),
    'caminhao' => (float)($configs['preco_caminhao'] ?? 2500),
    'aquatico' => (float)($configs['preco_aquatico'] ?? 1800),
    'mobilia'  => (float)($configs['preco_mobilia'] ?? 300),
];

$acabamentos = [
    '1.0'  => ['nome' => 'Fosco',       'fator' => 1.0],
    '1.15' => ['nome' => 'Brilhante',   'fator' => (float)($configs['acabamento_brilhante'] ?? 1.15)],
    '1.30' => ['nome' => 'Perolizado',  'fator' => (float)($configs['acabamento_perolizado'] ?? 1.30)],
    '1.40' => ['nome' => 'Texturizado', 'fator' => (float)($configs['acabamento_texturizado'] ?? 1.40)],
];

// =============================================
// VALIDAÇÕES
// =============================================
$erros = [];
if (empty($nome))       $erros[] = 'Nome obrigatório.';
if (empty($telefone))   $erros[] = 'Telefone obrigatório.';
if ($veiculo_id <= 0)   $erros[] = 'Veículo obrigatório.';
if (empty($descricao))  $erros[] = 'Descrição obrigatória.';
if (empty($tipo))       $erros[] = 'Tipo de serviço obrigatório.';
if (empty($data_ag))    $erros[] = 'Data obrigatória.';
if (!empty($cpf_limpo) && !validarCPF($cpf_limpo)) $erros[] = 'CPF cadastrado é inválido.';
if (!isset($precos_base[$tipo])) $erros[] = 'Tipo de serviço inválido.';
if (!isset($acabamentos[$acabamento])) $erros[] = 'Acabamento inválido.';
if (!in_array($tipo_pagamento, ['online', 'local'], true)) $erros[] = 'Pagamento inválido.';
if (!empty($email) && !validarEmail($email)) $erros[] = 'E-mail inválido.';
if (mb_strlen($descricao) > 500) $erros[] = 'Descrição muito longa (máx. 500 caracteres).';

if (!empty($erros)) responderErro(implode(' ', $erros), 400);

// O veículo vem do cadastro do ADM: precisa existir, estar visível e ser do tipo do serviço
try {
    $stmt = $pdo->prepare("SELECT vectipo, vecmarca, vecmodelo FROM veiculos WHERE vecid = :id AND vecativo = 1");
    $stmt->execute([':id' => $veiculo_id]);
    $veiculoRow = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro agendar (veiculo): " . $e->getMessage());
    responderErro('Erro ao validar o veículo.', 500);
}
if (!$veiculoRow) responderErro('Veículo não encontrado. Escolha outro modelo.', 400);
if ($veiculoRow['vectipo'] !== $tipo) responderErro('O modelo escolhido não é do tipo de serviço selecionado.', 400);
$veiculo = trim(($veiculoRow['vecmarca'] ?? '') . ' ' . $veiculoRow['vecmodelo']);
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_ag)) responderErro('Formato de data inválido.', 400);
if (!checkdate((int) substr($data_ag, 5, 2), (int) substr($data_ag, 8, 2), (int) substr($data_ag, 0, 4))) {
    responderErro('Data inválida.', 400);
}
if (strtotime($data_ag) < strtotime(date('Y-m-d'))) responderErro('Data no passado não é permitida.', 400);

// =============================================
// CÁLCULO
// =============================================
$preco_base      = $precos_base[$tipo];
$fator           = $acabamentos[$acabamento]['fator'];
$valor_total     = $preco_base * $fator;
$valor_total_str = number_format($valor_total, 2, '.', '');

// =============================================
// TRANSAÇÃO + REGRA "1 POR DIA (GLOBAL)"
// =============================================
// GET_LOCK serializa agendamentos do mesmo dia: duas requisições
// simultâneas não conseguem passar pela checagem ao mesmo tempo
$nomeLock = 'dws_agenda_' . $data_ag;
$lockObtido = false;

try {
    $stmt = $pdo->prepare("SELECT GET_LOCK(:nome, 10)");
    $stmt->execute([':nome' => $nomeLock]);
    $lockObtido = (int) $stmt->fetchColumn() === 1;
    if (!$lockObtido) {
        responderErro('Sistema ocupado. Tente novamente em alguns segundos.', 503);
    }

    $pdo->beginTransaction();

    [$diaIni, $diaFim] = intervaloDia($data_ag);
    $stmt = $pdo->prepare("
        SELECT serid FROM servicos
        WHERE serdata_servico >= :ini AND serdata_servico < :fim
          AND " . sqlAgendamentoAtivo() . "
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute([':ini' => $diaIni, ':fim' => $diaFim]);

    if ($stmt->fetch()) {
        $pdo->rollBack();
        responderErro('Este dia já tem um agendamento. Só é permitido 1 agendamento por dia. Escolha outra data.', 409);
    }

    // Descrição
    $nome_acabamento = $acabamentos[$acabamento]['nome'];
    $desc_completa = "Veículo: $veiculo | $descricao";
    if ($acabamento !== '1.0') {
        $pct = round(($fator - 1) * 100);
        $desc_completa .= " | Acabamento: $nome_acabamento (+{$pct}%)";
    }

    // Insert — sem hora específica, usa 12:00:00 para não quebrar DATE()
    $statusInicial = ($tipo_pagamento === 'local') ? 'pagar_no_local' : 'pendente';
    $stmt = $pdo->prepare("
        INSERT INTO servicos (cliid, tipo_servico, vecid, serdescricao, servalor, serstatus_pagamento, serdata_servico)
        VALUES (:cliid, :tipo, :vecid, :desc, :valor, :status, :data_hora)
    ");
    $stmt->execute([
        ':cliid'     => $cliid,
        ':tipo'      => $tipo,
        ':vecid'     => $veiculo_id,
        ':desc'      => $desc_completa,
        ':valor'     => $valor_total_str,
        ':status'    => $statusInicial,
        ':data_hora' => "$data_ag 12:00:00",
    ]);

    $serid = (int)$pdo->lastInsertId();
    $pdo->commit();
    $pdo->query("SELECT RELEASE_LOCK(" . $pdo->quote($nomeLock) . ")");
    $lockObtido = false;

    logAuditoria('agendamento_criado', ['serid' => $serid, 'cliid' => $cliid, 'tipo' => $tipo, 'data' => $data_ag]);

    // WhatsApp
    $data_fmt        = date('d/m/Y', strtotime($data_ag));
    $tipos_nomes     = [
        'carro' => 'Carro', 'moto' => 'Moto', 'caminhao' => 'Caminhão',
        'aquatico' => 'Aquático', 'mobilia' => 'Mobília',
    ];
    $tipo_nome       = $tipos_nomes[$tipo] ?? $tipo;
    $telefone_fmt    = formatarTelefone($telefone);
    $preco_base_fmt  = formatBr($preco_base);
    $valor_total_fmt = formatBr($valor_total);
    $pagamento_txt   = ($tipo_pagamento === 'local') ? 'Pagar no Local' : 'Pagar Online';

    $msg_texto = "NOVO AGENDAMENTO DWS!\n\n" .
        "Cliente: $nome\n" .
        "Telefone: $telefone_fmt\n" .
        "E-mail: $email\n\n" .
        "Data: $data_fmt\n\n" .
        "Veiculo: $veiculo\n" .
        "Servico: $tipo_nome\n" .
        "Acabamento: $nome_acabamento\n\n" .
        "Valor Base: R$ $preco_base_fmt\n" .
        "Valor Total: R$ $valor_total_fmt\n\n" .
        "Pagamento: $pagamento_txt\n\n" .
        "Descricao: $descricao\n\n" .
        "Agendamento #$serid";

    $msg = urlencode($msg_texto);

    responderSucesso("Agendamento #$serid realizado!", [
        'servico_id'     => $serid,
        'whatsapp_url'   => 'https://wa.me/' . preg_replace('/\D/', '', (string) config('contato.whatsapp', '')) . "?text=$msg",
        'tipo_pagamento' => $tipo_pagamento,
        'valor_total'    => $valor_total_str,
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if ($lockObtido) $pdo->query("SELECT RELEASE_LOCK(" . $pdo->quote($nomeLock) . ")");
    error_log("ERRO PDO agendar: " . $e->getMessage());
    $debug = (defined('DEBUG') && DEBUG) ? $e->getMessage() : null;
    responderErro('Erro interno ao processar. Tente novamente.', 500, ['debug' => $debug]);
}