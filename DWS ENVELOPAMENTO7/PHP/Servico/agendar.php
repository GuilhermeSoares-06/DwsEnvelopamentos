<?php
// =============================================
// agendar.php - Processa agendamento
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderErro('Método não permitido.', 405);
}

if (empty($_SESSION['cliid'])) {
    responderErro('Você precisa estar logado para agendar.', 401);
}

$cliid = (int)$_SESSION['cliid'];

// Valida que o cliente ainda existe
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

// Recebe dados
$email          = trim($_POST['email'] ?? '');
$veiculo        = trim($_POST['veiculo'] ?? '');
$descricao      = trim($_POST['descricao'] ?? '');
$tipo           = trim($_POST['tipo_servico'] ?? '');
$acabamento     = trim($_POST['acabamento'] ?? '1.0');
$data_ag        = trim($_POST['data_agendamento'] ?? '');
$horario_ag     = trim($_POST['horario_agendamento'] ?? '');
$tipo_pagamento = trim($_POST['tipo_pagamento'] ?? 'online');

// Preços
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

// Validações
$erros = [];
if (empty($nome))       $erros[] = 'Nome obrigatório.';
if (empty($telefone))   $erros[] = 'Telefone obrigatório.';
if (empty($veiculo))    $erros[] = 'Veículo obrigatório.';
if (empty($descricao))  $erros[] = 'Descrição obrigatória.';
if (empty($tipo))       $erros[] = 'Tipo de serviço obrigatório.';
if (empty($data_ag))    $erros[] = 'Data obrigatória.';
if (empty($horario_ag)) $erros[] = 'Horário obrigatório.';
if (!empty($cpf_limpo) && !validarCPF($cpf_limpo)) $erros[] = 'CPF cadastrado é inválido.';
if (!isset($precos_base[$tipo])) $erros[] = 'Tipo de serviço inválido.';
if (!isset($acabamentos[$acabamento])) $erros[] = 'Acabamento inválido.';
if ($tipo_pagamento !== 'online' && $tipo_pagamento !== 'local') $erros[] = 'Pagamento inválido.';

if (!empty($erros)) responderErro(implode(' ', $erros), 400);
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_ag)) responderErro('Formato de data inválido.', 400);
if (!preg_match('/^\d{2}:\d{2}$/', $horario_ag))    responderErro('Formato de horário inválido.', 400);
if (strtotime($data_ag) < strtotime(date('Y-m-d'))) responderErro('Data no passado não é permitida.', 400);

// Calcula valor
$preco_base      = $precos_base[$tipo];
$fator           = $acabamentos[$acabamento]['fator'];
$valor_total     = $preco_base * $fator;
$valor_total_str = number_format($valor_total, 2, '.', '');

try {
    // 1. Mesmo cliente no mesmo horário
    $stmt = $pdo->prepare("
        SELECT serid FROM servicos
        WHERE cliid = :cliid AND DATE(serdata_servico) = :data
          AND TIME_FORMAT(TIME(serdata_servico), '%H:%i') = :hora
          AND serstatus_pagamento != 'cancelado'
        LIMIT 1
    ");
    $stmt->execute([':cliid' => $cliid, ':data' => $data_ag, ':hora' => $horario_ag]);
    if ($stmt->rowCount() > 0) responderErro('Você já tem um agendamento neste horário.', 409);

    // 2. Horário ocupado
    $stmt = $pdo->prepare("
        SELECT serid FROM servicos
        WHERE DATE(serdata_servico) = :data
          AND TIME_FORMAT(TIME(serdata_servico), '%H:%i') = :hora
        LIMIT 1
    ");
    $stmt->execute([':data' => $data_ag, ':hora' => $horario_ag]);
    if ($stmt->rowCount() > 0) responderErro('Este horário já está ocupado.', 409);

    // 3. Atualiza telefone
    $stmt = $pdo->prepare("UPDATE clientes SET clitel = :tel WHERE cliid = :id");
    $stmt->execute([':tel' => $telefone, ':id' => $cliid]);

    // 4. Descrição
    $nome_acabamento = $acabamentos[$acabamento]['nome'];
    $desc_completa = "Veículo: $veiculo | $descricao";
    if ($acabamento !== '1.0') {
        $pct = round(($fator - 1) * 100);
        $desc_completa .= " | Acabamento: $nome_acabamento (+{$pct}%)";
    }

    // 5. Insert
    $statusInicial = ($tipo_pagamento === 'local') ? 'pagar_no_local' : 'pendente';
    $stmt = $pdo->prepare("
        INSERT INTO servicos (cliid, tipo_servico, serdescricao, servalor, serstatus_pagamento, serdata_servico)
        VALUES (:cliid, :tipo, :desc, :valor, :status, :data_hora)
    ");
    $stmt->execute([
        ':cliid'     => $cliid,
        ':tipo'      => $tipo,
        ':desc'      => $desc_completa,
        ':valor'     => $valor_total_str,
        ':status'    => $statusInicial,
        ':data_hora' => "$data_ag $horario_ag:00",
    ]);

    $serid = (int)$pdo->lastInsertId();

    logAuditoria('agendamento_criado', ['serid' => $serid, 'cliid' => $cliid, 'tipo' => $tipo]);

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
        "Data: $data_fmt\n" .
        "Horario: $horario_ag\n\n" .
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
        'whatsapp_url'   => "https://wa.me/5514996175617?text=$msg",
        'tipo_pagamento' => $tipo_pagamento,
        'valor_total'    => $valor_total_str,
    ]);

} catch (PDOException $e) {
    error_log("ERRO PDO agendar: " . $e->getMessage());
    $debug = (defined('DEBUG') && DEBUG) ? $e->getMessage() : null;
    responderErro('Erro interno ao processar. Tente novamente.', 500, ['debug' => $debug]);
}