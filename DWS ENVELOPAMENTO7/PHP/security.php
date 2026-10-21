<?php
// =============================================
// security.php - Headers + Rate Limit + CSRF + Auth
// =============================================

function enviarHeadersSeguranca(bool $isApi = false): void {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    if ($isApi) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

function verificarRateLimit(string $chave, int $max = 5, int $janelaSeg = 900): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $agora = time();
    $key = 'rl_' . md5($chave);
    $tentativas = $_SESSION[$key] ?? [];
    $tentativas = array_values(array_filter($tentativas, fn($t) => $agora - $t < $janelaSeg));
    $_SESSION[$key] = $tentativas;
    if (count($tentativas) >= $max) {
        $tempoRestante = $janelaSeg - ($agora - $tentativas[0]);
        $minutos = (int)ceil($tempoRestante / 60);
        responderErro("Muitas tentativas. Aguarde $minutos minuto(s).", 429, ['retry_after' => $tempoRestante]);
        return true;
    }
    return false;
}

function registrarTentativa(string $chave): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['rl_' . md5($chave)][] = time();
}

function limparTentativas(string $chave): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    unset($_SESSION['rl_' . md5($chave)]);
}

function gerarTokenCSRF(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarTokenCSRF(?string $token): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token']) || empty($token)) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function campoCSRF(): string {
    return '<input type="hidden" name="csrf_token" value="' . gerarTokenCSRF() . '">';
}

function isAjax(): bool {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}

function exigirLoginCliente(string $redirect = '../../telas/Cliente/loginClientes.html'): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['cliid'])) {
        if (isAjax()) responderErro('Você precisa estar logado.', 401);
        header('Location: ' . $redirect);
        exit;
    }
}

function exigirLoginAdmin(string $redirect = '../../telas/ADM/login.html'): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_id'])) {
        if (isAjax()) responderErro('Acesso restrito.', 401);
        header('Location: ' . $redirect);
        exit;
    }
}

function logAuditoria(string $acao, array $contexto = []): void {
    $log = date('Y-m-d H:i:s') . " | " . $acao;
    if (!empty($contexto)) $log .= " | " . json_encode($contexto, JSON_UNESCAPED_UNICODE);
    error_log("[AUDIT] $log");
}