<?php
// =============================================
// precos.php - Retorna preços dinâmicos do banco
// =============================================
include("../Banco/conexao.php");

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    $configs = $pdo->query("
        SELECT chave, valor FROM configuracoes 
        WHERE chave LIKE 'preco_%' OR chave LIKE 'acabamento_%'
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

    echo json_encode([
        'status' => 'sucesso',
        'precos' => [
            'carro' => (float)($configs['preco_carro'] ?? 800),
            'moto' => (float)($configs['preco_moto'] ?? 500),
            'caminhao' => (float)($configs['preco_caminhao'] ?? 2500),
            'aquatico' => (float)($configs['preco_aquatico'] ?? 1800),
            'mobilia' => (float)($configs['preco_mobilia'] ?? 300)
        ],
        'acabamentos' => [
            '1.0' => 1.0,
            '1.15' => (float)($configs['acabamento_brilhante'] ?? 1.15),
            '1.30' => (float)($configs['acabamento_perolizado'] ?? 1.30),
            '1.40' => (float)($configs['acabamento_texturizado'] ?? 1.40)
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Erro precos: " . $e->getMessage());
    echo json_encode([
        'status' => 'erro',
        'precos' => [
            'carro' => 800, 'moto' => 500, 'caminhao' => 2500, 
            'aquatico' => 1800, 'mobilia' => 300
        ],
        'acabamentos' => ['1.0' => 1.0, '1.15' => 1.15, '1.30' => 1.30, '1.40' => 1.40]
    ]);
}
?>