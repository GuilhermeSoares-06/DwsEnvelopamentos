<?php
include("../Banco/conexao.php");

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$data = $_GET['data'] ?? '';
if (empty($data)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT TIME_FORMAT(TIME(serdata_servico), '%H:%i') as horario 
         FROM servicos 
         WHERE DATE(serdata_servico) = :data"
    );
    $stmt->execute([':data' => $data]);
    
    $ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode($ocupados);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar horários ocupados: " . $e->getMessage());
    echo json_encode([]);
}