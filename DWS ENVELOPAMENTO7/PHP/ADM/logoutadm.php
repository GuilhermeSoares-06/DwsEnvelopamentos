<?php
// =============================================
// logoutadm.php - Logout do Administrador
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Apaga SÓ as chaves do admin (não mexe no cliente)
unset(
    $_SESSION['admin_id'],
    $_SESSION['admin_nome'],
    $_SESSION['admin_logado'],
    $_SESSION['admin_login_time'],
    $_SESSION['login_tentativas']
);

// Se NÃO há cliente logado junto, destrói a sessão inteira
if (empty($_SESSION['cliid'])) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

// 🚫 Anti-cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

header('Location: ../../telas/ADM/login.html?saiu=1&t=' . time());
exit;