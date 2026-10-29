<?php
// =============================================
// config_mp.php — Configuração Mercado Pago
// Credenciais vêm de PHP/config/config.php
// =============================================

if (!function_exists('config')) {
    require_once __DIR__ . '/../Config/config_loader.php';
}

if (!defined('MP_AMBIENTE')) {
    define('MP_AMBIENTE', config('mp.ambiente', 'sandbox'));
}

$__mpToken = (string) config('mp.access_token', '');

if (empty($__mpToken) || $__mpToken === 'COLE_AQUI_O_NOVO_TOKEN') {
    error_log('[MP] access_token não configurado em PHP/config/config.php');

    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
    }
    die(json_encode([
        'status'   => 'erro',
        'mensagem' => 'Pagamento temporariamente indisponível.',
    ]));
}

define('MP_ACCESS_TOKEN', $__mpToken);
define('MP_WEBHOOK_SECRET', (string) config('mp.webhook_secret', ''));

// URL base
$__appUrl = (string) config('app.url', '');

if (empty($__appUrl)) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir   = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? ''))), '/');
    $__appUrl = $proto . '://' . $host . $dir;
}

define('SITE_URL', rtrim($__appUrl, '/'));

define('MP_URL_SUCESSO',  SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=success');
define('MP_URL_FALHA',    SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=failure');
define('MP_URL_PENDENTE', SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=pending');
define('MP_URL_WEBHOOK',  SITE_URL . '/PHP/pagamento/webhook.php');

unset($__mpToken, $__appUrl);

if (!function_exists('mpRequest')) {
    function mpRequest(string $method, string $endpoint, ?array $body = null): array
    {
        $ch = curl_init();
        $url = 'https://api.mercadopago.com' . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . MP_ACCESS_TOKEN,
        ];
        if ($method === 'POST') {
            $headers[] = 'X-Idempotency-Key: ' . bin2hex(random_bytes(16));
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['http_code' => 0, 'body' => null, 'error' => $error];
        }
        return [
            'http_code' => $httpCode,
            'body'      => json_decode($response, true),
            'raw'       => $response,
        ];
    }
}