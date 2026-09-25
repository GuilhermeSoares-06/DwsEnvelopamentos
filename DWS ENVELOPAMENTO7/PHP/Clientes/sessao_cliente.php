<?php
// =============================================
// sessao_cliente.php - Status de login do cliente
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Access-Control-Allow-Credentials: true');

echo json_encode([
    'logado'   => isset($_SESSION['cliid']),
    'cliid'    => $_SESSION['cliid']    ?? null,
    'nome'     => $_SESSION['clinome']  ?? null,
    'telefone' => $_SESSION['clitel']   ?? null,
    'cpf'      => $_SESSION['clicpf']   ?? null,
], JSON_UNESCAPED_UNICODE);
exit;
?>