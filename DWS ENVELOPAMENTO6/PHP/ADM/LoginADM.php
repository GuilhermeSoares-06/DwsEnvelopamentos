<?php
session_start();
require_once __DIR__ . '/../Banco/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Buscar admin no banco
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE clinome = ? AND tipocliente = 'funcionario'");
    $stmt->execute([$nome]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && $senha === $admin['clisenha']) {
        // Iniciar sessão
        $_SESSION['admin_id'] = $admin['cliid'];
        $_SESSION['admin_nome'] = $admin['clinome'];
        header('Location: ../../ADM/principalFUN.html');
        exit;
    } else {
        header('Location: ../../ADM/login.html?erro=1');
        exit;
    }
}
?>