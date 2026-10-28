<?php
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT DISTINCT DATE(serdata_servico) as data
        FROM servicos
        WHERE serdata_servico >= CURDATE()
          AND " . sqlAgendamentoAtivo() . "
    ");
    $dias = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($dias);
} catch (PDOException $e) {
    error_log('[dias_ocupados] ' . $e->getMessage());
    echo json_encode([]);
}