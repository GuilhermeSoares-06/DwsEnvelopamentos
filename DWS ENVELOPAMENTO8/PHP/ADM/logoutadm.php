<?php
// =============================================
// logoutadm.php - Logout do Administrador
// =============================================
require_once __DIR__ . '/../bootstrap.php';

// Apaga TODAS as chaves (admin e cliente) para sair 100%
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

session_destroy();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

header('Location: ../../telas/ADM/login.html?saiu=1');
exit;