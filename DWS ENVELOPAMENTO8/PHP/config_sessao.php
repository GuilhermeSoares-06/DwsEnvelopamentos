<?php
// =============================================
// config_sessao.php — Sessão segura (endurecida)
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    if (!function_exists('config')) {
        require_once __DIR__ . '/Config/config_loader.php';
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? 80) == 443)
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'], true)
            || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false
            || strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false;

    session_name(config('app.session_name', 'DWSSESSID'));

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps && !$isLocal,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');

    session_start();

    // Regenera ID a cada 30 min
    if (empty($_SESSION['_regen'])) {
        $_SESSION['_regen'] = time();
    } elseif (time() - $_SESSION['_regen'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_regen'] = time();
    }

    // Timeout de sessão cliente (2h) — admin é validado em verificar_sessao.php
    if (isset($_SESSION['cliid'], $_SESSION['_login_time'])) {
        $lifetime = (int) config('app.session_lifetime', 7200);
        if (time() - (int) $_SESSION['_login_time'] > $lifetime) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();
            session_start();
        }
    }
}