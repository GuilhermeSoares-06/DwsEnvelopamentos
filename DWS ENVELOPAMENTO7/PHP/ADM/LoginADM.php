<?php
// =============================================
// LoginADM.php - VERSÃO SEGURA + anti-cache
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Banco/conexao.php';

// 🚫 Anti-cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Rate limiting (5 tentativas / 15min)
$agora = time();
$_SESSION['login_tentativas'] = $_SESSION['login_tentativas'] ?? [];
$_SESSION['login_tentativas'] = array_filter($_SESSION['login_tentativas'], fn($t) => $agora - $t < 900);

if (count($_SESSION['login_tentativas']) >= 5) {
    header('Location: ../../telas/ADM/login.html?erro=bloqueado&t=' . time());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../telas/ADM/login.html');
    exit;
}

$nome  = trim($_POST['nome'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($nome) || empty($senha)) {
    header('Location: ../../telas/ADM/login.html?erro=1');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT cliid, clinome, clisenha 
        FROM clientes 
        WHERE clinome = :nome 
        AND tipocliente = 'funcionario' 
        LIMIT 1
    ");
    $stmt->execute([':nome' => $nome]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    $senhaConfere = false;
    if ($admin) {
        if (strpos($admin['clisenha'], '$2y$') === 0) {
            $senhaConfere = password_verify($senha, $admin['clisenha']);
        } else {
            if ($admin['clisenha'] === $senha) {
                $senhaConfere = true;
                $novoHash = password_hash($senha, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE clientes SET clisenha = :h WHERE cliid = :id")
                    ->execute([':h' => $novoHash, ':id' => $admin['cliid']]);
            }
        }
    }

    if ($senhaConfere) {
        session_regenerate_id(true);

        $_SESSION['admin_id']         = (int)$admin['cliid'];
        $_SESSION['admin_nome']       = $admin['clinome'];
        $_SESSION['admin_logado']     = true;
        $_SESSION['admin_login_time'] = time();

        $_SESSION['login_tentativas'] = [];

        // 🚫 Anti-cache no redirect
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        header('Location: ../../telas/ADM/principalFUN.php?t=' . time());
        exit;
    }

    $_SESSION['login_tentativas'][] = $agora;
    header('Location: ../../telas/ADM/login.html?erro=1&t=' . time());
    exit;

} catch (PDOException $e) {
    error_log("Erro login ADM: " . $e->getMessage());
    header('Location: ../../telas/ADM/login.html?erro=2');
    exit;
}