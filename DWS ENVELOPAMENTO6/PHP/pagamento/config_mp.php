<?php
// =============================================
// config_mp.php - Configurações do Mercado Pago
// =============================================

// Ambiente: 'test' (sandbox) ou 'production'
define('MP_AMBIENTE', 'test');

// Pegue em: https://www.mercadopago.com.br/developers/panel/app
// (Credenciais de teste e de produção são diferentes)
define('MP_ACCESS_TOKEN_TEST', 'TEST-0000000000000000-000000-00000000000000000000000000000000-000000000');
define('MP_ACCESS_TOKEN_PROD', 'APP_USR-0000000000000000-000000-00000000000000000000000000000000-000000000');

define('MP_ACCESS_TOKEN', MP_AMBIENTE === 'production' ? MP_ACCESS_TOKEN_PROD : MP_ACCESS_TOKEN_TEST);

// URL base do site, SEM barra no final
define('SITE_URL', 'https://seudominio.com.br');

// Para onde o Mercado Pago manda o cliente de volta após pagar
define('MP_URL_SUCESSO',  SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=success');
define('MP_URL_FALHA',    SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=failure');
define('MP_URL_PENDENTE', SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=pending');

// URL que o Mercado Pago chama de servidor pra servidor (é a que importa de verdade)
define('MP_URL_WEBHOOK', SITE_URL . '/PHP/Pagamento/webhook.php');

/**
 * Faz uma chamada autenticada à API do Mercado Pago.
 */
function mpRequest($method, $endpoint, $body = null) {
    $ch = curl_init();
    $url = "https://api.mercadopago.com" . $endpoint;

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . MP_ACCESS_TOKEN
    ];

    if ($method === 'POST') {
        // Evita duplicar cobrança se a requisição for reenviada
        $headers[] = 'X-Idempotency-Key: ' . uniqid('mp_', true);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("Erro cURL Mercado Pago: $error");
        return ['http_code' => 0, 'body' => null];
    }

    return [
        'http_code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}