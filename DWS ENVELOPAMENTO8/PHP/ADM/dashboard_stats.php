<?php
// =============================================
// dashboard_stats.php — Estatísticas do admin
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

header('Content-Type: application/json; charset=utf-8');

exigirAdminApi();


try {
    $totalServicos = (int) $pdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();

    $totalClientes = (int) $pdo->query("
        SELECT COUNT(*) FROM clientes
        WHERE tipocliente = 'cliente' OR tipocliente IS NULL
    ")->fetchColumn();

    $servicosFinalizados = (int) $pdo->query("
        SELECT COUNT(*) FROM servicos
        WHERE serstatus_servico = 'finalizado'
    ")->fetchColumn();

    $servicosPendentes = (int) $pdo->query("
        SELECT COUNT(*) FROM servicos
        WHERE serstatus_servico <> 'finalizado' OR serstatus_servico IS NULL
    ")->fetchColumn();

    // Intervalos de mês em vez de MONTH()/YEAR(): permite usar o índice de serdata_servico
    $iniMes      = date('Y-m-01 00:00:00');
    $iniProxMes  = date('Y-m-01 00:00:00', strtotime('first day of next month'));
    $iniMesAnt   = date('Y-m-01 00:00:00', strtotime('first day of last month'));

    $porPeriodo = $pdo->prepare("
        SELECT COUNT(*) AS qtd, COALESCE(SUM(servalor), 0) AS total
        FROM servicos
        WHERE serdata_servico >= :ini AND serdata_servico < :fim
    ");

    $porPeriodo->execute([':ini' => $iniMes, ':fim' => $iniProxMes]);
    $mesAtual = $porPeriodo->fetch(PDO::FETCH_ASSOC);

    $porPeriodo->execute([':ini' => $iniMesAnt, ':fim' => $iniMes]);
    $mesAnterior = $porPeriodo->fetch(PDO::FETCH_ASSOC);

    $servicosMes          = (int) $mesAtual['qtd'];
    $servicosMesAnterior  = (int) $mesAnterior['qtd'];
    $faturamentoMes       = (float) $mesAtual['total'];
    $faturamentoAnterior  = (float) $mesAnterior['total'];

    $faturamentoTotal = (float) $pdo->query("SELECT COALESCE(SUM(servalor), 0) FROM servicos")->fetchColumn();

    $faturamentoAprovado = (float) $pdo->query("
        SELECT COALESCE(SUM(servalor), 0) FROM servicos
        WHERE serstatus_pagamento IN ('aprovado', 'pagar_no_local')
    ")->fetchColumn();

    $ticketMedio = $totalServicos > 0 ? ($faturamentoTotal / $totalServicos) : 0.0;

    echo json_encode([
        'success'                => true,
        'total_servicos'         => $totalServicos,
        'total_clientes'         => $totalClientes,
        'servicos_finalizados'   => $servicosFinalizados,
        'finalizados'            => $servicosFinalizados,
        'servicos_pendentes'     => $servicosPendentes,
        'pendentes'              => $servicosPendentes,
        'servicos_mes'           => $servicosMes,
        'servicos_mes_anterior'  => $servicosMesAnterior,
        'faturamento_total'      => round($faturamentoTotal, 2),
        'faturamento_aprovado'   => round($faturamentoAprovado, 2),
        'faturamento_mes'        => round($faturamentoMes, 2),
        'faturamento_mes_atual'  => round($faturamentoMes, 2),
        'faturamento_anterior'   => round($faturamentoAnterior, 2),
        'ticket_medio'           => round($ticketMedio, 2),
    ]);

} catch (PDOException $e) {
    error_log('[dashboard_stats] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno']);
}
exit;