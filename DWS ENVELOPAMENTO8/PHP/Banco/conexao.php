<?php
// =============================================
// conexao.php — PDO via config.php (sem hardcode)
// =============================================

if (!function_exists('config')) {
    require_once __DIR__ . '/../Config/config_loader.php';
}

$__host    = config('db.host', 'localhost');
$__port    = (int) config('db.port', 3306);
$__dbname  = config('db.name', 'dws');
$__user    = config('db.user', 'root');
$__pass    = config('db.pass', '');
$__charset = config('db.charset', 'utf8mb4');

try {
    $pdo = new PDO(
        "mysql:host={$__host};port={$__port};dbname={$__dbname};charset={$__charset}",
        $__user,
        $__pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$__charset}",
        ]
    );
} catch (PDOException $e) {
    error_log('[DB] Falha de conexão: ' . $e->getMessage());

    $msg = (defined('APP_DEBUG') && APP_DEBUG)
        ? 'Erro de conexão com o banco: ' . $e->getMessage()
        : 'Erro na conexão com o banco de dados.';

    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
    }
    die(json_encode(['status' => 'erro', 'mensagem' => $msg]));
}

unset($__host, $__port, $__dbname, $__user, $__pass, $__charset);