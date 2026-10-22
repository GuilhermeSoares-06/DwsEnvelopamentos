<?php
// =============================================
// verificar_sessao.php - Valida admin no banco
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Timeout de 2h
if (isset($_SESSION['admin_login_time']) && time() - $_SESSION['admin_login_time'] > 7200) {
    session_destroy();
    echo json_encode(['logado' => false, 'motivo' => 'timeout']);
    exit;
}

if (empty($_SESSION['admin_id'])) {
    echo json_encode(['logado' => false, 'motivo' => 'sem_sessao']);
    exit;
}

require_once __DIR__ . '/../Banco/conexao.php';

try {
    $stmt = $pdo->prepare("
        SELECT cliid, clinome FROM clientes 
        WHERE cliid = :id AND tipocliente = 'funcionario' 
        LIMIT 1
    ");
    $stmt->execute([':id' => (int)$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        session_destroy();
        echo json_encode(['logado' => false, 'motivo' => 'excluido']);
        exit;
    }

    echo json_encode([
        'logado' => true,
        'nome'   => $admin['clinome'],
        'id'     => (int)$admin['cliid']
    ]);
} catch (PDOException $e) {
    error_log("Erro verificar_sessao: " . $e->getMessage());
    echo json_encode(['logado' => false, 'motivo' => 'erro']);
}
exit;