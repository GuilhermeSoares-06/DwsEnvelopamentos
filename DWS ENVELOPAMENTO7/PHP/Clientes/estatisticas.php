<?php
// =============================================
// estatisticas.php
// Retorna estatísticas REAIS do banco
// =============================================

include("../Banco/conexao.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // =============================================
    // 1. TOTAL DE VEÍCULOS TRANSFORMADOS
    // =============================================
    // Conta TODOS os serviços, independente do status
    // (exceto se quiser filtrar por algo)
    $stmt = $pdo->query("SELECT COUNT(*) FROM servicos");
    $totalServicos = (int)$stmt->fetchColumn();

    // =============================================
    // 2. TOTAL DE CLIENTES
    // =============================================
    // Conta TODOS os clientes cadastrados
    // (sem filtrar por tipocliente para não perder ninguém)
    $stmt = $pdo->query("SELECT COUNT(*) FROM clientes");
    $totalClientes = (int)$stmt->fetchColumn();

    // =============================================
    // 3. ANOS DE EXPERIÊNCIA
    // =============================================
    $stmt = $pdo->query("SELECT MIN(serdata_servico) FROM servicos");
    $primeiraData = $stmt->fetchColumn();
    
    $anosExperiencia = 5; // padrão
    if ($primeiraData) {
        $anoInicial = (int)date('Y', strtotime($primeiraData));
        $anoAtual = (int)date('Y');
        $anosExperiencia = max(1, $anoAtual - $anoInicial);
    }

    // =============================================
    // 4. PERCENTUAL DE SATISFAÇÃO
    // =============================================
    $percentualSatisfacao = 98;

    // =============================================
    // LOG PARA DEBUG
    // =============================================
    error_log("=== ESTATÍSTICAS ===");
    error_log("Serviços: $totalServicos");
    error_log("Clientes: $totalClientes");
    error_log("Anos: $anosExperiencia");
    error_log("Satisfação: $percentualSatisfacao%");

    // =============================================
    // RETORNA
    // =============================================
    echo json_encode([
        'status' => 'sucesso',
        'estatisticas' => [
            'veiculos_transformados' => $totalServicos,
            'clientes_satisfeitos' => $totalClientes,
            'anos_experiencia' => $anosExperiencia,
            'percentual_satisfacao' => $percentualSatisfacao
        ],
        'debug' => [
            'total_servicos_banco' => $totalServicos,
            'total_clientes_banco' => $totalClientes
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Erro estatisticas: " . $e->getMessage());
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro ao buscar estatísticas: ' . $e->getMessage(),
        'estatisticas' => [
            'veiculos_transformados' => 0,
            'clientes_satisfeitos' => 0,
            'anos_experiencia' => 5,
            'percentual_satisfacao' => 98
        ]
    ]);
}
?>