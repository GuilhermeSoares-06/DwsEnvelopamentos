<?php
// =============================================
// LoginCliente.php - VERSÃO CORRIGIDA (login por CPF)
// =============================================
session_start();

// Habilita exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../Banco/conexao.php';

function paginaErro($titulo, $mensagem) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>$titulo - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: 'Segoe UI', Arial, sans-serif;
            }
            .box{
                background: #403E3F;
                padding: 50px;
                border-radius: 25px;
                text-align: center;
                border: 1px solid #F23535;
                animation: fadeIn 0.5s ease;
                max-width: 400px;
            }
            .error-icon{
                width: 100px;
                height: 100px;
                background: #F23535;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                animation: shake 0.5s ease;
            }
            .error-icon span{ font-size: 50px; color: white; }
            h1{ color: white; margin-bottom: 15px; }
            p{ color: #ccc; margin-bottom: 10px; }
            .btn-voltar{
                background: #F23535;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 30px;
                font-size: 16px;
                cursor: pointer;
                margin-top: 20px;
                transition: transform 0.3s;
            }
            .btn-voltar:hover{ transform: scale(1.05); }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-30px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes shake{
                0%,100%{ transform: translateX(0); }
                25%{ transform: translateX(-10px); }
                75%{ transform: translateX(10px); }
            }
        </style>
        <script>
            setTimeout(() => { window.history.back(); }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='error-icon'><span>✗</span></div>
            <h1>$titulo</h1>
            <p>$mensagem</p>
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

// =============================================
// LOGIN AGORA É FEITO POR CPF + SENHA (em vez de nome de usuário)
// =============================================
$cpf   = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (!$cpf || !$senha) {
    paginaErro('Campos incompletos', 'Preencha CPF e senha.');
}

if (strlen($cpf) !== 11) {
    paginaErro('CPF inválido', 'Informe um CPF válido, com 11 dígitos.');
}

try {
    // =============================================
    // CONSULTA POR CPF - SEM clifoto
    // =============================================
    $stmt = $pdo->prepare("
        SELECT cliid, clinome, clisenha, clitel, clicpf, cliendereco, tipocliente 
        FROM clientes 
        WHERE clicpf = :cpf 
        ORDER BY cliid DESC 
        LIMIT 1
    ");
    $stmt->execute([':cpf' => $cpf]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente || empty($cliente['clisenha'])) {
        paginaErro('Login inválido', 'CPF ou senha incorretos.');
    }

    // Verifica a senha
    $senhaConfere = false;

    // Verifica se é hash bcrypt
    if (strpos($cliente['clisenha'], '$2y$') === 0) {
        $senhaConfere = password_verify($senha, $cliente['clisenha']);
    } else {
        // Senha em texto puro
        if ($cliente['clisenha'] === $senha) {
            $senhaConfere = true;
            // Atualiza para hash
            $novoHash = password_hash($senha, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE clientes SET clisenha = :h WHERE cliid = :id");
            $upd->execute([':h' => $novoHash, ':id' => $cliente['cliid']]);
        }
    }

    if (!$senhaConfere) {
        paginaErro('Login inválido', 'CPF ou senha incorretos.');
    }

    // =============================================
    // LOGIN OK - Armazena os dados na sessão
    // =============================================
    $_SESSION['cliid']      = (int)$cliente['cliid'];
    $_SESSION['clinome']    = $cliente['clinome'];
    $_SESSION['clitel']     = $cliente['clitel'] ?? '';
    $_SESSION['clicpf']     = $cliente['clicpf'] ?? '';
    $_SESSION['cliendereco']= $cliente['cliendereco'] ?? '';
    $_SESSION['tipocliente']= $cliente['tipocliente'] ?? 'cliente';

    // Redireciona conforme o tipo de usuário
    if ($_SESSION['tipocliente'] === 'funcionario') {
        $redirect = '../../telas/ADM/principalFUN.html';
    } else {
        $redirect = '../../telas/Cliente/principal.html';
    }

    // =============================================
    // REDIRECIONA
    // =============================================
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Login realizado - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: 'Segoe UI', Arial, sans-serif;
            }
            .box{
                background: #403E3F;
                padding: 50px;
                border-radius: 25px;
                text-align: center;
                border: 1px solid #4CAF50;
                animation: fadeIn 0.6s ease;
                max-width: 400px;
            }
            .success-icon{
                width: 100px;
                height: 100px;
                background: #4CAF50;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                animation: bounce 0.8s ease;
            }
            .success-icon span{ font-size: 50px; color: white; }
            h1{ color: white; margin-bottom: 15px; font-size: 24px; }
            p{ color: #ccc; margin-bottom: 10px; }
            .dados-cliente {
                background: #2a2a2a;
                padding: 15px;
                border-radius: 10px;
                margin: 15px 0;
                text-align: left;
                color: #ddd;
                font-size: 14px;
            }
            .dados-cliente strong { color: #F23535; }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-30px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes bounce{
                0%,100%{ transform: scale(1); }
                50%{ transform: scale(1.1); }
            }
        </style>
        <script>
            setTimeout(() => {
                window.location.href = '$redirect';
            }, 2000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='success-icon'><span>✓</span></div>
            <h1>Bem-vindo, " . htmlspecialchars($cliente['clinome']) . "!</h1>
            <div class='dados-cliente'>
                <p><strong>📱 Telefone:</strong> " . htmlspecialchars($cliente['clitel'] ?? 'Não informado') . "</p>
                <p><strong>🆔 CPF:</strong> " . htmlspecialchars($cliente['clicpf'] ?? 'Não informado') . "</p>
                <p><strong>👤 Tipo:</strong> " . ucfirst($cliente['tipocliente'] ?? 'cliente') . "</p>
            </div>
            <p>Redirecionando para o painel...</p>
        </div>
    </body>
    </html>
    ";
    exit;

} catch (PDOException $e) {
    error_log("Erro no login: " . $e->getMessage());
    paginaErro('Erro no sistema', 'Ocorreu um erro ao processar o login. Tente novamente.');
}
?>