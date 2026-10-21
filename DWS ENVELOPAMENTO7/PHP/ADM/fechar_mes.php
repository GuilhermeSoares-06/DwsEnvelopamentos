<?php
// =============================================
// fechar_mes.php - Fecha o mês anterior
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    responderErro('Acesso restrito.', 401);
}

try {
    // Data atual
    $anoAtual = (int)date('Y');
    $mesAtual = (int)date('n');

    // Busca o último fechamento salvo
    $stmt = $pdo->query("
        SELECT ano, mes FROM faturamento_mensal 
        ORDER BY ano DESC, mes DESC LIMIT 1
    ");
    $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se nunca fechou nada, cria um ponto de partida com o mês anterior
    if (!$ultimo) {
        // Ex: se agora é setembro/2026, fecha agosto/2026
        $mesAnterior = $mesAtual - 1;
        $anoAnterior = $anoAtual;
        if ($mesAnterior < 1) {
            $mesAnterior = 12;
            $anoAnterior = $anoAtual - 1;
        }

        fecharMes($pdo, $anoAnterior, $mesAnterior);
        responderSucesso('Primeiro fechamento criado.', [
            'ano' => $anoAnterior,
            'mes' => $mesAnterior
        ]);
        exit;
    }

    $anoUltimo = (int)$ultimo['ano'];
    $mesUltimo = (int)$ultimo['mes'];

    // Se o último fechamento JÁ é o mês anterior ao atual → nada a fazer
    $mesAnteriorEsperado = $mesAtual - 1;
    $anoAnteriorEsperado = $anoAtual;
    if ($mesAnteriorEsperado < 1) {
        $mesAnteriorEsperado = 12;
        $anoAnteriorEsperado = $anoAtual - 1;
    }

    if ($anoUltimo === $anoAnteriorEsperado && $mesUltimo === $mesAnteriorEsperado) {
        responderSucesso('Mês atualizado.', ['nada_a_fazer' => true]);
        exit;
    }

    // Precisa fechar todos os meses que faltam entre o último e o mês anterior
    $fechados = [];
    $cursorAno = $anoUltimo;
    $cursorMes = $mesUltimo;

    while (true) {
        // Avança 1 mês
        $cursorMes++;
        if ($cursorMes > 12) {
            $cursorMes = 1;
            $cursorAno++;
        }

        // Se chegou no mês atual, para
        if ($cursorAno === $anoAtual && $cursorMes === $mesAtual) {
            break;
        }

        fecharMes($pdo, $cursorAno, $cursorMes);
        $fechados[] = sprintf('%02d/%d', $cursorMes, $cursorAno);

        // Proteção: máximo 24 meses de uma vez
        if (count($fechados) >= 24) break;
    }

    responderSucesso('Fechamentos processados.', [
        'meses_fechados' => $fechados
    ]);

} catch (PDOException $e) {
    error_log("Erro fechar_mes: " . $e->getMessage());
    responderErro('Erro ao fechar mês.', 500);
}

/**
 * Fecha um mês específico — calcula total e salva
 */
function fecharMes(PDO $pdo, int $ano, int $mes): void {
    // Verifica se já existe
    $stmt = $pdo->prepare("
        SELECT id FROM faturamento_mensal WHERE ano = :a AND mes = :m LIMIT 1
    ");
    $stmt->execute([':a' => $ano, ':m' => $mes]);
    if ($stmt->fetch()) return; // já existe

    // Busca estatísticas do mês
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total,
            COALESCE(SUM(CAST(REPLACE(REPLACE(servalor, '.', ''), ',', '.') AS DECIMAL(10,2))), 0) AS soma_total,
            COALESCE(SUM(
                CASE 
                    WHEN serstatus_pagamento = 'aprovado' 
                    THEN CAST(REPLACE(REPLACE(servalor, '.', ''), ',', '.') AS DECIMAL(10,2))
                    ELSE 0
                END
            ), 0) AS soma_aprovado
        FROM servicos
        WHERE YEAR(serdata_servico) = :ano AND MONTH(serdata_servico) = :mes
    ");
    $stmt->execute([':ano' => $ano, ':mes' => $mes]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Insere na tabela
    $stmt = $pdo->prepare("
        INSERT INTO faturamento_mensal 
            (ano, mes, total_servicos, faturamento_total, faturamento_aprovado)
        VALUES (:a, :m, :t, :ft, :fa)
    ");
    $stmt->execute([
        ':a'  => $ano,
        ':m'  => $mes,
        ':t'  => (int)($stats['total'] ?? 0),
        ':ft' => (float)($stats['soma_total'] ?? 0),
        ':fa' => (float)($stats['soma_aprovado'] ?? 0),
    ]);
}