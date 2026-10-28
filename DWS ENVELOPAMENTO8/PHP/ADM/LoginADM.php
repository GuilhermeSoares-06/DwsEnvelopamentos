<?php
// =============================================
// LoginADM.php — Login do funcionário/admin
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);
verificarRateLimit('login_adm_' . ipCliente(), 5, 900);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../telas/ADM/login.html');
    exit;
}

$usuario  = trim($_POST['nome'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($usuario === '' || $senha === '') {
    header('Location: ../../telas/ADM/login.html?erro=1');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT f.id, f.nome, f.senha, f.cargo_id, c.nome as cargo_nome
        FROM funcionarios f
        LEFT JOIN cargos c ON c.id = f.cargo_id
        WHERE f.usuario = :usuario 
        LIMIT 1
    ");
    $stmt->execute([':usuario' => $usuario]);
    $func = $stmt->fetch(PDO::FETCH_ASSOC);

    $senhaConfere = false;

    if ($func) {
        $hash = (string) $func['senha'];
        if (password_verify($senha, $hash)) {
            $senhaConfere = true;
            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                $novo = password_hash($senha, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE funcionarios SET senha = :h WHERE id = :id")
                    ->execute([':h' => $novo, ':id' => $func['id']]);
            }
        }
    }

    if (!$senhaConfere) {
        registrarTentativa('login_adm_' . ipCliente());
        header('Location: ../../telas/ADM/login.html?erro=1');
        exit;
    }

    limparTentativas('login_adm_' . ipCliente());
    session_regenerate_id(true);

    $_SESSION['admin_id']         = (int) $func['id'];
    $_SESSION['admin_nome']       = $func['nome'];
    $_SESSION['admin_cargo']      = $func['cargo_nome'] ?? 'Sem Cargo';
    $_SESSION['admin_logado']     = true;
    $_SESSION['admin_login_time'] = time();

    logAuditoria('admin_login', ['id' => $func['id'], 'usuario' => $usuario]);

    header('Location: ../../telas/ADM/principalFun.php');
    exit;

} catch (PDOException $e) {
    error_log('[LoginADM] ' . $e->getMessage());
    header('Location: ../../telas/ADM/login.html?erro=2');
    exit;
}