<?php
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

header('Content-Type: application/json; charset=utf-8');


$data = $_GET['data'] ?? '';
if (empty($data) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM servicos
        WHERE serdata_servico >= :ini AND serdata_servico < :fim
          AND " . sqlAgendamentoAtivo() . "
    ");
    [$ini, $fim] = intervaloDia($data);
    $stmt->execute([':ini' => $ini, ':fim' => $fim]);
    $total = (int)$stmt->fetchColumn();

    echo json_encode($total > 0 ? ['dia_ocupado'] : []);

} catch (PDOException $e) {
    error_log("Erro horarios_ocupados: " . $e->getMessage());
    echo json_encode([]);
}