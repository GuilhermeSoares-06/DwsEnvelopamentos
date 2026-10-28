<?php
// =============================================
// security.php — Headers, Rate Limit, CSRF, Auth
// =============================================

if (!function_exists('enviarHeadersSeguranca')) {
    function enviarHeadersSeguranca(bool $isApi = false): void
    {
        if (headers_sent()) return;

        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Cross-Origin-Resource-Policy: same-origin');

        if ($isApi) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
        }

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
        if ($isHttps && defined('APP_ENV') && APP_ENV === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}

// ---------------- IP ----------------
if (!function_exists('ipCliente')) {
    // Usa só REMOTE_ADDR: X-Forwarded-For pode ser forjado pelo cliente
    function ipCliente(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }
}

// ---------------- RATE LIMIT ----------------
// Guarda as tentativas na tabela `tentativas_login` (apagar o cookie não zera).
// Se a tabela não existir, cai para a sessão.
if (!function_exists('rateLimitPdo')) {
    function rateLimitPdo(): ?PDO
    {
        global $pdo;
        static $ok = null;
        if (!($pdo instanceof PDO)) return null;
        if ($ok === null) {
            try {
                $pdo->query("SELECT 1 FROM tentativas_login LIMIT 1");
                $ok = true;
            } catch (PDOException $e) {
                error_log('[RATE-LIMIT] tabela tentativas_login ausente — usando sessão');
                $ok = false;
            }
        }
        return $ok ? $pdo : null;
    }
}

if (!function_exists('verificarRateLimit')) {
    function verificarRateLimit(string $chave, int $max = 5, int $janelaSeg = 900): bool
    {
        $agora = time();
        $db = rateLimitPdo();

        if ($db) {
            $db->prepare("DELETE FROM tentativas_login WHERE criado_em < (NOW() - INTERVAL :j SECOND)")
               ->execute([':j' => $janelaSeg]);
            $stmt = $db->prepare("
                SELECT COUNT(*) AS total, UNIX_TIMESTAMP(MIN(criado_em)) AS primeira
                FROM tentativas_login WHERE chave = :c
            ");
            $stmt->execute([':c' => md5($chave)]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = (int) ($row['total'] ?? 0);
            $primeira = (int) ($row['primeira'] ?? $agora);
        } else {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $key = 'rl_' . md5($chave);
            $tentativas = array_values(array_filter($_SESSION[$key] ?? [], fn($t) => $agora - $t < $janelaSeg));
            $_SESSION[$key] = $tentativas;
            $total = count($tentativas);
            $primeira = $tentativas[0] ?? $agora;
        }

        if ($total >= $max) {
            $restante = max(60, $janelaSeg - ($agora - $primeira));
            $min = (int) ceil($restante / 60);
            responderErro("Muitas tentativas. Aguarde {$min} minuto(s).", 429, ['retry_after' => $restante]);
            return true;
        }
        return false;
    }
}

if (!function_exists('registrarTentativa')) {
    function registrarTentativa(string $chave): void
    {
        $db = rateLimitPdo();
        if ($db) {
            $db->prepare("INSERT INTO tentativas_login (chave, criado_em) VALUES (:c, NOW())")
               ->execute([':c' => md5($chave)]);
            return;
        }
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['rl_' . md5($chave)][] = time();
    }
}

if (!function_exists('limparTentativas')) {
    function limparTentativas(string $chave): void
    {
        $db = rateLimitPdo();
        if ($db) {
            $db->prepare("DELETE FROM tentativas_login WHERE chave = :c")->execute([':c' => md5($chave)]);
            return;
        }
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['rl_' . md5($chave)]);
    }
}

// ---------------- CSRF ----------------
if (!function_exists('gerarTokenCSRF')) {
    function gerarTokenCSRF(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validarTokenCSRF')) {
    function validarTokenCSRF(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token']) || empty($token)) return false;
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('campoCSRF')) {
    function campoCSRF(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . gerarTokenCSRF() . '">';
    }
}

if (!function_exists('exigirCSRF')) {
    function exigirCSRF(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!validarTokenCSRF($token)) {
            responderErro('Sessão expirada ou requisição inválida. Recarregue a página.', 419);
        }
    }
}

// ---------------- AUTH ----------------
if (!function_exists('isAjax')) {
    function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (!empty($_SERVER['HTTP_ACCEPT'])
                && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}

if (!function_exists('exigirLoginCliente')) {
    function exigirLoginCliente(string $redirect = '../../telas/Cliente/loginClientes.html'): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['cliid'])) {
            if (isAjax()) responderErro('Você precisa estar logado.', 401);
            header('Location: ' . $redirect);
            exit;
        }
    }
}

if (!function_exists('exigirLoginAdmin')) {
    function exigirLoginAdmin(string $redirect = '../../telas/ADM/login.html'): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['admin_id'])) {
            if (isAjax()) responderErro('Acesso restrito.', 401);
            header('Location: ' . $redirect);
            exit;
        }

        if (isset($_SESSION['admin_login_time'])
            && time() - (int) $_SESSION['admin_login_time'] > 7200) {
            unset($_SESSION['admin_id'], $_SESSION['admin_nome'],
                  $_SESSION['admin_logado'], $_SESSION['admin_login_time']);
            if (isAjax()) responderErro('Sessão expirada. Faça login novamente.', 401);
            header('Location: ' . $redirect);
            exit;
        }
    }
}

if (!function_exists('exigirAdminApi')) {
    // Versão JSON de exigirLoginAdmin(), para endpoints chamados via fetch
    function exigirAdminApi(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['admin_id'])) responderErro('Não autorizado.', 401);
        if (isset($_SESSION['admin_login_time'])
            && time() - (int) $_SESSION['admin_login_time'] > 7200) {
            unset($_SESSION['admin_id'], $_SESSION['admin_nome'],
                  $_SESSION['admin_logado'], $_SESSION['admin_login_time']);
            responderErro('Sessão expirada. Faça login novamente.', 401);
        }
    }
}

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin(): bool
    {
        $ids = array_map('intval', (array) config('admin.super_admin_ids', []));
        return in_array((int) ($_SESSION['admin_id'] ?? 0), $ids, true);
    }
}

// ---------------- AUDITORIA ----------------
if (!function_exists('logAuditoria')) {
    function logAuditoria(string $acao, array $contexto = []): void
    {
        $ip = ipCliente();
        $log = date('Y-m-d H:i:s') . " | ip={$ip} | " . $acao;
        if (!empty($contexto)) {
            $log .= " | " . json_encode($contexto, JSON_UNESCAPED_UNICODE);
        }
        error_log("[AUDIT] $log");
    }
}