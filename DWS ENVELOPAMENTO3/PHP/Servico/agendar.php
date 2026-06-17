<?php
// PHP/Servico/agendar.php

// Ativar erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once("../Banco/conexao.php");
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!$conn) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro de conexão com o banco de dados!']);
        exit;
    }

    // Pegar dados do formulário
    $nome = mysqli_real_escape_string($conn, $_POST['nome'] ?? '');
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $veiculo = mysqli_real_escape_string($conn, $_POST['veiculo'] ?? '');
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao'] ?? '');
    $tipo_servico = mysqli_real_escape_string($conn, $_POST['tipo_servico'] ?? '');
    $acabamento = mysqli_real_escape_string($conn, $_POST['acabamento'] ?? 'Fosco');
    $data_agendamento = mysqli_real_escape_string($conn, $_POST['data_agendamento'] ?? '');
    $horario_agendamento = mysqli_real_escape_string($conn, $_POST['horario_agendamento'] ?? '');
    $valor_total = mysqli_real_escape_string($conn, $_POST['valor_total'] ?? '0');
    $valor_base = mysqli_real_escape_string($conn, $_POST['valor_base'] ?? '0');

    // Validar campos obrigatórios
    $erros = [];
    if (empty($nome)) $erros[] = "Nome é obrigatório";
    if (empty($telefone)) $erros[] = "Telefone é obrigatório";
    if (empty($veiculo)) $erros[] = "Veículo/Mobília é obrigatório";
    if (empty($descricao)) $erros[] = "Descrição é obrigatória";
    if (empty($tipo_servico)) $erros[] = "Tipo de serviço é obrigatório";
    if (empty($data_agendamento)) $erros[] = "Data é obrigatória";
    if (empty($horario_agendamento)) $erros[] = "Horário é obrigatório";

    if (!empty($erros)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => implode(", ", $erros)
        ]);
        exit;
    }

    // Verificar se o horário já está ocupado
    $sql_verifica = "SELECT * FROM servicos 
                     WHERE age_data = '$data_agendamento' 
                     AND age_horario = '$horario_agendamento' 
                     AND age_status != 'cancelado'";
    $result_verifica = $conn->query($sql_verifica);

    if ($result_verifica && $result_verifica->num_rows > 0) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Este horário já está ocupado! Escolha outro horário.'
        ]);
        exit;
    }

    // ==========================================
    // BUSCAR OU CRIAR CLIENTE - CORRIGIDO
    // ==========================================
    $cliid = null;
    
    // Primeiro tenta buscar pelo telefone
    $sql_busca_cliente = "SELECT cliid FROM clientes WHERE clitel = '$telefone'";
    $result_cliente = $conn->query($sql_busca_cliente);

    if ($result_cliente && $result_cliente->num_rows > 0) {
        $cliente = $result_cliente->fetch_assoc();
        $cliid = $cliente['cliid'];
    } else {
        // 🔧 CORRIGIDO: cliemail (não licemail)
        $sql_insert_cliente = "INSERT INTO clientes (clinome, clitel, cliemail, tipocliente) 
                               VALUES ('$nome', '$telefone', '$email', 'cliente')";
        
        if ($conn->query($sql_insert_cliente)) {
            $cliid = $conn->insert_id;
        } else {
            echo json_encode([
                'status' => 'erro',
                'mensagem' => 'Erro ao criar cliente: ' . $conn->error
            ]);
            exit;
        }
    }

    // Verificar se o cliid foi obtido
    if (empty($cliid) || $cliid == 0) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Não foi possível identificar o cliente'
        ]);
        exit;
    }

    // ==========================================
    // INSERIR AGENDAMENTO
    // ==========================================
    $sql_insert = "INSERT INTO servicos (
        cliid, 
        tipo_servico, 
        serdescricao, 
        servalor, 
        serdata_servico,
        age_data,
        age_horario,
        age_veiculo,
        age_acabamento,
        age_valor_base,
        age_nome_cliente,
        age_telefone_cliente,
        age_email_cliente,
        age_status
    ) VALUES (
        '$cliid',
        '$tipo_servico',
        '$descricao',
        '$valor_total',
        NOW(),
        '$data_agendamento',
        '$horario_agendamento',
        '$veiculo',
        '$acabamento',
        '$valor_base',
        '$nome',
        '$telefone',
        '$email',
        'pendente'
    )";

    if ($conn->query($sql_insert)) {
        $servico_id = $conn->insert_id;

        // Enviar para WhatsApp
        $telefone_whatsapp = "5514996175617";
        $texto = "*📋 NOVO AGENDAMENTO - DWS ENVELOPAMENTO*%0A%0A";
        $texto .= "*Cliente:* $nome%0A";
        $texto .= "*Telefone:* $telefone%0A";
        $texto .= "*E-mail:* " . ($email ?: "Não informado") . "%0A";
        $texto .= "*Veículo:* $veiculo%0A";
        $texto .= "*Tipo:* " . ucfirst($tipo_servico) . "%0A";
        $texto .= "*Acabamento:* $acabamento%0A";
        $texto .= "*Data:* $data_agendamento%0A";
        $texto .= "*Horário:* $horario_agendamento%0A";
        $texto .= "*Valor Total:* R$ " . number_format((float)$valor_total, 2, ',', '.') . "%0A";
        $texto .= "*Descrição:* $descricao%0A%0A";
        $texto .= "🔗 ID do Agendamento: #$servico_id%0A";
        $texto .= "Enviado pelo site da DWS Envelopamento.";

        $url_whatsapp = "https://wa.me/$telefone_whatsapp?text=$texto";

        echo json_encode([
            'status' => 'sucesso',
            'mensagem' => 'Agendamento realizado com sucesso!',
            'id' => $servico_id,
            'cliid' => $cliid,
            'whatsapp_url' => $url_whatsapp,
            'dados' => [
                'nome' => $nome,
                'data' => $data_agendamento,
                'horario' => $horario_agendamento,
                'total' => $valor_total
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Erro ao salvar agendamento: ' . $conn->error
        ]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido. Use POST.']);
    exit();
}
?>