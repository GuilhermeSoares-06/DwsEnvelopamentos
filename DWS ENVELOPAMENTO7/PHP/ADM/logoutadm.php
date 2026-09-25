<?php
// =============================================
// logoutadm.php - Logout do Administrador
// =============================================
session_start();

// Limpa variáveis
$_SESSION = array();

// Destroi cookie da sessão
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

header('Location: ../../telas/ADM/login.html');
exit;
?>