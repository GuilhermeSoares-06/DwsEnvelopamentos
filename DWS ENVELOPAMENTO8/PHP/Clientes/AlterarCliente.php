<?php
// =============================================
// AlterarCliente.php — Admin edita cliente (PROTEGIDO)
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca();
exigirLoginAdmin();

$cliente = null;

// Buscar cliente
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE cliid = :id");
    $stmt->execute([':id' => $id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Atualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCSRF();
    $id       = (int) ($_POST['id'] ?? 0);
    $nome     = trim($_POST['nome'] ?? '');
    $senha    = trim($_POST['senha'] ?? '');
    $cpf      = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    if ($id <= 0 || $nome === '') {
        die('Dados inválidos.');
    }
    if ($cpf !== '' && !validarCPF($cpf)) {
        die('CPF inválido.');
    }

    try {
        if ($senha !== '') {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE clientes SET clinome = :n, clisenha = :s, clicpf = :c, clitel = :t, cliendereco = :e WHERE cliid = :id");
            $stmt->execute([':n' => $nome, ':s' => $senhaHash, ':c' => $cpf, ':t' => $telefone, ':e' => $endereco, ':id' => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE clientes SET clinome = :n, clicpf = :c, clitel = :t, cliendereco = :e WHERE cliid = :id");
            $stmt->execute([':n' => $nome, ':c' => $cpf, ':t' => $telefone, ':e' => $endereco, ':id' => $id]);
        }
        logAuditoria('admin_alterar_cliente', ['cliid' => $id]);
        header('Location: ListarCliente.php');
        exit;
    } catch (PDOException $e) {
        error_log('[AlterarCliente] ' . $e->getMessage());
        die('Erro ao atualizar cliente.');
    }
}

if (!$cliente) {
    die('Cliente não encontrado.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Cliente - DWS</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background: linear-gradient(135deg, #1e1e1e, #3b3b3b); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; font-family: 'Segoe UI', Arial, sans-serif; }
.container { background: #403E3F; padding: 40px; border-radius: 25px; border: 1px solid #F23535; width: 500px; max-width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
h1 { color: #fff; text-align: center; margin-bottom: 30px; font-size: 28px; }
.input-box { margin-bottom: 20px; }
.input-box input { width: 100%; padding: 15px; border: none; border-radius: 10px; background: #2b2b2b; color: #fff; font-size: 16px; outline: none; transition: 0.3s; }
.input-box input:focus { border: 1px solid #F23535; background: #1e1e1e; }
.input-box input::placeholder { color: #888; }
button { width: 100%; padding: 15px; background: linear-gradient(135deg, #F23535, #c91f2c); border: none; color: #fff; border-radius: 10px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s; }
button:hover { transform: scale(1.02); box-shadow: 0 5px 20px rgba(242,53,53,0.4); }
.aviso-senha { color: #888; font-size: 12px; margin-top: -15px; margin-bottom: 15px; }
</style>
</head>
<body>
    <div class="container">
        <h1>✏️ Editar Cliente</h1>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCSRF()) ?>">
            <input type="hidden" name="id" value="<?= (int) $cliente['cliid'] ?>">

            <div class="input-box">
                <input type="text" name="nome" value="<?= htmlspecialchars($cliente['clinome'] ?? '') ?>" placeholder="NOME COMPLETO" required>
            </div>
            <div class="input-box">
                <input type="password" name="senha" placeholder="NOVA SENHA (deixe em branco para manter)">
            </div>
            <div class="aviso-senha">⚠️ Se não quiser mudar a senha, deixe o campo em branco.</div>
            <div class="input-box">
                <input type="text" name="cpf" value="<?= htmlspecialchars($cliente['clicpf'] ?? '') ?>" placeholder="CPF" required>
            </div>
            <div class="input-box">
                <input type="text" name="telefone" value="<?= htmlspecialchars($cliente['clitel'] ?? '') ?>" placeholder="TELEFONE" required>
            </div>
            <div class="input-box">
                <input type="text" name="endereco" value="<?= htmlspecialchars($cliente['cliendereco'] ?? '') ?>" placeholder="ENDEREÇO" required>
            </div>
            <button type="submit">ATUALIZAR CLIENTE</button>
        </form>
    </div>
</body>
</html>