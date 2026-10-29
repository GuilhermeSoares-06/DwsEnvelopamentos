<?php
// =============================================
// historico_faturamento.php — Faturamento por mês/ano
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

header('Content-Type: application/json; charset=utf-8');

exigirAdminApi();


try {
    $stmt = $pdo->query("
        SELECT serdata_servico, servalor, serstatus_pagamento
        FROM servicos
        ORDER BY serdata_servico ASC
    ");
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $porMes = [];
    foreach ($todos as $s) {
        $data = $s['serdata_servico'] ?? null;
        if (!$data) continue;
        $ts = strtotime($data);
        if (!$ts) continue;

        $chave = date('Y-m', $ts);
        if (!isset($porMes[$chave])) {
            $porMes[$chave] = [
                'mes_ano'             => $chave,
                'total_servicos'      => 0,
                'faturamento_total'   => 0.0,
                'faturamento_aprovado' => 0.0,
                'fechado_em'          => $data,
            ];
        }

        $valor  = valorParaFloat($s['servalor'] ?? 0);
        $status = strtolower((string) ($s['serstatus_pagamento'] ?? ''));

        $porMes[$chave]['total_servicos']++;
        $porMes[$chave]['faturamento_total'] += $valor;
        if (in_array($status, ['aprovado', 'pagar_no_local'], true)) {
            $porMes[$chave]['faturamento_aprovado'] += $valor;
        }
    }

    krsort($porMes);

    $mesesPt = [
        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
        '04' => 'Abril',   '05' => 'Maio',      '06' => 'Junho',
        '07' => 'Julho',   '08' => 'Agosto',    '09' => 'Setembro',
        '10' => 'Outubro', '11' => 'Novembro',  '12' => 'Dezembro',
    ];

    $historico = [];
    foreach ($porMes as $chave => $item) {
        [$ano, $mes] = explode('-', $chave);
        $historico[] = [
            'mes_ano'              => ($mesesPt[$mes] ?? $mes) . ' / ' . $ano,
            'total_servicos'       => $item['total_servicos'],
            'faturamento_total'    => round($item['faturamento_total'], 2),
            'faturamento_aprovado' => round($item['faturamento_aprovado'], 2),
            'fechado_em'           => $item['fechado_em'],
        ];
    }

    echo json_encode([
        'status'    => 'sucesso',
        'historico' => $historico,
        'total'     => count($historico),
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log('[historico_faturamento] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao carregar histórico']);
}
exit;