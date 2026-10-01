<?php
// =============================================
// config_mp.php - Configurações do Mercado Pago
// =============================================

// ⚠️ MODO PRODUÇÃO - PAGAMENTOS REAIS!
define('MP_AMBIENTE', 'production');

// =============================================
// CREDENCIAIS DO MERCADO PAGO (PRODUÇÃO)
// =============================================
define('MP_ACCESS_TOKEN_PROD', 'APP_USR-2060307338411316-092213-168f53bd440ffcdd540517078211006e-3709463674');
define('MP_ACCESS_TOKEN_TEST', ''); // Não usado em produção

define('MP_ACCESS_TOKEN', MP_AMBIENTE === 'production' ? MP_ACCESS_TOKEN_PROD : MP_ACCESS_TOKEN_TEST);

// =============================================
// URL BASE DO SITE
// =============================================
// ⚠️ ATENÇÃO: Como você está testando no LOCALHOST,
// o Mercado Pago NÃO consegue redirecionar pra cá.
// 
// OPÇÕES:
// 1) Se está testando localmente: use um túnel (ngrok) e coloque a URL do túnel
// 2) Se já tem domínio: coloque a URL real
// 
// Por enquanto, mantenha o localhost para os links internos
// funcionarem. O MP vai avisar sobre back_urls mas pode funcionar.

// Detecta automaticamente se está em localhost
$isLocalhost = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:80'])
    || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false
    || strpos($_SERVER['HTTP_HOST'] ?? '', '.test') !== false
    || strpos($_SERVER['HTTP_HOST'] ?? '', '.local') !== false;

if ($isLocalhost) {
    // Detecta protocolo
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Pega o caminho base (até /telas/)
    $caminhoAtual = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')));
    $baseUrl = $protocolo . '://' . $host . $caminhoAtual;
    define('SITE_URL', $baseUrl);
} else {
    // Em produção: COLOQUE SEU DOMÍNIO REAL AQUI
    define('SITE_URL', 'https://seudominio.com.br');
}

// =============================================
// URLs DE RETORNO
// =============================================
define('MP_URL_SUCESSO',  SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=success');
define('MP_URL_FALHA',    SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=failure');
define('MP_URL_PENDENTE', SITE_URL . '/telas/Cliente/pagamento_retorno.html?status=pending');

// URL do webhook (o Mercado Pago chama de servidor pra servidor)
define('MP_URL_WEBHOOK', SITE_URL . '/PHP/Pagamento/webhook.php');

// =============================================
// FUNÇÃO DE REQUISIÇÃO À API DO MERCADO PAGO
// =============================================
function mpRequest($method, $endpoint, $body = null) {
    $ch = curl_init();
    $url = "https://api.mercadopago.com" . $endpoint;

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . MP_ACCESS_TOKEN
    ];

    if ($method === 'POST') {
        $headers[] = 'X-Idempotency-Key: ' . uniqid('mp_', true);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("Erro cURL Mercado Pago: $error");
        return ['http_code' => 0, 'body' => null, 'error' => $error];
    }

    return [
        'http_code' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

// Log da configuração atual (útil para debug)
error_log("=== CONFIG MP CARREGADA ===");
error_log("Ambiente: " . MP_AMBIENTE);
error_log("SITE_URL: " . SITE_URL);
error_log("Webhook: " . MP_URL_WEBHOOK);
error_log("Token (primeiros 20 chars): " . substr(MP_ACCESS_TOKEN, 0, 20) . "...");
?>