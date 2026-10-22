<?php
include("../Banco/conexao.php");

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$data = $_GET['data'] ?? '';
if (empty($data) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM servicos
        WHERE DATE(serdata_servico) = :data
          AND serstatus_pagamento NOT IN ('cancelado', 'rejeitado', 'estornado')
    ");
    $stmt->execute([':data' => $data]);
    $total = (int)$stmt->fetchColumn();

    echo json_encode($total > 0 ? ['dia_ocupado'] : []);

} catch (PDOException $e) {
    error_log("Erro horarios_ocupados: " . $e->getMessage());
    echo json_encode([]);
}