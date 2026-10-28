<?php
// =============================================
// meus_agendamentos.php
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);


header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (empty($_SESSION['cliid'])) {
    echo json_encode([
        'logado' => false,
        'status' => 'erro',
        'mensagem' => 'Não autenticado'
    ]);
    exit;
}

$cliid = (int)$_SESSION['cliid'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            serid,
            tipo_servico,
            serdescricao,
            servalor,
            CASE WHEN serstatus_pagamento = 'pendente' AND NOT (" . sqlAgendamentoAtivo() . ")
                 THEN 'expirado' ELSE serstatus_pagamento END AS serstatus_pagamento,
            serstatus_servico,
            serdata_servico
        FROM servicos
        WHERE cliid = :cliid
        ORDER BY serdata_servico DESC
        LIMIT 50
    ");
    $stmt->execute([':cliid' => $cliid]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'logado' => true,
        'status' => 'sucesso',
        'agendamentos' => $agendamentos,
        'total' => count($agendamentos)
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Erro meus_agendamentos: " . $e->getMessage());
    echo json_encode([
        'logado' => true,
        'status' => 'erro',
        'mensagem' => 'Erro ao buscar agendamentos'
    ]);
}