<?php
require_once __DIR__ . '/../Banco/conexao.php';

session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

try {
    // Buscar últimos serviços com dados do cliente
    $stmt = $pdo->prepare("
        SELECT 
            s.serid,
            s.tipo_servico,
            s.serdescricao,
            s.servalor,
            s.serdata_servico,
            c.clinome,
            CASE 
                WHEN s.serdata_servico >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'concluido'
                WHEN s.serdata_servico >= DATE_SUB(NOW(), INTERVAL 14 DAY) THEN 'pendente'
                ELSE 'cancelado'
            END as status
        FROM servicos s
        LEFT JOIN clientes c ON s.cliid = c.cliid
        ORDER BY s.serdata_servico DESC
        LIMIT 5
    ");
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'servicos' => $servicos
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>