<?php
include("../Banco/conexao.php");
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT DISTINCT DATE(serdata_servico) as data
        FROM servicos
        WHERE serstatus_pagamento NOT IN ('cancelado', 'rejeitado', 'estornado')
          AND serdata_servico >= CURDATE()
    ");
    $dias = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($dias);
} catch (PDOException $e) {
    echo json_encode([]);
}