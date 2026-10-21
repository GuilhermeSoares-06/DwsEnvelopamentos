<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/config_mp.php';

header('Content-Type: application/json; charset=utf-8');

function validarAssinaturaMP(): bool {
    $secret = MP_WEBHOOK_SECRET;
    if (empty($secret)) {
        error_log('⚠️ MP_WEBHOOK_SECRET não configurado — webhook sem validação');
        return true;
    }
    $xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    $xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';
    $dataId     = $_GET['data_id'] ?? $_GET['id'] ?? '';
    if (empty($xSignature) || empty($xRequestId) || empty($dataId)) return false;

    $parts = [];
    foreach (explode(',', $xSignature) as $part) {
        [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
        $parts[trim($k)] = trim($v);
    }
    $ts = $parts['ts'] ?? '';
    $v1 = $parts['v1'] ?? '';
    if (empty($ts) || empty($v1)) return false;

    $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
    $expected = hash_hmac('sha256', $manifest, $secret);
    return hash_equals($expected, $v1);
}

if (!validarAssinaturaMP()) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Assinatura inválida']);
    exit;
}

$topic = $_GET['topic'] ?? $_GET['type'] ?? null;
$id    = $_GET['id'] ?? ($_GET['data_id'] ?? null);

if (!$topic || !$id) {
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);
    $topic = $payload['type'] ?? $topic;
    $id = $payload['data']['id'] ?? $id;
}

if ($topic !== 'payment' || empty($id)) {
    http_response_code(200);
    echo json_encode(['status' => 'ignorado']);
    exit;
}

$resposta = mpRequest('GET', "/v1/payments/$id");
if ($resposta['http_code'] !== 200 || empty($resposta['body'])) {
    http_response_code(200);
    echo json_encode(['status' => 'erro']);
    exit;
}

$pagamento = $resposta['body'];
$serid     = (int)($pagamento['external_reference'] ?? 0);
$statusMP  = $pagamento['status'] ?? '';
$metodo    = $pagamento['payment_type_id'] ?? '';

if ($serid <= 0) {
    http_response_code(200);
    echo json_encode(['status' => 'ignorado']);
    exit;
}

$mapaStatus = [
    'approved' => 'aprovado', 'pending' => 'pendente', 'in_process' => 'pendente',
    'rejected' => 'rejeitado', 'cancelled' => 'cancelado',
    'refunded' => 'estornado', 'charged_back' => 'estornado'
];
$statusLocal = $mapaStatus[$statusMP] ?? 'pendente';

try {
    $stmt = $pdo->prepare("
        UPDATE servicos
        SET serstatus_pagamento = :status,
            sermp_payment_id = :payment_id,
            sermp_metodo_pagamento = :metodo,
            serdata_pagamento = NOW()
        WHERE serid = :serid
    ");
    $stmt->execute([
        ':status' => $statusLocal,
        ':payment_id' => $id,
        ':metodo' => $metodo,
        ':serid' => $serid
    ]);
} catch (PDOException $e) {
    error_log("Erro PDO no webhook: " . $e->getMessage());
}

http_response_code(200);
echo json_encode(['status' => 'ok']);