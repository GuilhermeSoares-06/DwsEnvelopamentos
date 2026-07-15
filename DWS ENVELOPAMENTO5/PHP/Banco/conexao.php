<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'dws';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()
    ]));
}
?>