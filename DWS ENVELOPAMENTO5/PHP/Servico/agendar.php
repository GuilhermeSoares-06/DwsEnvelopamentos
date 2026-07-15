<?php
include("../Banco/conexao.php");
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
// FUNÇÃO DE VALIDAÇÃO DE CPF (EMBUTIDA)
// =============================================
function validarCPF($cpf) {
    // Remove tudo que não é número
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se tem 11 dígitos
    if (strlen($cpf) !== 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais (ex: 111.111.111-11)
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Calcula o primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += (int)$cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Calcula o segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += (int)$cpf[$i] * (11 - $i);
    }
    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Verifica se os dígitos calculados conferem
    return ($cpf[9] == $digito1 && $cpf[10] == $digito2);
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
// RECEBE E LIMPA OS DADOS
// =============================================
$nome        = trim($_POST['nome'] ?? '');
$telefone    = trim($_POST['telefone'] ?? '');
$cpf_input   = trim($_POST['cpf'] ?? '');
$email       = trim($_POST['email'] ?? '');
$veiculo     = trim($_POST['veiculo'] ?? '');
$descricao   = trim($_POST['descricao'] ?? '');
$tipo        = trim($_POST['tipo_servico'] ?? '');
$acabamento  = trim($_POST['acabamento'] ?? '1.0');
$data_ag     = trim($_POST['data_agendamento'] ?? '');
$horario_ag  = trim($_POST['horario_agendamento'] ?? '');
$valor_total = trim($_POST['valor_total'] ?? '0');
$valor_base  = trim($_POST['valor_base'] ?? '0');

// LIMPA O CPF (remove pontuação)
$cpf_limpo = preg_replace('/\D/', '', $cpf_input);

// LOG PARA DEPURAÇÃO
error_log("=== AGENDAMENTO RECEBIDO ===");
error_log("Nome: $nome");
error_log("CPF original: $cpf_input");
error_log("CPF limpo: $cpf_limpo");
error_log("Tamanho CPF: " . strlen($cpf_limpo));
error_log("Telefone: $telefone");

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
    $erros[] = 'CPF é obrigatório.';
} elseif (strlen($cpf_limpo) !== 11) {
    $erros[] = 'CPF deve ter 11 dígitos.';
} elseif (!validarCPF($cpf_limpo)) {
    $erros[] = 'CPF inválido.';
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
// VERIFICA SE HORÁRIO ESTÁ OCUPADO
// =============================================
try {
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

    // =============================================
    // BUSCA OU CRIA CLIENTE
    // =============================================
    $cliid = null;

    // 1) Verifica se está logado
    if (!empty($_SESSION['cliid'])) {
        $cliid = (int)$_SESSION['cliid'];
        error_log("Cliente logado: ID $cliid");
    }

    // 2) Busca por CPF
    if (!$cliid) {
        $stmt = $pdo->prepare(
            "SELECT cliid, clinome, clitel FROM clientes 
             WHERE REPLACE(REPLACE(REPLACE(clicpf, '.', ''), '-', ''), ' ', '') = :cpf 
             LIMIT 1"
        );
        $stmt->execute([':cpf' => $cpf_limpo]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $cliid = (int)$cliente['cliid'];
            error_log("Cliente encontrado por CPF: ID $cliid");
            
            // Atualiza dados
            $stmt = $pdo->prepare("UPDATE clientes SET clinome = :nome, clitel = :telefone WHERE cliid = :id");
            $stmt->execute([
                ':nome' => $nome,
                ':telefone' => $telefone,
                ':id' => $cliid
            ]);
        }
    }

    // 3) Busca por telefone (se ainda não encontrou)
    if (!$cliid) {
        $telefone_limpo = preg_replace('/\D/', '', $telefone);
        $stmt = $pdo->prepare(
            "SELECT cliid FROM clientes 
             WHERE REPLACE(REPLACE(REPLACE(REPLACE(clitel, '(', ''), ')', ''), '-', ''), ' ', '') = :telefone 
             LIMIT 1"
        );
        $stmt->execute([':telefone' => $telefone_limpo]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $cliid = (int)$cliente['cliid'];
            error_log("Cliente encontrado por telefone: ID $cliid");
            
            // Atualiza CPF
            $stmt = $pdo->prepare("UPDATE clientes SET clinome = :nome, clicpf = :cpf WHERE cliid = :id");
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf_limpo,
                ':id' => $cliid
            ]);
        }
    }

    // 4) Cria novo cliente
    if (!$cliid) {
        $stmt = $pdo->prepare(
            "INSERT INTO clientes (clinome, clitel, clicpf, cliendereco, tipocliente) 
             VALUES (:nome, :telefone, :cpf, 'Não informado', 'cliente')"
        );
        $stmt->execute([
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':cpf' => $cpf_limpo
        ]);
        $cliid = (int)$pdo->lastInsertId();
        error_log("Novo cliente criado: ID $cliid");
    }

    // =============================================
    // CRIA DESCRIÇÃO COMPLETA
    // =============================================
    $acab_map = [
        '1.0' => 'Fosco',
        '1.15' => 'Brilhante',
        '1.30' => 'Perolizado',
        '1.40' => 'Texturizado'
    ];
    
    $desc_completa = "Veículo: $veiculo | $descricao";
    if ($acabamento !== '1.0') {
        $desc_completa .= ' | Acabamento: ' . ($acab_map[$acabamento] ?? $acabamento);
    }

    // =============================================
    // INSERE SERVIÇO
    // =============================================
    $stmt = $pdo->prepare(
        "INSERT INTO servicos (cliid, tipo_servico, serdescricao, servalor, serdata_servico) 
         VALUES (:cliid, :tipo, :descricao, :valor, :data_hora)"
    );
    $stmt->execute([
        ':cliid' => $cliid,
        ':tipo' => $tipo,
        ':descricao' => $desc_completa,
        ':valor' => $valor_total,
        ':data_hora' => "$data_ag $horario_ag:00"
    ]);
    
    $serid = (int)$pdo->lastInsertId();
    error_log("Agendamento criado: ID $serid");

    // =============================================
    // LINK WHATSAPP
    // =============================================
    $msg = urlencode(
        "🆕 NOVO AGENDAMENTO DWS!\n\n" .
        "👤 Cliente: $nome\n📞 $telefone\n📧 $email\n\n" .
        "📅 Data: $data_ag  🕐 Horário: $horario_ag\n" .
        "🚗 Veículo: $veiculo\n🔧 Serviço: $tipo\n💰 Valor: R$ $valor_total\n\n" .
        "📝 $descricao\n\n✅ Agendamento #$serid"
    );
    $whatsapp_url = "https://wa.me/5514996175617?text=$msg";

    // =============================================
    // RETORNA SUCESSO
    // =============================================
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => "Agendamento #$serid realizado com sucesso!",
        'servico_id' => $serid,
        'whatsapp_url' => $whatsapp_url,
        'cliente_id' => $cliid
    ]);

} catch (PDOException $e) {
    error_log("ERRO PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Erro interno ao processar o agendamento: ' . $e->getMessage()]);
}
?>