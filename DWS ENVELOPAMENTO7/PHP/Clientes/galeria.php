<?php
// =============================================
// galeria.php - Lista todos os itens da galeria
// =============================================
include("../Banco/conexao.php");

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    $stmt = $pdo->query("
        SELECT id, titulo, descricao, tipo, imagem, criado_em
        FROM galeria
        ORDER BY criado_em DESC
        LIMIT 100
    ");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'sucesso',
        'items' => $items,
        'total' => count($items)
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Erro galeria: " . $e->getMessage());
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro ao carregar galeria',
        'items' => []
    ]);
}
?>