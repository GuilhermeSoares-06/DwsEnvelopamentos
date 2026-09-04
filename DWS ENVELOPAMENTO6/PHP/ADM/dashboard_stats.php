<?php
require_once __DIR__ . '/../Banco/conexao.php';

session_start();

// VERIFICAR SE O ADMIN ESTÁ LOGADO
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Não autorizado - Sessão não iniciada']);
    exit;
}

try {
    // Total de serviços
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM servicos");
    $totalServicos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de clientes (apenas clientes, não funcionários)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clientes WHERE tipocliente = 'cliente' OR tipocliente IS NULL");
    $totalClientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Serviços do mês atual
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM servicos 
                         WHERE MONTH(serdata_servico) = MONTH(CURRENT_DATE()) 
                         AND YEAR(serdata_servico) = YEAR(CURRENT_DATE())");
    $servicosMes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Serviços do mês passado
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM servicos 
                         WHERE MONTH(serdata_servico) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) 
                         AND YEAR(serdata_servico) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
    $servicosMesAnterior = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        'success' => true,
        'total_servicos' => (int)$totalServicos,
        'total_clientes' => (int)$totalClientes,
        'servicos_mes' => (int)$servicosMes,
        'servicos_mes_anterior' => (int)$servicosMesAnterior
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>