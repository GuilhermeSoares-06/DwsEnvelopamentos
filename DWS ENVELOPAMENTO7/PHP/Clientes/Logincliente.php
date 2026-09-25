<?php
// =============================================
// Logincliente.php - Login + redirect limpo + anti-cache
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Banco/conexao.php';

function paginaErro($titulo, $mensagem) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>" . htmlspecialchars($titulo) . " - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
                display: flex; justify-content: center; align-items: center;
                height: 100vh; font-family: 'Segoe UI', Arial, sans-serif;
            }
            .box{
                background: #403E3F; padding: 50px; border-radius: 25px;
                text-align: center; border: 1px solid #F23535;
                animation: fadeIn 0.5s ease; max-width: 400px;
            }
            .error-icon{
                width: 100px; height: 100px; background: #F23535;
                border-radius: 50%; display: flex; align-items: center;
                justify-content: center; margin: 0 auto 20px;
            }
            .error-icon span{ font-size: 50px; color: white; }
            h1{ color: white; margin-bottom: 15px; }
            p{ color: #ccc; margin-bottom: 10px; }
            .btn-voltar{
                background: #F23535; color: white; border: none;
                padding: 12px 30px; border-radius: 30px; font-size: 16px;
                cursor: pointer; margin-top: 20px; transition: transform 0.3s;
            }
            .btn-voltar:hover{ transform: scale(1.05); }
            @keyframes fadeIn{ from{ opacity: 0; transform: translateY(-30px); } to{ opacity: 1; transform: translateY(0); } }
        </style>
        <script>setTimeout(() => { window.history.back(); }, 3000);</script>
    </head>
    <body>
        <div class='box'>
            <div class='error-icon'><span>✗</span></div>
            <h1>" . htmlspecialchars($titulo) . "</h1>
            <p>" . htmlspecialchars($mensagem) . "</p>
            <p>Redirecionando em 3 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>Voltar agora</button>
        </div>
    </body>
    </html>
    ";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../telas/Cliente/loginClientes.html');
    exit();
}

$cpf   = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (!$cpf || !$senha) {
    paginaErro('Campos incompletos', 'Preencha CPF e senha.');
}

if (strlen($cpf) !== 11) {
    paginaErro('CPF inválido', 'Informe um CPF válido, com 11 dígitos.');
}

// Captura o destino passado pelo form
$redirect = trim($_POST['redirect'] ?? '');

$permitidos = [
    'contato.html',
    'servico.html',
    'sobre.html',
    'principal.html',
    'meus_agendamentos.html',
    'editar_perfil.html'
];

if ($redirect === '' || !in_array($redirect, $permitidos, true)) {
    $redirect = 'principal.html';
}

try {
    $stmt = $pdo->prepare("
        SELECT cliid, clinome, clisenha, clitel, clicpf, cliendereco, tipocliente
        FROM clientes
        WHERE clicpf = :cpf
        ORDER BY cliid DESC
        LIMIT 1
    ");
    $stmt->execute([':cpf' => $cpf]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Recusa funcionário no login de cliente
    $isFuncionario = $cliente && ($cliente['tipocliente'] ?? '') === 'funcionario';

    $senhaConfere = false;
    if ($cliente && !$isFuncionario && !empty($cliente['clisenha'])) {
        $hash = (string)$cliente['clisenha'];

        if (password_verify($senha, $hash)) {
            $senhaConfere = true;

            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                $novoHash = password_hash($senha, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE clientes SET clisenha = :h WHERE cliid = :id")
                    ->execute([':h' => $novoHash, ':id' => $cliente['cliid']]);
            }
        }
    }

    // Mensagem genérica (não revela se o CPF existe)
    if (!$senhaConfere) {
        paginaErro('Login inválido', 'CPF ou senha incorretos.');
    }

    session_regenerate_id(true);

    $_SESSION['cliid']        = (int)$cliente['cliid'];
    $_SESSION['clinome']      = $cliente['clinome'];
    $_SESSION['clitel']       = $cliente['clitel'] ?? '';
    $_SESSION['clicpf']       = $cliente['clicpf'] ?? '';
    $_SESSION['cliendereco']  = $cliente['cliendereco'] ?? '';
    $_SESSION['tipocliente']  = 'cliente';
    $_SESSION['_login_time']  = time();
    $_SESSION['_login_fresh'] = time(); // marca sessão recém-criada

    // 🔥 Headers anti-cache pra o navegador não reusar a página antiga
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    // 🔥 Timestamp pra o JS saber que é sessão nova
    $ts = time();

    // 🔥 Redireciona com ?login=ok&nome=Nome&t=timestamp
    $urlFinal = '../../telas/Cliente/' . $redirect
              . '?login=ok'
              . '&nome=' . urlencode($cliente['clinome'])
              . '&t=' . $ts;

    header('Location: ' . $urlFinal);
    exit;

} catch (PDOException $e) {
    error_log("Erro no login cliente: " . $e->getMessage());
    paginaErro('Erro no sistema', 'Ocorreu um erro. Tente novamente.');
}