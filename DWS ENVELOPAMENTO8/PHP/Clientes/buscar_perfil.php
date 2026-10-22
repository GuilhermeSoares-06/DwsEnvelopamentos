<?php
// =============================================
// buscar_perfil.php - Retorna dados do cliente logado
// =============================================
include("../Banco/conexao.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['cliid'])) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não autenticado']);
    exit;
}

$cliid = (int)$_SESSION['cliid'];

try {
    $stmt = $pdo->prepare("
        SELECT cliid, clinome, clicpf, clitel, cliendereco, cliemail
        FROM clientes 
        WHERE cliid = :id 
        LIMIT 1
    ");
    $stmt->execute([':id' => $cliid]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Cliente não encontrado']);
        exit;
    }

    echo json_encode([
        'status' => 'sucesso',
        'cliente' => $cliente
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Erro buscar_perfil: " . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno']);
}