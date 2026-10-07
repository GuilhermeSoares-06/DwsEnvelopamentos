<?php
include("../Banco/conexao.php");
include("config_mp.php");

// =============================================
// webhook.php - Recebe notificações do Mercado Pago
// =============================================

header('Content-Type: application/json; charset=utf-8');

// O MP manda notificações de duas formas:
$topic = $_GET['topic'] ?? $_GET['type'] ?? null;
$id    = $_GET['id'] ?? ($_GET['data_id'] ?? null);

if (!$topic || !$id) {
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);
    $topic = $payload['type'] ?? $topic;
    $id = $payload['data']['id'] ?? $id;
}

error_log("=== WEBHOOK MP RECEBIDO === Topic: $topic | ID: $id");

if ($topic !== 'payment' || empty($id)) {
    http_response_code(200);
    echo json_encode(['status'=>'ignorado']);
    exit;
}

// NUNCA CONFIA NO CONTEÚDO DA NOTIFICAÇÃO.
// Sempre consulta o pagamento de verdade na API.
$resposta = mpRequest('GET', "/v1/payments/$id");

if ($resposta['http_code'] !== 200 || empty($resposta['body'])) {
    error_log("Webhook: não foi possível consultar pagamento $id");
    http_response_code(200);
    echo json_encode(['status'=>'erro','mensagem'=>'Não foi possível consultar o pagamento']);
    exit;
}

$pagamento = $resposta['body'];
$serid = (int)($pagamento['external_reference'] ?? 0);
$statusMP = $pagamento['status'] ?? '';
$metodo = $pagamento['payment_type_id'] ?? '';

if ($serid <= 0) {
    error_log("Webhook: external_reference ausente no pagamento $id");
    http_response_code(200);
    echo json_encode(['status'=>'ignorado']);
    exit;
}

$mapaStatus = [
    'approved'     => 'aprovado',
    'pending'      => 'pendente',
    'in_process'   => 'pendente',
    'rejected'     => 'rejeitado',
    'cancelled'    => 'cancelado',
    'refunded'     => 'estornado',
    'charged_back' => 'estornado'
];

$statusLocal = $mapaStatus[$statusMP] ?? 'pendente';

try {
    $stmt = $pdo->prepare(
        "UPDATE servicos
         SET serstatus_pagamento = :status,
             sermp_payment_id = :payment_id,
             sermp_metodo_pagamento = :metodo,
             serdata_pagamento = NOW()
         WHERE serid = :serid"
    );
    $stmt->execute([
        ':status' => $statusLocal,
        ':payment_id' => $id,
        ':metodo' => $metodo,
        ':serid' => $serid
    ]);

    error_log("Webhook: serviço $serid atualizado para status '$statusLocal' (pagamento $id)");

} catch (PDOException $e) {
    error_log("Erro PDO no webhook: " . $e->getMessage());
}

http_response_code(200);
echo json_encode(['status'=>'ok']);
?>