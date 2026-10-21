<?php
define('MP_AMBIENTE', getenv('MP_AMBIENTE') ?: 'sandbox');

$mpToken = getenv('MP_ACCESS_TOKEN');
if (!$mpToken) {
    if (in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'], true)) {
        $mpToken = 'APP_USR-2060307338411316-092213-168f53bd440ffcdd540517078211006e-3709463674';
    } else {
        error_log('❌ MP_ACCESS_TOKEN não configurado!');
        die(json_encode(['status' => 'erro', 'mensagem' => 'Pagamento indisponível.']));
    }
}
define('MP_ACCESS_TOKEN', $mpToken);
define('MP_WEBHOOK_SECRET', getenv('MP_WEBHOOK_SECRET') ?: '');

$isLocalhost = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:80'])
    || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false;

if ($isLocalhost) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $caminhoAtual = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')));
    define('SITE_URL', $protocolo . '://' . $host . $caminhoAtual);
} else {
    define('SITE_URL', getenv('SITE_URL') ?: 'https://seudominio.com.br');
}

define('MP_URL_SUCESSO',  SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=success');
define('MP_URL_FALHA',    SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=failure');
define('MP_URL_PENDENTE', SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=pending');
define('MP_URL_WEBHOOK',  SITE_URL . '/PHP/Pagamento/webhook.php');

function mpRequest($method, $endpoint, $body = null) {
    $ch = curl_init();
    $url = "https://api.mercadopago.com" . $endpoint;
    $headers = ['Content-Type: application/json', 'Authorization: Bearer ' . MP_ACCESS_TOKEN];
    if ($method === 'POST') $headers[] = 'X-Idempotency-Key: ' . uniqid('mp_', true);
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error) return ['http_code' => 0, 'body' => null, 'error' => $error];
    return ['http_code' => $httpCode, 'body' => json_decode($response, true), 'raw' => $response];
}