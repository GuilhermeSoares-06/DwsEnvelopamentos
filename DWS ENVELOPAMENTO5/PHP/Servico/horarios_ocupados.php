<?php
// =============================================
// horarios_ocupados.php - Retorna horários ocupados para o front
// GET → JSON { status, horarios: [{data, horario}] }
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../Banco/conexao.php';

try {
    // Traz apenas agendamentos futuros (a partir de hoje)
    $stmt = $pdo->query(
        "SELECT DATE(serdata_servico) AS data,
                TIME_FORMAT(TIME(serdata_servico), '%H:%i') AS horario
         FROM servicos
         WHERE DATE(serdata_servico) >= CURDATE()
         ORDER BY serdata_servico ASC"
    );
    $rows = $stmt->fetchAll();

    echo json_encode([
        'status'   => 'sucesso',
        'horarios' => $rows,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'horarios' => []]);
}
?>
