<?php
// =============================================
// LoginADM.php - VERSÃO SEGURA
// =============================================
session_start();
require_once __DIR__ . '/../Banco/conexao.php';

// Rate limiting simples (5 tentativas por 15 min)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$agora = time();
$_SESSION['login_tentativas'] = $_SESSION['login_tentativas'] ?? [];
$_SESSION['login_tentativas'] = array_filter($_SESSION['login_tentativas'], fn($t) => $agora - $t < 900);

if (count($_SESSION['login_tentativas']) >= 5) {
    header('Location: ../../telas/ADM/login.html?erro=bloqueado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
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
                // Senha em texto puro - atualiza
                if ($admin['clisenha'] === $senha) {
                    $senhaConfere = true;
                    $novoHash = password_hash($senha, PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE clientes SET clisenha = :h WHERE cliid = :id")
                        ->execute([':h' => $novoHash, ':id' => $admin['cliid']]);
                }
            }
        }

        if ($senhaConfere) {
            // Regenera ID de sessão (anti session fixation)
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['cliid'];
            $_SESSION['admin_nome'] = $admin['clinome'];
            $_SESSION['admin_logado'] = true;
            $_SESSION['admin_login_time'] = time();

            // Limpa tentativas
            $_SESSION['login_tentativas'] = [];

            header('Location: ../../telas/ADM/principalFUN.html');
            exit;
        }

        $_SESSION['login_tentativas'][] = $agora;
        header('Location: ../../telas/ADM/login.html?erro=1');
        exit;

    } catch (PDOException $e) {
        error_log("Erro login ADM: " . $e->getMessage());
        header('Location: ../../telas/ADM/login.html?erro=2');
        exit;
    }
}

header('Location: ../../telas/ADM/login.html');
exit;