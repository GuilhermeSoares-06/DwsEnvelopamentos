<?php
// =============================================
// verificar_sessao.php — Valida admin no banco
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

header('Content-Type: application/json; charset=utf-8');

// Sem sessão de admin?
if (empty($_SESSION['admin_id'])) {
    echo json_encode(['logado' => false, 'motivo' => 'sem_sessao']);
    exit;
}

// Timeout 2h
if (isset($_SESSION['admin_login_time'])
    && time() - (int) $_SESSION['admin_login_time'] > 7200) {
    unset(
        $_SESSION['admin_id'],
        $_SESSION['admin_nome'],
        $_SESSION['admin_logado'],
        $_SESSION['admin_login_time']
    );
    echo json_encode(['logado' => false, 'motivo' => 'timeout']);
    exit;
}

// Confirma no banco que ainda é funcionário
try {
    // O login (LoginADM.php) autentica pela tabela `funcionarios`
    $stmt = $pdo->prepare("
        SELECT id, nome
        FROM funcionarios
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => (int) $_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        unset(
            $_SESSION['admin_id'],
            $_SESSION['admin_nome'],
            $_SESSION['admin_logado'],
            $_SESSION['admin_login_time']
        );
        echo json_encode(['logado' => false, 'motivo' => 'excluido']);
        exit;
    }

    echo json_encode([
        'logado' => true,
        'nome'   => $admin['nome'],
        'id'     => (int) $admin['id'],
        'super_admin' => isSuperAdmin(),
    ]);
} catch (PDOException $e) {
    error_log('[verificar_sessao] ' . $e->getMessage());
    echo json_encode(['logado' => false, 'motivo' => 'erro']);
}
exit;