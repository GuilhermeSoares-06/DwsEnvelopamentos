<?php
// =============================================
// verificar_sessao.php - Verifica sessão do admin
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Timeout de 2 horas
if (isset($_SESSION['admin_login_time']) && time() - $_SESSION['admin_login_time'] > 7200) {
    session_destroy();
    echo json_encode(['logado' => false, 'motivo' => 'timeout']);
    exit;
}

if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_nome'])) {
    echo json_encode([
        'logado' => true,
        'nome' => $_SESSION['admin_nome'],
        'id' => $_SESSION['admin_id']
    ]);
} else {
    echo json_encode(['logado' => false]);
}
exit;