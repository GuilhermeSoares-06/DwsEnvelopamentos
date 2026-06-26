<?php
/**
 * agendar.php - Processa o agendamento de serviços
 * Salva na tabela `servicos` do banco de dados
 */

// =============================================
// 1. CONFIGURAÇÃO INICIAL
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// =============================================
// 2. INCLUI A CONEXÃO COM O BANCO
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

// =============================================
// 3. RECEBE OS DADOS DO POST
// =============================================
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$veiculo = isset($_POST['veiculo']) ? trim($_POST['veiculo']) : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$tipo_servico = isset($_POST['tipo_servico']) ? trim($_POST['tipo_servico']) : '';
$acabamento = isset($_POST['acabamento']) ? trim($_POST['acabamento']) : '';
$data_agendamento = isset($_POST['data_agendamento']) ? trim($_POST['data_agendamento']) : '';
$horario_agendamento = isset($_POST['horario_agendamento']) ? trim($_POST['horario_agendamento']) : '';
$valor_total = isset($_POST['valor_total']) ? trim($_POST['valor_total']) : '';
$valor_base = isset($_POST['valor_base']) ? trim($_POST['valor_base']) : '';

// =============================================
// 4. VALIDAÇÕES
// =============================================
$erros = [];

if (empty($nome)) $erros[] = "Nome é obrigatório";
if (empty($telefone)) $erros[] = "Telefone é obrigatório";
if (empty($veiculo)) $erros[] = "Veículo/Mobília é obrigatório";
if (empty($descricao)) $erros[] = "Descrição do serviço é obrigatória";
if (empty($tipo_servico)) $erros[] = "Tipo de serviço é obrigatório";
if (empty($data_agendamento)) $erros[] = "Data do agendamento é obrigatória";
if (empty($horario_agendamento)) $erros[] = "Horário do agendamento é obrigatório";

if (!empty($erros)) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => implode(', ', $erros),
        'erros' => $erros
    ]);
    exit;
}

// =============================================
// 5. VERIFICA SE O CLIENTE JÁ EXISTE
// =============================================
$cliid = null;

try {
    // Procura cliente pelo telefone
    $sql_busca = "SELECT cliid FROM clientes WHERE clitel = :telefone";
    $stmt_busca = $pdo->prepare($sql_busca);
    $stmt_busca->execute([':telefone' => $telefone]);
    $cliente = $stmt_busca->fetch();
    
    if ($cliente) {
        $cliid = $cliente['cliid'];
    } else {
        // Se não existir, cadastra o cliente
        $sql_insert_cliente = "INSERT INTO clientes 
                               (clinome, clitel, cliendereco, tipocliente) 
                               VALUES 
                               (:nome, :telefone, :endereco, 'cliente')";
        $stmt_insert = $pdo->prepare($sql_insert_cliente);
        $stmt_insert->execute([
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':endereco' => 'Não informado'
        ]);
        $cliid = $pdo->lastInsertId();
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar/criar cliente: " . $e->getMessage());
}

// Se não conseguiu o cliid, tenta criar um registro sem ele
if (!$cliid) {
    // Cria um cliente temporário
    try {
        $sql_insert = "INSERT INTO clientes (clinome, clitel, cliendereco, tipocliente) 
                       VALUES (:nome, :telefone, 'Não informado', 'cliente')";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([
            ':nome' => $nome,
            ':telefone' => $telefone
        ]);
        $cliid = $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Erro ao criar cliente: " . $e->getMessage());
        $cliid = null;
    }
}

// =============================================
// 6. SALVA NA TABELA SERVICOS
// =============================================
$servico_id = null;

try {
    // Monta a descrição completa
    $descricao_completa = "Veículo: $veiculo | " . $descricao;
    if (!empty($acabamento) && $acabamento != '1.0') {
        $descricao_completa .= " | Acabamento: " . 
            ($acabamento == '1.15' ? 'Brilhante' : 
             ($acabamento == '1.30' ? 'Perolizado' : 
              ($acabamento == '1.40' ? 'Texturizado' : 'Fosco')));
    }
    
    $sql = "INSERT INTO servicos 
            (cliid, tipo_servico, serdescricao, servalor, serdata_servico) 
            VALUES 
            (:cliid, :tipo_servico, :descricao, :valor, :data_servico)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':cliid' => $cliid,
        ':tipo_servico' => $tipo_servico,
        ':descricao' => $descricao_completa,
        ':valor' => $valor_total,
        ':data_servico' => $data_agendamento . ' ' . $horario_agendamento . ':00'
    ]);
    
    $servico_id = $pdo->lastInsertId();
    
} catch (PDOException $e) {
    error_log("Erro ao salvar serviço: " . $e->getMessage());
    
    // Se der erro, tenta salvar em arquivo JSON como fallback
    $arquivo = __DIR__ . '/agendamentos_backup.json';
    $agendamentos = [];
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        if (!empty($conteudo)) {
            $agendamentos = json_decode($conteudo, true) ?: [];
        }
    }
    
    $agendamentos[] = [
        'id' => uniqid(),
        'cliid' => $cliid,
        'nome' => $nome,
        'telefone' => $telefone,
        'email' => $email,
        'veiculo' => $veiculo,
        'descricao' => $descricao_completa,
        'tipo_servico' => $tipo_servico,
        'acabamento' => $acabamento,
        'data_agendamento' => $data_agendamento,
        'horario_agendamento' => $horario_agendamento,
        'valor_total' => $valor_total,
        'valor_base' => $valor_base,
        'status' => 'pendente',
        'data_criacao' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($arquivo, json_encode($agendamentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Retorna erro avisando que foi salvo em backup
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Agendamento realizado com sucesso! (Salvo em backup local)',
        'servico_id' => null,
        'whatsapp_url' => $whatsapp_url ?? null
    ]);
    exit;
}

// =============================================
// 7. CRIA MENSAGEM PARA WHATSAPP
// =============================================
$mensagem_whatsapp = urlencode(
    "🆕 NOVO AGENDAMENTO DWS!\n\n" .
    "👤 Cliente: $nome\n" .
    "📞 Telefone: $telefone\n" .
    "📧 Email: $email\n\n" .
    "📅 Data: $data_agendamento\n" .
    "🕐 Horário: $horario_agendamento\n" .
    "🚗 Veículo: $veiculo\n" .
    "🔧 Serviço: $tipo_servico\n" .
    "💰 Valor: $valor_total\n\n" .
    "📝 Descrição:\n$descricao\n\n" .
    "✅ Agendamento ID: #$servico_id"
);

$whatsapp_url = "https://wa.me/5514996175617?text=" . $mensagem_whatsapp;

// =============================================
// 8. RETORNA RESPOSTA DE SUCESSO
// =============================================
echo json_encode([
    'status' => 'sucesso',
    'mensagem' => 'Agendamento realizado com sucesso! ID: #' . $servico_id,
    'servico_id' => $servico_id,
    'cliid' => $cliid,
    'whatsapp_url' => $whatsapp_url
]);

exit;
?>