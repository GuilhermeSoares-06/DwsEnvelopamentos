<?php
// =============================================
// security.php - Headers + Rate Limiting
// =============================================
// Incluir no topo de todo arquivo PHP que
// responde ao cliente.

// =============================================
// HEADERS DE SEGURANÇA
// =============================================

/**
 * Envia headers de segurança HTTP.
 * Chame no topo de qualquer página HTML/PHP.
 */
function enviarHeadersSeguranca(bool $isApi = false): void {
    // Impede que a página seja embutida em iframe (clickjacking)
    header('X-Frame-Options: SAMEORIGIN');

    // Impede MIME sniffing
    header('X-Content-Type-Options: nosniff');

    // Não vaza URL em referrer pra outros sites
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Restringe features do navegador
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // Cache para APIs (evita vazar dados)
    if ($isApi) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

// =============================================
// RATE LIMITING
// =============================================

/**
 * Verifica se um IP/chave está bloqueado por excesso de tentativas.
 * Retorna true se está bloqueado (e envia a resposta de erro).
 *
 * @param string $chave     Identificador (ex: 'login_cliente_'.$ip)
 * @param int    $max       Máximo de tentativas no período
 * @param int    $janelaSeg Janela em segundos (padrão 15 min)
 */
function verificarRateLimit(string $chave, int $max = 5, int $janelaSeg = 900): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $agora = time();
    $key = 'rl_' . md5($chave);

    $tentativas = $_SESSION[$key] ?? [];
    // Remove as antigas
    $tentativas = array_values(array_filter($tentativas, fn($t) => $agora - $t < $janelaSeg));
    $_SESSION[$key] = $tentativas;

    if (count($tentativas) >= $max) {
        $tempoRestante = $janelaSeg - ($agora - $tentativas[0]);
        $minutos = ceil($tempoRestante / 60);
        responderErro("Muitas tentativas. Aguarde $minutos minuto(s).", 429, [
            'retry_after' => $tempoRestante
        ]);
        return true;
    }
    return false;
}

/**
 * Registra uma tentativa (falha) na chave.
 */
function registrarTentativa(string $chave): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $key = 'rl_' . md5($chave);
    $_SESSION[$key][] = time();
}

/**
 * Limpa as tentativas de uma chave (após sucesso).
 */
function limparTentativas(string $chave): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $key = 'rl_' . md5($chave);
    unset($_SESSION[$key]);
}

// =============================================
// CSRF
// =============================================

function gerarTokenCSRF(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarTokenCSRF(?string $token): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token']) || empty($token)) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function campoCSRF(): string {
    return '<input type="hidden" name="csrf_token" value="' . gerarTokenCSRF() . '">';
}

// =============================================
// LOGIN DO CLIENTE / ADMIN
// =============================================

/**
 * Verifica se o cliente está logado. Se não, redireciona.
 */
function exigirLoginCliente(string $redirect = '../../telas/Cliente/loginClientes.html'): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['cliid'])) {
        if (isAjax()) {
            responderErro('Você precisa estar logado.', 401);
        }
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Verifica se o admin está logado. Se não, redireciona.
 */
function exigirLoginAdmin(string $redirect = '../../telas/ADM/login.html'): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_id'])) {
        if (isAjax()) {
            responderErro('Acesso restrito.', 401);
        }
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Detecta se é requisição AJAX/fetch.
 */
function isAjax(): bool {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (!empty($_SERVER['HTTP_ACCEPT'])
            && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}

// =============================================
// LOGS DE AUDITORIA
// =============================================

function logAuditoria(string $acao, array $contexto = []): void {
    $log = date('Y-m-d H:i:s') . " | " . $acao;
    if (!empty($contexto)) {
        $log .= " | " . json_encode($contexto, JSON_UNESCAPED_UNICODE);
    }
    error_log("[AUDIT] $log");
}