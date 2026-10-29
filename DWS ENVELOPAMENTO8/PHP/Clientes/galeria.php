<?php
// =============================================
// galeria.php - Retorna os itens da galeria em JSON
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    $items = $pdo->query("
        SELECT id, titulo, descricao, tipo, imagem, criado_em
        FROM galeria
        ORDER BY criado_em DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'sucesso',
        'items'  => $items
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    error_log("Erro galeria: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'items' => []]);
}
