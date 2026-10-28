<?php
// =============================================
// criar_preferencia.php — Gera o checkout do Mercado Pago
// =============================================
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/config_mp.php';

enviarHeadersSeguranca(true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderErro('Método não permitido.', 405);
}

exigirLoginCliente();
exigirCSRF();

$cliid = (int) $_SESSION['cliid'];
$serid = (int) ($_POST['servico_id'] ?? 0);

if ($serid <= 0) {
    responderErro('Serviço inválido.', 400);
}

try {
    $stmt = $pdo->prepare("
        SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor, s.serstatus_pagamento,
               c.clinome, c.clitel, c.cliemail
        FROM servicos s
        JOIN clientes c ON c.cliid = s.cliid
        WHERE s.serid = :serid AND s.cliid = :cliid
          AND " . sqlAgendamentoAtivo('s') . "
        LIMIT 1
    ");
    $stmt->execute([':serid' => $serid, ':cliid' => $cliid]);
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$servico) {
        responderErro('Agendamento não encontrado ou expirado. Faça um novo agendamento.', 404);
    }

    if ($servico['serstatus_pagamento'] === 'aprovado') {
        responderErro('Este agendamento já foi pago.', 409);
    }

    // E-mail do cadastro tem prioridade; o enviado pelo front é só fallback
    $emailPagador = trim((string) ($servico['cliemail'] ?? ''));
    if ($emailPagador === '') $emailPagador = trim((string) ($_POST['email'] ?? ''));
    if (!validarEmail($emailPagador)) {
        responderErro('Cadastre um e-mail válido no seu perfil para pagar online.', 400);
    }

    $valor    = (float) $servico['servalor'];
    $telefone = limparTelefone($servico['clitel'] ?? '');

    $preference = [
        'items' => [[
            'title'       => 'DWS Envelopamento - ' . ucfirst($servico['tipo_servico']),
            'description' => mb_substr($servico['serdescricao'], 0, 250),
            'quantity'    => 1,
            'currency_id' => 'BRL',
            'unit_price'  => $valor,
        ]],
        'payer' => [
            'name'  => $servico['clinome'],
            'email' => $emailPagador,
        ],
        'back_urls' => [
            'success' => MP_URL_SUCESSO,
            'failure' => MP_URL_FALHA,
            'pending' => MP_URL_PENDENTE,
        ],
        'auto_return'          => 'approved',
        'notification_url'     => MP_URL_WEBHOOK,
        'external_reference'   => (string) $serid,
        'statement_descriptor' => 'DWS SERVICOS',
    ];

    if (strlen($telefone) >= 10) {
        $preference['payer']['phone'] = [
            'area_code' => substr($telefone, 0, 2),
            'number'    => substr($telefone, 2),
        ];
    }

    $resposta = mpRequest('POST', '/checkout/preferences', $preference);

    if ($resposta['http_code'] !== 201 || empty($resposta['body']['id'])) {
        // Detalhes só no log do servidor; o cliente recebe mensagem genérica
        error_log('[MP] Falha ao criar preferência serid=' . $serid
            . ' http=' . $resposta['http_code']
            . ' erro=' . ($resposta['error'] ?? ($resposta['body']['message'] ?? '-')));

        $extras = APP_DEBUG ? ['debug' => ['http_code' => $resposta['http_code'], 'resposta' => $resposta['body']]] : [];
        responderErro('Não foi possível iniciar o pagamento. Tente novamente em instantes.', 502, $extras);
    }

    $preferenceId = $resposta['body']['id'];
    $initPoint = MP_AMBIENTE === 'production'
        ? ($resposta['body']['init_point'] ?? '')
        : ($resposta['body']['sandbox_init_point'] ?? '');

    if (empty($initPoint)) {
        responderErro('URL de pagamento não retornada pelo Mercado Pago.', 502);
    }

    $pdo->prepare("UPDATE servicos SET sermp_preference_id = :pref WHERE serid = :serid")
        ->execute([':pref' => $preferenceId, ':serid' => $serid]);

    logAuditoria('mp_preferencia_criada', ['serid' => $serid, 'preference_id' => $preferenceId]);

    responderSucesso('Preferência criada.', [
        'preference_id' => $preferenceId,
        'init_point'    => $initPoint,
    ]);

} catch (PDOException $e) {
    error_log('[criar_preferencia] PDO: ' . $e->getMessage());
    responderErro('Erro interno ao gerar pagamento.', 500);
}
