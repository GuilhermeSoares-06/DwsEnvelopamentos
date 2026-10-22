<?php
// =============================================
// config_sessao.php - Sessão segura
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
              || ($_SERVER['SERVER_PORT'] ?? 80) == 443;

    // Detecta se está em localhost (XAMPP não tem HTTPS)
    $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'])
            || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false;

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure && !$isLocal,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();

    // Regenera ID a cada 30 min (anti session fixation)
    if (!isset($_SESSION['ultima_regeneracao'])) {
        $_SESSION['ultima_regeneracao'] = time();
    } elseif (time() - $_SESSION['ultima_regeneracao'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['ultima_regeneracao'] = time();
    }

    // Timeout de sessão cliente (2h)
    if (isset($_SESSION['cliid']) && isset($_SESSION['_login_time'])) {
        if (time() - $_SESSION['_login_time'] > 7200) {
            $_SESSION = [];
            session_destroy();
            session_start();
        }
    }
}