<?php
// =============================================
// historico_faturamento.php
// Agrupa faturamento por mês/ano
// Retorna: mes_ano, total_servicos, faturamento_total,
//          faturamento_aprovado, fechado_em
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não autorizado']);
    exit;
}

/**
 * Converte qualquer formato monetário para float.
 */
function valorParaFloat($valor): float {
    if (is_numeric($valor)) return (float)$valor;

    $valor = trim((string)$valor);
    $valor = str_replace(['R$', ' ', "\xc2\xa0"], '', $valor);

    $temVirgula = strpos($valor, ',') !== false;
    $temPonto   = strpos($valor, '.') !== false;

    if ($temVirgula && $temPonto) {
        $posV = strrpos($valor, ',');
        $posP = strrpos($valor, '.');
        if ($posV > $posP) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } else {
            $valor = str_replace(',', '', $valor);
        }
    } elseif ($temVirgula) {
        $valor = str_replace(',', '.', $valor);
    }
    return (float)$valor;
}

try {
    // Pega TODOS os serviços (para agrupar em PHP — mais confiável que SUM em VARCHAR)
    $stmt = $pdo->query("
        SELECT serdata_servico, servalor, serstatus_pagamento
        FROM servicos
        ORDER BY serdata_servico ASC
    ");
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupa por YYYY-MM
    $porMes = [];
    foreach ($todos as $s) {
        $data = $s['serdata_servico'] ?? null;
        if (!$data) continue;

        $timestamp = strtotime($data);
        if (!$timestamp) continue;

        $chave = date('Y-m', $timestamp);
        if (!isset($porMes[$chave])) {
            $porMes[$chave] = [
                'mes_ano' => $chave,
                'total_servicos' => 0,
                'faturamento_total' => 0.0,
                'faturamento_aprovado' => 0.0,
                'fechado_em' => $data
            ];
        }

        $valor = valorParaFloat($s['servalor'] ?? 0);
        $status = strtolower((string)($s['serstatus_pagamento'] ?? ''));

        $porMes[$chave]['total_servicos']++;
        $porMes[$chave]['faturamento_total'] += $valor;

        if (in_array($status, ['aprovado', 'pagar_no_local'], true)) {
            $porMes[$chave]['faturamento_aprovado'] += $valor;
        }
    }

    // Ordena do mês mais recente para o mais antigo
    krsort($porMes);

    // Formata mes_ano para exibição: "2026-09" → "Setembro / 2026"
    $mesesPt = [
        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
        '04' => 'Abril',   '05' => 'Maio',      '06' => 'Junho',
        '07' => 'Julho',   '08' => 'Agosto',    '09' => 'Setembro',
        '10' => 'Outubro', '11' => 'Novembro',  '12' => 'Dezembro'
    ];

    $historico = [];
    foreach ($porMes as $chave => $item) {
        [$ano, $mes] = explode('-', $chave);
        $historico[] = [
            'mes_ano' => ($mesesPt[$mes] ?? $mes) . ' / ' . $ano,
            'total_servicos' => $item['total_servicos'],
            'faturamento_total' => round($item['faturamento_total'], 2),
            'faturamento_aprovado' => round($item['faturamento_aprovado'], 2),
            'fechado_em' => $item['fechado_em']
        ];
    }

    echo json_encode([
        'status' => 'sucesso',
        'historico' => $historico,
        'total' => count($historico)
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Erro historico_faturamento: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro ao carregar histórico'
    ]);
}