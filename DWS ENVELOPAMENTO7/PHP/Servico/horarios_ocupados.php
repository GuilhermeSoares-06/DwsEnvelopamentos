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
    // Duração por tipo de serviço (minutos)
    $duracoes = [
        'carro'    => 240,
        'moto'     => 120,
        'caminhao' => 480,
        'aquatico' => 300,
        'mobilia'  => 120
    ];
    $duracaoPadrao = 120;

    // Busca serviços do dia, EXCLUINDO cancelados e rejeitados
    $stmt = $pdo->prepare(
        "SELECT TIME_FORMAT(TIME(serdata_servico), '%H:%i') as horario_inicio,
                tipo_servico
         FROM servicos 
         WHERE DATE(serdata_servico) = :data
         AND serstatus_pagamento NOT IN ('cancelado', 'rejeitado', 'estornado')
         ORDER BY serdata_servico ASC"
    );
    $stmt->execute([':data' => $data]);
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ocupados = [];
    foreach ($servicos as $s) {
        $inicio = $s['horario_inicio'];
        $ocupados[] = $inicio;

        $duracao = $duracoes[$s['tipo_servico']] ?? $duracaoPadrao;

        $partes = explode(':', $inicio);
        $totalMinutos = ((int)$partes[0]) * 60 + (int)$partes[1];
        $blocos = ceil($duracao / 30);

        for ($i = 1; $i < $blocos; $i++) {
            $novoTotal = $totalMinutos + ($i * 30);
            $novaHora = floor($novoTotal / 60);
            $novoMinuto = $novoTotal % 60;
            if ($novaHora < 18) {
                $ocupados[] = sprintf('%02d:%02d', $novaHora, $novoMinuto);
            }
        }
    }

    echo json_encode(array_values(array_unique($ocupados)));

} catch (PDOException $e) {
    error_log("Erro ao buscar horários ocupados: " . $e->getMessage());
    echo json_encode([]);
}
?>