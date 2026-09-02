<?php
include("../Banco/conexao.php");
include("config_mp.php");

// =============================================
// criar_preferencia.php - Gera o link de pagamento
// (Checkout Pro) para um agendamento já criado
// =============================================

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'erro','mensagem'=>'Método não permitido.']);
    exit;
}

if (empty($_SESSION['cliid'])) {
    http_response_code(401);
    echo json_encode(['status'=>'erro','mensagem'=>'Você precisa estar logado.']);
    exit;
}

$cliid = (int)$_SESSION['cliid'];
$serid = (int)($_POST['servico_id'] ?? 0);
// Email é opcional (nem sempre existe coluna de e-mail em clientes) - usado só p/ o checkout
$email_pagador = trim($_POST['email'] ?? '') ?: 'cliente@example.com';

if ($serid <= 0) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','mensagem'=>'Serviço inválido.']);
    exit;
}

try {
    // ---------------------------------------------
    // BUSCA O SERVIÇO E CONFIRMA QUE É DESSE CLIENTE
    // ---------------------------------------------
    $stmt = $pdo->prepare(
        "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor, s.serstatus_pagamento,
                c.clinome, c.clitel
         FROM servicos s
         JOIN clientes c ON c.cliid = s.cliid
         WHERE s.serid = :serid AND s.cliid = :cliid
         LIMIT 1"
    );
    $stmt->execute([':serid' => $serid, ':cliid' => $cliid]);
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$servico) {
        http_response_code(404);
        echo json_encode(['status'=>'erro','mensagem'=>'Agendamento não encontrado.']);
        exit;
    }

    if ($servico['serstatus_pagamento'] === 'aprovado') {
        http_response_code(409);
        echo json_encode(['status'=>'erro','mensagem'=>'Este agendamento já foi pago.']);
        exit;
    }

    $valor = (float)$servico['servalor'];

    // ---------------------------------------------
    // MONTA A PREFERÊNCIA DE PAGAMENTO
    // ---------------------------------------------
    $preference = [
        'items' => [[
            'title' => 'Serviço DWS - ' . ucfirst($servico['tipo_servico']),
            'description' => mb_substr($servico['serdescricao'], 0, 250),
            'quantity' => 1,
            'currency_id' => 'BRL',
            'unit_price' => $valor
        ]],
        'payer' => [
            'name' => $servico['clinome'],
            'email' => $email_pagador,
            'phone' => ['number' => preg_replace('/\D/', '', $servico['clitel'])]
        ],
        'back_urls' => [
            'success' => MP_URL_SUCESSO,
            'failure' => MP_URL_FALHA,
            'pending' => MP_URL_PENDENTE
        ],
        'auto_return' => 'approved',
        'notification_url' => MP_URL_WEBHOOK,
        // É assim que o webhook sabe a qual serviço o pagamento se refere
        'external_reference' => (string)$serid,
        'statement_descriptor' => 'DWS SERVICOS'
    ];

    $resposta = mpRequest('POST', '/checkout/preferences', $preference);

    if ($resposta['http_code'] !== 201 || empty($resposta['body']['id'])) {
        error_log("Erro ao criar preferência MP (serviço $serid): " . json_encode($resposta));
        http_response_code(502);
        echo json_encode(['status'=>'erro','mensagem'=>'Não foi possível iniciar o pagamento. Tente novamente.']);
        exit;
    }

    $preferenceId = $resposta['body']['id'];
    $initPoint = MP_AMBIENTE === 'production'
        ? $resposta['body']['init_point']
        : $resposta['body']['sandbox_init_point'];

    // Salva o ID da preferência para conseguir rastrear depois
    $upd = $pdo->prepare("UPDATE servicos SET sermp_preference_id = :pref WHERE serid = :serid");
    $upd->execute([':pref' => $preferenceId, ':serid' => $serid]);

    error_log("Preferência MP criada: $preferenceId para serviço $serid (R$ $valor)");

    echo json_encode([
        'status' => 'sucesso',
        'preference_id' => $preferenceId,
        'init_point' => $initPoint
    ]);

} catch (PDOException $e) {
    error_log("ERRO PDO (criar_preferencia): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Erro interno ao gerar pagamento.']);
}