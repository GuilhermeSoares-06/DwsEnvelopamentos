<?php
include("../Banco/conexao.php");
include("../Clientes/validacao.php");

// =============================================
// agendar.php - Processa agendamento de serviços
// =============================================

// Inicia sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// =============================================
// VERIFICA SE É POST
// =============================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'erro','mensagem'=>'Método não permitido.']);
    exit;
}

// =============================================
// EXIGE CLIENTE LOGADO
// =============================================
if (empty($_SESSION['cliid'])) {
    http_response_code(401);
    echo json_encode(['status'=>'erro','mensagem'=>'Você precisa estar logado para agendar.']);
    exit;
}

$cliid       = (int)$_SESSION['cliid'];
$nome        = trim($_SESSION['clinome'] ?? '');
$telefone    = trim($_SESSION['clitel'] ?? '');
$cpf_input   = trim($_SESSION['clicpf'] ?? '');

// =============================================
// RECEBE E LIMPA OS DEMAIS DADOS
// =============================================
$email       = trim($_POST['email'] ?? '');
$veiculo     = trim($_POST['veiculo'] ?? '');
$descricao   = trim($_POST['descricao'] ?? '');
$tipo        = trim($_POST['tipo_servico'] ?? '');
$acabamento  = trim($_POST['acabamento'] ?? '1.0');
$data_ag     = trim($_POST['data_agendamento'] ?? '');
$horario_ag  = trim($_POST['horario_agendamento'] ?? '');
$valor_base  = trim($_POST['valor_base'] ?? '0');
$valor_total = trim($_POST['valor_total'] ?? '0');

// LIMPA O CPF
$cpf_limpo = preg_replace('/\D/', '', $cpf_input);

// LOG PARA DEPURAÇÃO
error_log("=== AGENDAMENTO RECEBIDO ===");
error_log("Cliente: ID $cliid | Nome: $nome | CPF: $cpf_limpo | Tel: $telefone");
error_log("Dados: Tipo=$tipo | Acabamento=$acabamento | Data=$data_ag | Horario=$horario_ag");
error_log("Valores: Base=$valor_base | Total=$valor_total");

// =============================================
// MAPEAMENTO DE PREÇOS BASE POR TIPO DE SERVIÇO
// =============================================
$precos_base = [
    'carro' => 800.00,
    'moto' => 500.00,
    'caminhao' => 2500.00,
    'aquatico' => 1800.00,
    'mobilia' => 300.00
];

// =============================================
// MAPEAMENTO DE ACABAMENTOS
// =============================================
$acabamentos = [
    '1.0' => ['nome' => 'Fosco', 'fator' => 1.0],
    '1.15' => ['nome' => 'Brilhante', 'fator' => 1.15],
    '1.30' => ['nome' => 'Perolizado', 'fator' => 1.30],
    '1.40' => ['nome' => 'Texturizado', 'fator' => 1.40]
];

// =============================================
// VALIDAÇÕES
// =============================================
$erros = [];

// Campos obrigatórios
if (empty($nome)) $erros[] = 'Nome é obrigatório.';
if (empty($telefone)) $erros[] = 'Telefone é obrigatório.';
if (empty($veiculo)) $erros[] = 'Veículo/Mobília é obrigatório.';
if (empty($descricao)) $erros[] = 'Descrição do serviço é obrigatória.';
if (empty($tipo)) $erros[] = 'Tipo de serviço é obrigatório.';
if (empty($data_ag)) $erros[] = 'Data do agendamento é obrigatória.';
if (empty($horario_ag)) $erros[] = 'Horário é obrigatório.';

// Valida CPF
if (empty($cpf_limpo)) {
    $erros[] = 'CPF é obrigatório. Atualize seu cadastro com um CPF válido.';
} elseif (strlen($cpf_limpo) !== 11) {
    $erros[] = 'CPF cadastrado é inválido (não tem 11 dígitos). Atualize seu cadastro.';
} elseif (!validarCPF($cpf_limpo)) {
    $erros[] = 'CPF cadastrado é inválido. Atualize seu cadastro.';
}

// Valida tipo de serviço
if (!isset($precos_base[$tipo])) {
    $erros[] = 'Tipo de serviço inválido.';
}

// Valida acabamento
if (!isset($acabamentos[$acabamento])) {
    $erros[] = 'Tipo de acabamento inválido.';
}

// Se tiver erros, retorna
if (!empty($erros)) {
    http_response_code(400);
    $msg = implode(' ', $erros);
    error_log("ERROS: " . $msg);
    echo json_encode(['status'=>'erro','mensagem'=>$msg]);
    exit;
}

// =============================================
// RECALCULA O VALOR TOTAL (segurança - não confia no frontend)
// =============================================
$preco_base = $precos_base[$tipo];
$fator_acabamento = $acabamentos[$acabamento]['fator'];
$valor_total_calculado = $preco_base * $fator_acabamento;

// Formata para 2 casas decimais
$valor_total_calculado = number_format($valor_total_calculado, 2, '.', '');
$preco_base_formatado = number_format($preco_base, 2, '.', '');

error_log("Cálculo: Base=$preco_base_formatado x Fator=$fator_acabamento = Total=$valor_total_calculado");

// =============================================
// PROCESSA AGENDAMENTO
// =============================================
try {
    // ---------------------------------------------
    // VERIFICA SE HORÁRIO ESTÁ OCUPADO
    // ---------------------------------------------
    $stmt = $pdo->prepare(
        "SELECT serid FROM servicos
         WHERE DATE(serdata_servico) = :data
         AND TIME_FORMAT(TIME(serdata_servico), '%H:%i') = :hora
         LIMIT 1"
    );
    $stmt->execute([
        ':data' => $data_ag,
        ':hora' => $horario_ag
    ]);

    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['status'=>'erro','mensagem'=>'Este horário já está ocupado. Escolha outro.']);
        exit;
    }

    // ---------------------------------------------
    // ATUALIZA DADOS DO CLIENTE
    // ---------------------------------------------
    $stmt = $pdo->prepare("UPDATE clientes SET clinome = :nome, clitel = :telefone WHERE cliid = :id");
    $stmt->execute([
        ':nome' => $nome,
        ':telefone' => $telefone,
        ':id' => $cliid
    ]);

    // ---------------------------------------------
    // CRIA DESCRIÇÃO COMPLETA
    // ---------------------------------------------
    $nome_acabamento = $acabamentos[$acabamento]['nome'];
    $desc_completa = "Veículo: $veiculo | $descricao";
    if ($acabamento !== '1.0') {
        $desc_completa .= " | Acabamento: $nome_acabamento (+" . (($fator_acabamento - 1) * 100) . "%)";
    }

    // ---------------------------------------------
    // INSERE SERVIÇO
    // ---------------------------------------------
    $stmt = $pdo->prepare(
        "INSERT INTO servicos (cliid, tipo_servico, serdescricao, servalor, serdata_servico)
         VALUES (:cliid, :tipo, :descricao, :valor, :data_hora)"
    );
    $stmt->execute([
        ':cliid' => $cliid,
        ':tipo' => $tipo,
        ':descricao' => $desc_completa,
        ':valor' => $valor_total_calculado,
        ':data_hora' => "$data_ag $horario_ag:00"
    ]);

    $serid = (int)$pdo->lastInsertId();
    error_log("Agendamento criado: ID $serid para cliente $cliid - Valor: R$ $valor_total_calculado");

    // ---------------------------------------------
    // LINK WHATSAPP
    // ---------------------------------------------
    $msg = urlencode(
        "🆕 NOVO AGENDAMENTO DWS!\n\n" .
        "👤 Cliente: $nome\n📞 $telefone\n📧 $email\n\n" .
        "📅 Data: $data_ag  🕐 Horário: $horario_ag\n" .
        "🚗 Veículo: $veiculo\n" .
        "🔧 Serviço: $tipo\n" .
        "🎨 Acabamento: $nome_acabamento\n" .
        "💰 Valor Base: R$ " . number_format($preco_base, 2, ',', '.') . "\n" .
        "💰 Valor Total: R$ " . number_format($valor_total_calculado, 2, ',', '.') . "\n\n" .
        "📝 $descricao\n\n" .
        "✅ Agendamento #$serid"
    );
    $whatsapp_url = "https://wa.me/5514996175617?text=$msg";

    // ---------------------------------------------
    // RETORNA SUCESSO
    // ---------------------------------------------
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => "Agendamento #$serid realizado com sucesso!",
        'servico_id' => $serid,
        'whatsapp_url' => $whatsapp_url,
        'cliente_id' => $cliid,
        'valor_base' => $preco_base_formatado,
        'valor_total' => $valor_total_calculado,
        'acabamento' => $nome_acabamento
    ]);

} catch (PDOException $e) {
    error_log("ERRO PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Erro interno ao processar o agendamento: ' . $e->getMessage()]);
}