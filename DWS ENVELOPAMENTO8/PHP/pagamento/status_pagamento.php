<?php
include("../Banco/conexao.php");

// =============================================
// status_pagamento.php - Consulta o status atual
// de pagamento de um agendamento (usado na tela
// de retorno para saber se já foi confirmado)
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['cliid'])) {
    http_response_code(401);
    echo json_encode(['status'=>'erro','mensagem'=>'Não autenticado.']);
    exit;
}

$serid = (int)($_GET['servico_id'] ?? 0);
$cliid = (int)$_SESSION['cliid'];

if ($serid <= 0) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','mensagem'=>'Serviço inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT serid, serstatus_pagamento, sermp_payment_id, servalor
         FROM servicos WHERE serid = :serid AND cliid = :cliid LIMIT 1"
    );
    $stmt->execute([':serid' => $serid, ':cliid' => $cliid]);
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$servico) {
        http_response_code(404);
        echo json_encode(['status'=>'erro','mensagem'=>'Não encontrado.']);
        exit;
    }

    echo json_encode(['status'=>'sucesso', 'dados'=>$servico]);

} catch (PDOException $e) {
    error_log("ERRO PDO (status_pagamento): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Erro interno.']);
}