<?php
// =============================================
// webhook.php — Notificações do Mercado Pago
// Validação HMAC-SHA256 obrigatória em produção
// =============================================
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/config_mp.php';

header('Content-Type: application/json; charset=utf-8');

if (!function_exists('validarAssinaturaMP')) {
    function validarAssinaturaMP(): bool
    {
        $secret = MP_WEBHOOK_SECRET;

        if (empty($secret)) {
            if (APP_ENV === 'production') {
                error_log('[MP-WEBHOOK] MP_WEBHOOK_SECRET vazio em produção — rejeitado');
                return false;
            }
            error_log('[MP-WEBHOOK] ⚠ secret vazio — aceitando em dev');
            return true;
        }

        $xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        $xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';
        $dataId     = $_GET['data_id'] ?? $_GET['id'] ?? '';

        if ($xSignature === '' || $xRequestId === '' || $dataId === '') return false;

        $parts = [];
        foreach (explode(',', $xSignature) as $part) {
            [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
            $parts[trim($k)] = trim($v);
        }
        $ts = $parts['ts'] ?? '';
        $v1 = $parts['v1'] ?? '';
        if ($ts === '' || $v1 === '') return false;

        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
        $expected = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $v1);
    }
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

if ($topic !== 'payment' || empty($id) || !ctype_digit((string) $id)) {
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
$serid     = (int) ($pagamento['external_reference'] ?? 0);
$statusMP  = $pagamento['status'] ?? '';
$metodo    = $pagamento['payment_type_id'] ?? '';

if ($serid <= 0) {
    http_response_code(200);
    echo json_encode(['status' => 'ignorado']);
    exit;
}

$mapaStatus = [
    'approved'     => 'aprovado',
    'pending'      => 'pendente',
    'in_process'   => 'pendente',
    'authorized'   => 'pendente',
    'rejected'     => 'rejeitado',
    'cancelled'    => 'cancelado',
    'refunded'     => 'estornado',
    'charged_back' => 'estornado',
];
$statusLocal = $mapaStatus[$statusMP] ?? 'pendente';

try {
    $stmt = $pdo->prepare("SELECT servalor, serstatus_pagamento FROM servicos WHERE serid = :serid LIMIT 1");
    $stmt->execute([':serid' => $serid]);
    $atual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$atual) {
        http_response_code(200);
        echo json_encode(['status' => 'ignorado']);
        exit;
    }

    // Valor pago tem que bater com o valor do agendamento
    $valorPago = (float) ($pagamento['transaction_amount'] ?? 0);
    if ($statusLocal === 'aprovado' && abs($valorPago - (float) $atual['servalor']) > 0.01) {
        error_log("[MP-WEBHOOK] Valor divergente serid={$serid} pago={$valorPago} esperado={$atual['servalor']}");
        logAuditoria('webhook_mp_valor_divergente', ['serid' => $serid, 'mp_id' => $id, 'pago' => $valorPago]);
        http_response_code(200);
        echo json_encode(['status' => 'valor_divergente']);
        exit;
    }

    // Notificações podem chegar fora de ordem: um "pendente" atrasado
    // não pode rebaixar um pagamento já aprovado (só estorno/cancelamento pode)
    if ($atual['serstatus_pagamento'] === 'aprovado'
        && !in_array($statusLocal, ['aprovado', 'estornado', 'cancelado'], true)) {
        http_response_code(200);
        echo json_encode(['status' => 'ignorado']);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE servicos
        SET serstatus_pagamento = :status,
            sermp_payment_id = :payment_id,
            sermp_metodo_pagamento = :metodo,
            serdata_pagamento = NOW()
        WHERE serid = :serid
    ");
    $stmt->execute([
        ':status'     => $statusLocal,
        ':payment_id' => $id,
        ':metodo'     => $metodo,
        ':serid'      => $serid,
    ]);

    logAuditoria('webhook_mp_pagamento', [
        'serid'  => $serid,
        'status' => $statusLocal,
        'mp_id'  => $id,
        'metodo' => $metodo,
    ]);
} catch (PDOException $e) {
    error_log('[MP-WEBHOOK] Erro PDO: ' . $e->getMessage());
}

http_response_code(200);
echo json_encode(['status' => 'ok']);