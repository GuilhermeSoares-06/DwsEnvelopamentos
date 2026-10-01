<?php
// =============================================
// DadosPFun.php - Dados do dashboard admin
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once __DIR__ . '/../Banco/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'erro' => 'Não autorizado']);
    exit;
}

try {
    // 🔥 Últimos serviços COM status do serviço
    $stmtServicos = $pdo->prepare("
        SELECT
            c.clinome,
            s.tipo_servico,
            s.servalor,
            s.serstatus_pagamento,
            s.serstatus_servico,
            s.serdata_servico
        FROM servicos s
        INNER JOIN clientes c ON c.cliid = s.cliid
        ORDER BY s.serid DESC
        LIMIT 5
    ");
    $stmtServicos->execute();
    $servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);

    // Últimos clientes
    $stmtClientes = $pdo->prepare("
        SELECT cliid, clinome, clitel, cliendereco
        FROM clientes
        WHERE tipocliente = 'cliente' OR tipocliente IS NULL
        ORDER BY cliid DESC
        LIMIT 5
    ");
    $stmtClientes->execute();
    $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'servicos' => $servicos,
        'clientes' => $clientes,
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Erro no dashboard: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'erro' => 'Erro ao consultar o banco de dados.'
    ], JSON_UNESCAPED_UNICODE);
}