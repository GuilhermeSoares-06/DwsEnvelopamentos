<?php
// =============================================
// conexao.php - VERSÃO CORRIGIDA
// =============================================

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'dws';  // Nome do seu banco
$user = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    // Log do erro (não exibir em produção)
    error_log("Erro de conexão: " . $e->getMessage());
    
    // Retorna erro em JSON
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    die(json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro na conexão com o banco de dados.'
    ]));
}
?>