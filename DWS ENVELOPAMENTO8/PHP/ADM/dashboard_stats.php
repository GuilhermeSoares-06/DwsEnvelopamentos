<?php
// =============================================
// dashboard_stats.php — Estatísticas do admin
// M0: devolve TODOS os campos usados pelas telas do painel
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

if (isset($_SESSION['admin_login_time']) && time() - $_SESSION['admin_login_time'] > 7200) {
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Sessão expirada']);
    exit;
}

/**
 * Converte QUALQUER formato monetário para float.
 * Aceita: "800", "800,00", "800.00", "1.500,00", "1,500.00", "R$ 800,00"
 */
function valorParaFloat($valor): float {
    if (is_numeric($valor)) return (float)$valor;

    $valor = trim((string)$valor);
    $valor = str_replace(['R$', ' ', "\xc2\xa0"], '', $valor);

    $temVirgula = strpos($valor, ',') !== false;
    $temPonto   = strpos($valor, '.') !== false;

    if ($temVirgula && $temPonto) {
        $posVirgula = strrpos($valor, ',');
        $posPonto   = strrpos($valor, '.');
        if ($posVirgula > $posPonto) {
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
    // =============================================
    // 1. CONTAGENS
    // =============================================
    $totalServicos = (int)$pdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();

    $totalClientes = (int)$pdo->query("
        SELECT COUNT(*) FROM clientes
        WHERE tipocliente = 'cliente' OR tipocliente IS NULL
    ")->fetchColumn();

    // Serviços finalizados (card "Serviços Finalizados")
    $servicosFinalizados = (int)$pdo->query("
        SELECT COUNT(*) FROM servicos
        WHERE serstatus_servico = 'finalizado'
    ")->fetchColumn();

    // Serviços pendentes (não finalizados)
    $servicosPendentes = (int)$pdo->query("
        SELECT COUNT(*) FROM servicos
        WHERE serstatus_servico <> 'finalizado' OR serstatus_servico IS NULL
    ")->fetchColumn();

    // Serviços no mês atual (por data do serviço)
    $servicosMes = (int)$pdo->query("
        SELECT COUNT(*) FROM servicos
        WHERE MONTH(serdata_servico) = MONTH(CURRENT_DATE())
        AND YEAR(serdata_servico) = YEAR(CURRENT_DATE())
    ")->fetchColumn();

    // Serviços no mês anterior
    $servicosMesAnterior = (int)$pdo->query("
        SELECT COUNT(*) FROM servicos
        WHERE MONTH(serdata_servico) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
        AND YEAR(serdata_servico) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
    ")->fetchColumn();

    // =============================================
    // 2. FATURAMENTO (conversão robusta em PHP)
    // =============================================

    // Total geral (todos os status)
    $faturamentoTotal = 0.0;
    $valores = $pdo->query("SELECT servalor FROM servicos")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($valores as $v) {
        $faturamentoTotal += valorParaFloat($v);
    }

    // Aprovados (pago online ou pago no local)
    $faturamentoAprovado = 0.0;
    $valoresAprov = $pdo->query("
        SELECT servalor FROM servicos
        WHERE serstatus_pagamento IN ('aprovado', 'pagar_no_local')
    ")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($valoresAprov as $v) {
        $faturamentoAprovado += valorParaFloat($v);
    }

    // Mês atual (todos os status)
    $faturamentoMes = 0.0;
    $valoresMes = $pdo->query("
        SELECT servalor FROM servicos
        WHERE MONTH(serdata_servico) = MONTH(CURRENT_DATE())
        AND YEAR(serdata_servico) = YEAR(CURRENT_DATE())
    ")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($valoresMes as $v) {
        $faturamentoMes += valorParaFloat($v);
    }

    // Mês anterior
    $faturamentoAnterior = 0.0;
    $valoresAnt = $pdo->query("
        SELECT servalor FROM servicos
        WHERE MONTH(serdata_servico) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
        AND YEAR(serdata_servico) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
    ")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($valoresAnt as $v) {
        $faturamentoAnterior += valorParaFloat($v);
    }

    // Ticket médio
    $ticketMedio = $totalServicos > 0 ? ($faturamentoTotal / $totalServicos) : 0.0;

    // =============================================
    // 3. RETORNA — todos os nomes possíveis, pra
    //    qualquer tela encontrar o que espera
    // =============================================
    echo json_encode([
        'success' => true,

        // Contagens
        'total_servicos'         => $totalServicos,
        'total_clientes'         => $totalClientes,
        'servicos_finalizados'   => $servicosFinalizados,
        'finalizados'            => $servicosFinalizados,      // alias
        'servicos_pendentes'     => $servicosPendentes,
        'pendentes'              => $servicosPendentes,        // alias
        'servicos_mes'           => $servicosMes,
        'servicos_mes_anterior'  => $servicosMesAnterior,

        // Faturamento
        'faturamento_total'      => round($faturamentoTotal, 2),
        'faturamento_aprovado'   => round($faturamentoAprovado, 2),
        'faturamento_mes'        => round($faturamentoMes, 2),
        'faturamento_anterior'   => round($faturamentoAnterior, 2),
        'ticket_medio'           => round($ticketMedio, 2),

        // Aliases antigos (compatibilidade com telas que possam usar)
        'faturamento_mes_atual'  => round($faturamentoMes, 2)
    ]);

} catch (PDOException $e) {
    error_log("Erro dashboard_stats: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno']);
}