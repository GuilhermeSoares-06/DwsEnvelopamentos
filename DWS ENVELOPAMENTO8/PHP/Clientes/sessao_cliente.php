<?php
// =============================================
// sessao_cliente.php - Status de login do cliente
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (empty($_SESSION['cliid'])) {
    echo json_encode(['logado' => false]);
    exit;
}

// 🔍 Confirma que o cliente ainda existe

try {
    $stmt = $pdo->prepare("
        SELECT cliid, clinome, clitel, clicpf 
        FROM clientes 
        WHERE cliid = :id AND (tipocliente = 'cliente' OR tipocliente IS NULL)
        LIMIT 1
    ");
    $stmt->execute([':id' => (int)$_SESSION['cliid']]);
    $cli = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cli) {
        // 🚫 Conta foi removida
        session_destroy();
        echo json_encode([
            'logado' => false,
            'motivo' => 'excluido',
            'mensagem' => 'Sua conta foi desativada.'
        ]);
        exit;
    }

    echo json_encode([
        'logado'   => true,
        'cliid'    => (int)$cli['cliid'],
        'nome'     => $cli['clinome'],
        'telefone' => $cli['clitel'],
        'cpf'      => $cli['clicpf'],
        'csrf_token' => gerarTokenCSRF(),
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Erro sessao_cliente: " . $e->getMessage());
    echo json_encode(['logado' => false, 'motivo' => 'erro']);
}
exit;