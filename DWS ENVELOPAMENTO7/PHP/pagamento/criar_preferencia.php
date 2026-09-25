<?php
include("../Banco/conexao.php");
include("config_mp.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'erro','mensagem'=>'Método não permitido.']);
    exit;
}

if (empty($_SESSION['cliid'])) {
    http_response_code(401);
    echo json_encode(['status'=>'erro','mensagem'=>'Você precisa estar logado.']);
    exit;
}

$cliid = (int)$_SESSION['cliid'];
$serid = (int)($_POST['servico_id'] ?? 0);
$email_pagador = trim($_POST['email'] ?? '') ?: 'cliente@example.com';

if ($serid <= 0) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','mensagem'=>'Serviço inválido.']);
    exit;
}

// =============================================
// VALIDAÇÃO DE EMAIL
// =============================================
if (!filter_var($email_pagador, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','mensagem'=>'E-mail inválido. Preencha um e-mail válido.']);
    exit;
}

try {
    // Busca serviço
    $stmt = $pdo->prepare(
        "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor, s.serstatus_pagamento,
                c.clinome, c.clitel
         FROM servicos s
         JOIN clientes c ON c.cliid = s.cliid
         WHERE s.serid = :serid AND s.cliid = :cliid
         LIMIT 1"
    );
    $stmt->execute([':serid' => $serid, ':cliid' => $cliid]);
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$servico) {
        http_response_code(404);
        echo json_encode(['status'=>'erro','mensagem'=>'Agendamento não encontrado.']);
        exit;
    }

    if ($servico['serstatus_pagamento'] === 'aprovado') {
        http_response_code(409);
        echo json_encode(['status'=>'erro','mensagem'=>'Este agendamento já foi pago.']);
        exit;
    }

    $valor = (float)$servico['servalor'];
    $telefone = preg_replace('/\D/', '', $servico['clitel'] ?? '');

    error_log("=== CRIAR PREFERÊNCIA ===");
    error_log("Serviço: $serid | Valor: R$ $valor | Cliente: {$servico['clinome']}");
    error_log("SITE_URL: " . SITE_URL);

    // Monta preferência
    $preference = [
        'items' => [[
            'title' => 'DWS Envelopamento - ' . ucfirst($servico['tipo_servico']),
            'description' => mb_substr($servico['serdescricao'], 0, 250),
            'quantity' => 1,
            'currency_id' => 'BRL',
            'unit_price' => (float)$valor
        ]],
        'payer' => [
            'name' => $servico['clinome'],
            'email' => $email_pagador
        ],
        'back_urls' => [
            'success' => MP_URL_SUCESSO,
            'failure' => MP_URL_FALHA,
            'pending' => MP_URL_PENDENTE
        ],
        'auto_return' => 'approved',
        'notification_url' => MP_URL_WEBHOOK,
        'external_reference' => (string)$serid,
        'statement_descriptor' => 'DWS SERVICOS'
    ];

    // Adiciona telefone se tiver
    if ($telefone && strlen($telefone) >= 10) {
        $preference['payer']['phone'] = [
            'area_code' => substr($telefone, 0, 2),
            'number' => substr($telefone, 2)
        ];
    }

    error_log("Preferência enviada: " . json_encode($preference));

    // Chama API
    $resposta = mpRequest('POST', '/checkout/preferences', $preference);

    error_log("HTTP Code: " . $resposta['http_code']);
    error_log("Resposta: " . json_encode($resposta['body']));

    if ($resposta['http_code'] !== 201 || empty($resposta['body']['id'])) {
        $erroDetalhe = 'Erro desconhecido';
        if (!empty($resposta['body']['message'])) {
            $erroDetalhe = $resposta['body']['message'];
        } elseif (!empty($resposta['body']['error'])) {
            $erroDetalhe = $resposta['body']['error'];
        } elseif (!empty($resposta['body']['cause'])) {
            $erroDetalhe = json_encode($resposta['body']['cause']);
        } elseif (!empty($resposta['error'])) {
            $erroDetalhe = $resposta['error'];
        } elseif ($resposta['http_code'] === 401) {
            $erroDetalhe = 'Token de acesso inválido ou expirado.';
        } elseif ($resposta['http_code'] === 403) {
            $erroDetalhe = 'Acesso negado. Verifique as permissões do token.';
        }

        error_log("❌ ERRO MP: $erroDetalhe");

        http_response_code(502);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Erro do Mercado Pago: ' . $erroDetalhe,
            'debug' => [
                'http_code' => $resposta['http_code'],
                'resposta' => $resposta['body']
            ]
        ]);
        exit;
    }

    $preferenceId = $resposta['body']['id'];
    $initPoint = MP_AMBIENTE === 'production'
        ? ($resposta['body']['init_point'] ?? '')
        : ($resposta['body']['sandbox_init_point'] ?? '');

    if (empty($initPoint)) {
        http_response_code(500);
        echo json_encode(['status'=>'erro','mensagem'=>'URL de pagamento não retornada pelo Mercado Pago.']);
        exit;
    }

    // Salva ID da preferência
    $upd = $pdo->prepare("UPDATE servicos SET sermp_preference_id = :pref WHERE serid = :serid");
    $upd->execute([':pref' => $preferenceId, ':serid' => $serid]);

    error_log("✅ Preferência criada: $preferenceId");
    error_log("✅ Init point: $initPoint");

    echo json_encode([
        'status' => 'sucesso',
        'preference_id' => $preferenceId,
        'init_point' => $initPoint
    ]);

} catch (PDOException $e) {
    error_log("ERRO PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Erro interno ao gerar pagamento.']);
}
?>