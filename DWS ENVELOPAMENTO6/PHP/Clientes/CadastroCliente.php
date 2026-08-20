<?php
// CORREÇÃO DE CAMINHOS (usa __DIR__ para evitar erros de "failed to open stream")
include(__DIR__ . "/../Banco/conexao.php");
include(__DIR__ . "/../../PHP/Clientes/validacao.php");

function paginaErroCadastro($titulo, $mensagem) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$titulo - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background: #111; /* Fundo mais escuro e sólido */
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
                font-family: 'Segoe UI', Arial, sans-serif;
            }
            .box{
                background: #1e1e1e; /* Fundo do cartão mais escuro */
                padding: 40px 30px;
                border-radius: 20px;
                text-align: center;
                border: 2px solid #F23535;
                animation: fadeIn 0.5s ease;
                width: 100%;
                max-width: 480px;
                box-shadow: 0 15px 40px rgba(0,0,0,0.8);
            }
            .error-icon{
                width: 90px;
                height: 90px;
                background: #F23535;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                box-shadow: 0 0 30px rgba(242, 53, 53, 0.4);
            }
            .error-icon span{ font-size: 50px; color: white; font-weight: bold; }
            h1{ color: #ffffff; margin-bottom: 15px; font-size: 2rem; letter-spacing: -0.5px; }
            p{ color: #bbbbbb; margin-bottom: 10px; font-size: 1.1rem; line-height: 1.5; }
            .btn-voltar{
                background: #F23535;
                color: white;
                border: none;
                padding: 16px 40px;
                border-radius: 50px;
                font-size: 1.1rem;
                font-weight: bold;
                cursor: pointer;
                margin-top: 25px;
                width: 100%;
                max-width: 280px;
                transition: all 0.3s;
                box-shadow: 0 6px 20px rgba(242, 53, 53, 0.4);
                letter-spacing: 1px;
            }
            .btn-voltar:hover{ 
                transform: translateY(-3px); 
                background: #d62c2c;
                box-shadow: 0 8px 30px rgba(242, 53, 53, 0.6);
            }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-40px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            
            @media (max-width: 500px) {
                .box { padding: 30px 20px; border-radius: 16px; }
                h1 { font-size: 1.6rem; }
                .error-icon { width: 70px; height: 70px; }
                .error-icon span { font-size: 35px; }
                .btn-voltar { padding: 18px; font-size: 1rem; max-width: 100%; }
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
            <p style='color:#666; font-size:0.9rem;'>Redirecionando em 3 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>🔄 Voltar agora</button>
        </div>
    </body>
    </html>
    ";
    exit();
}

// VERIFICA CAMPOS
if(!isset($_POST['nome']) || !isset($_POST['Senha']) || !isset($_POST['cpf']) || !isset($_POST['telefone']) || !isset($_POST['endereco'])) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Erro - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background: #111;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
                font-family: 'Segoe UI', Arial, sans-serif;
            }
            .box{
                background: #1e1e1e;
                padding: 40px 30px;
                border-radius: 20px;
                text-align: center;
                border: 2px solid #F23535;
                animation: fadeIn 0.5s ease;
                width: 100%;
                max-width: 480px;
                box-shadow: 0 15px 40px rgba(0,0,0,0.8);
            }
            .error-icon{
                width: 90px;
                height: 90px;
                background: #F23535;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                animation: shake 0.6s ease;
                box-shadow: 0 0 30px rgba(242, 53, 53, 0.4);
            }
            .error-icon span{ font-size: 50px; color: white; font-weight: bold; }
            h1{ color: #ffffff; margin-bottom: 15px; font-size: 2rem; }
            p{ color: #bbbbbb; margin-bottom: 10px; font-size: 1.1rem; }
            .btn-voltar{
                background: #F23535;
                color: white;
                border: none;
                padding: 16px 40px;
                border-radius: 50px;
                font-size: 1.1rem;
                font-weight: bold;
                cursor: pointer;
                margin-top: 25px;
                width: 100%;
                max-width: 280px;
                transition: all 0.3s;
                box-shadow: 0 6px 20px rgba(242, 53, 53, 0.4);
            }
            .btn-voltar:hover{ transform: scale(1.05); background: #d62c2c; }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-40px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes shake{
                0%,100%{ transform: translateX(0); }
                25%{ transform: translateX(-12px); }
                75%{ transform: translateX(12px); }
            }
            @media (max-width: 500px) {
                .box { padding: 30px 20px; border-radius: 16px; }
                h1 { font-size: 1.6rem; }
                .error-icon { width: 70px; height: 70px; }
                .error-icon span { font-size: 35px; }
                .btn-voltar { padding: 18px; font-size: 1rem; max-width: 100%; }
            }
        </style>
        <script>
            setTimeout(() => { window.history.back(); }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='error-icon'><span>✗</span></div>
            <h1>Campos Incompletos!</h1>
            <p>Preencha todos os campos do formulário.</p>
            <p style='color:#666; font-size:0.9rem;'>Redirecionando em 3 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>🔄 Voltar agora</button>
        </div>
    </body>
    </html>
    ";
    exit();
}

$nome     = trim($_POST['nome']);
$senha    = password_hash($_POST['Senha'], PASSWORD_DEFAULT); 
$cpf      = preg_replace('/\D/', '', $_POST['cpf']);          
$telefone = trim($_POST['telefone']);
$endereco = trim($_POST['endereco']);

// VERIFICA SE CLIENTE JA EXISTE PELO CPF
$check = $pdo->prepare("SELECT cliid FROM clientes WHERE clicpf = :cpf");
$check->execute([':cpf' => $cpf]);
$clienteExistente = $check->fetch();

if ($clienteExistente) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Cliente Existente - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background: #111;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
                font-family: 'Segoe UI', Arial, sans-serif;
            }
            .box{
                background: #1e1e1e;
                padding: 40px 30px;
                border-radius: 20px;
                text-align: center;
                border: 2px solid #ff9800;
                animation: fadeIn 0.5s ease;
                width: 100%;
                max-width: 480px;
                box-shadow: 0 15px 40px rgba(0,0,0,0.8);
            }
            .warning-icon{
                width: 90px;
                height: 90px;
                background: #ff9800;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                animation: pulse 0.8s ease;
                box-shadow: 0 0 30px rgba(255, 152, 0, 0.4);
            }
            .warning-icon span{ font-size: 50px; color: white; font-weight: bold; }
            h1{ color: #ffffff; margin-bottom: 15px; font-size: 2rem; }
            p{ color: #bbbbbb; margin-bottom: 10px; font-size: 1.1rem; }
            .btn-voltar{
                background: #F23535;
                color: white;
                border: none;
                padding: 16px 40px;
                border-radius: 50px;
                font-size: 1.1rem;
                font-weight: bold;
                cursor: pointer;
                margin-top: 25px;
                width: 100%;
                max-width: 280px;
                transition: all 0.3s;
                box-shadow: 0 6px 20px rgba(242, 53, 53, 0.4);
            }
            .btn-voltar:hover{ transform: scale(1.05); background: #d62c2c; }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-40px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes pulse{
                0%,100%{ transform: scale(1); }
                50%{ transform: scale(1.15); }
            }
            @media (max-width: 500px) {
                .box { padding: 30px 20px; border-radius: 16px; }
                h1 { font-size: 1.6rem; }
                .warning-icon { width: 70px; height: 70px; }
                .warning-icon span { font-size: 35px; }
                .btn-voltar { padding: 18px; font-size: 1rem; max-width: 100%; }
            }
        </style>
        <script>
            setTimeout(() => { window.history.back(); }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='warning-icon'><span>⚠</span></div>
            <h1>Cliente já existe!</h1>
            <p>Já existe um cliente cadastrado com este CPF.</p>
            <p style='color:#666; font-size:0.9rem;'>Redirecionando em 3 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>🔄 Voltar agora</button>
        </div>
    </body>
    </html>
    ";
    exit();
}

// TRATAMENTO DA FOTO DE PERFIL (opcional)
$clifoto = null;
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $extensoesPermitidas)) {
        $pastaDestino = __DIR__ . '/../../uploads/clientes/';
        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0755, true);
        }
        $nomeArquivo = 'cli_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $pastaDestino . $nomeArquivo)) {
            $clifoto = 'uploads/clientes/' . $nomeArquivo;
        }
    }
}

// INSERE CLIENTE
$sucesso = false;
$erroMsg = '';
try {
    if ($clifoto !== null) {
        try {
            $stmt = $pdo->prepare("INSERT INTO clientes (clinome, clisenha, clicpf, clitel, cliendereco, clifoto, tipocliente) VALUES (:n, :s, :c, :t, :e, :f, 'cliente')");
            $sucesso = $stmt->execute([
                ':n' => $nome, ':s' => $senha, ':c' => $cpf, ':t' => $telefone, ':e' => $endereco, ':f' => $clifoto
            ]);
        } catch (PDOException $eFoto) {
            if (strpos($eFoto->getMessage(), 'clifoto') === false) {
                throw $eFoto;
            }
            $stmt = $pdo->prepare("INSERT INTO clientes (clinome, clisenha, clicpf, clitel, cliendereco, tipocliente) VALUES (:n, :s, :c, :t, :e, 'cliente')");
            $sucesso = $stmt->execute([
                ':n' => $nome, ':s' => $senha, ':c' => $cpf, ':t' => $telefone, ':e' => $endereco
            ]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO clientes (clinome, clisenha, clicpf, clitel, cliendereco, tipocliente) VALUES (:n, :s, :c, :t, :e, 'cliente')");
        $sucesso = $stmt->execute([
            ':n' => $nome, ':s' => $senha, ':c' => $cpf, ':t' => $telefone, ':e' => $endereco
        ]);
    }
} catch (PDOException $e) {
    $sucesso = false;
    $erroMsg = $e->getMessage();
}

if($sucesso) {
    // SUCESSO COM ANIMAÇÃO BONITA E RESPONSIVO (MELHORADO)
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Cadastro Realizado - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background: #111;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
                font-family: 'Segoe UI', Arial, sans-serif;
            }
            .box{
                background: #1e1e1e;
                padding: 45px 35px;
                border-radius: 20px;
                text-align: center;
                border: 2px solid #4CAF50;
                width: 100%;
                max-width: 480px;
                animation: fadeIn 0.6s ease;
                box-shadow: 0 15px 50px rgba(76, 175, 80, 0.2);
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
                box-shadow: 0 0 40px rgba(76, 175, 80, 0.5);
            }
            .success-icon span{
                font-size: 55px;
                color: white;
                font-weight: bold;
                animation: check 0.5s ease 0.3s both;
            }
            h1{
                color: #ffffff;
                margin-bottom: 20px;
                font-size: 2rem;
                animation: slideUp 0.5s ease 0.2s both;
            }
            .cliente-info{
                background: #0f0f0f;
                padding: 20px;
                border-radius: 12px;
                margin: 20px 0;
                animation: slideUp 0.5s ease 0.4s both;
                border: 1px solid #333;
            }
            .cliente-info p{
                color: #4CAF50;
                font-weight: bold;
                font-size: 1.2rem;
                margin: 5px 0;
                word-break: break-word;
            }
            .cliente-info small{
                color: #aaa;
                font-size: 13px;
            }
            p{
                color: #bbbbbb;
                margin-bottom: 10px;
                animation: slideUp 0.5s ease 0.6s both;
                font-size: 1.1rem;
            }
            .loader{
                width: 100%;
                height: 5px;
                background: #333;
                border-radius: 10px;
                margin: 30px auto 0;
                overflow: hidden;
                animation: slideUp 0.5s ease 0.8s both;
            }
            .loader span{
                display: block;
                height: 100%;
                width: 100%;
                background: #4CAF50;
                animation: loading 2.5s linear forwards;
                box-shadow: 0 0 20px rgba(76, 175, 80, 0.6);
            }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-60px) scale(0.9); }
                to{ opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes bounce{
                0%,100%{ transform: scale(1); }
                50%{ transform: scale(1.2); }
            }
            @keyframes check{
                0%{ opacity: 0; transform: scale(0); }
                100%{ opacity: 1; transform: scale(1); }
            }
            @keyframes slideUp{
                from{ opacity: 0; transform: translateY(30px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes loading{
                0%{ width: 0; }
                100%{ width: 100%; }
            }

            @media (max-width: 500px) {
                .box { padding: 30px 20px; border-radius: 16px; max-width: 100%; }
                h1 { font-size: 1.6rem; }
                .success-icon { width: 80px; height: 80px; }
                .success-icon span { font-size: 40px; }
                .cliente-info { padding: 15px; }
                .cliente-info p { font-size: 1rem; }
            }
        </style>
        <script>
            setTimeout(() => {
                window.location.href = '../../telas/Cliente/principal.html';
            }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='success-icon'>
                <span>✓</span>
            </div>
            <h1>Cadastro Realizado!</h1>
            <div class='cliente-info'>
                <p>👤 " . htmlspecialchars($nome) . "</p>
                <small>Cliente cadastrado com sucesso</small>
            </div>
            <p>Redirecionando para o sistema...</p>
            <div class='loader'><span></span></div>
        </div>
    </body>
    </html>
    ";
} else {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Erro no Cadastro - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background: #111;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
                font-family: 'Segoe UI', Arial, sans-serif;
            }
            .box{
                background: #1e1e1e;
                padding: 40px 30px;
                border-radius: 20px;
                text-align: center;
                border: 2px solid #F23535;
                width: 100%;
                max-width: 480px;
                animation: fadeIn 0.5s ease;
                box-shadow: 0 15px 40px rgba(242, 53, 53, 0.15);
            }
            .error-icon{
                width: 90px;
                height: 90px;
                background: #F23535;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                animation: shake 0.6s ease;
                box-shadow: 0 0 30px rgba(242, 53, 53, 0.4);
            }
            .error-icon span{ font-size: 50px; color: white; font-weight: bold; }
            h1{ color: #ffffff; margin-bottom: 15px; font-size: 2rem; }
            p{ color: #bbbbbb; margin-bottom: 10px; font-size: 1.1rem; }
            .error-detail{
                background: #0f0f0f;
                padding: 12px 15px;
                border-radius: 10px;
                margin: 15px 0;
                color: #ff9800;
                font-size: 14px;
                word-break: break-all;
                border: 1px solid #333;
                text-align: left;
            }
            .btn-voltar{
                background: #F23535;
                color: white;
                border: none;
                padding: 16px 40px;
                border-radius: 50px;
                font-size: 1.1rem;
                font-weight: bold;
                cursor: pointer;
                margin-top: 25px;
                transition: all 0.3s;
                width: 100%;
                max-width: 280px;
                box-shadow: 0 6px 20px rgba(242, 53, 53, 0.4);
            }
            .btn-voltar:hover{ transform: scale(1.05); background: #d62c2c; }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-40px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes shake{
                0%,100%{ transform: translateX(0); }
                25%{ transform: translateX(-12px); }
                75%{ transform: translateX(12px); }
            }
            @media (max-width: 500px) {
                .box { padding: 30px 20px; border-radius: 16px; }
                h1 { font-size: 1.6rem; }
                .error-icon { width: 70px; height: 70px; }
                .error-icon span { font-size: 35px; }
                .btn-voltar { padding: 18px; font-size: 1rem; max-width: 100%; }
            }
        </style>
        <script>
            setTimeout(() => { window.history.back(); }, 4000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='error-icon'><span>✗</span></div>
            <h1>Erro no Cadastro!</h1>
            <p>Ocorreu um erro ao tentar cadastrar.</p>
            <div class='error-detail'>" . htmlspecialchars($erroMsg) . "</div>
            <p style='color:#666; font-size:0.9rem;'>Redirecionando em 4 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>🔄 Tentar novamente</button>
        </div>
    </body>
    </html>
    ";
}
?>