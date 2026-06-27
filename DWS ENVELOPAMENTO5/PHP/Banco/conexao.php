<?php
// =============================================
// conexao.php - Conexão única PDO para todo o projeto
// =============================================
$host     = 'localhost';
$dbname   = 'dws';
$user     = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);

    // Compatibilidade: alias $conn para arquivos que usam mysqli_*
    // (não usar em código novo – use $pdo)
    $conn = null;

} catch (PDOException $e) {
    $isJson = (isset($_SERVER['HTTP_ACCEPT']) &&
               strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
              (isset($_SERVER['CONTENT_TYPE']) &&
               strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);

    if ($isJson) {
        header('Content-Type: application/json');
        die(json_encode([
            'status'   => 'erro',
            'mensagem' => 'Erro na conexão com o banco de dados.'
        ]));
    }
    die('<h2 style="color:red;font-family:sans-serif;padding:20px">
          Erro ao conectar ao banco de dados.<br>
          <small>' . htmlspecialchars($e->getMessage()) . '</small>
         </h2>');
}
?>
