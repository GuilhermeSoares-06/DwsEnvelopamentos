<?php
include("../Banco/conexao.php");

// Verificar se os campos foram enviados
if(!isset($_POST['nome']) || !isset($_POST['senha'])) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro - DWS</title>
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
                max-width: 450px;
                animation: fadeIn 0.5s ease;
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
            .error-icon span{
                font-size: 50px;
                color: white;
            }
            h1{
                color: white;
                margin-bottom: 15px;
                font-size: 28px;
            }
            p{
                color: #ccc;
                margin-bottom: 10px;
            }
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
            .btn-voltar:hover{
                transform: scale(1.05);
                background: #c91f2c;
            }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-30px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes shake{
                0%, 100%{ transform: translateX(0); }
                25%{ transform: translateX(-10px); }
                75%{ transform: translateX(10px); }
            }
        </style>
        <script>
            setTimeout(() => {
                window.history.back();
            }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='error-icon'>
                <span>✗</span>
            </div>
            <h1>Campos Incompletos!</h1>
            <p>Preencha todos os campos do formulário.</p>
            <p>Redirecionando em 3 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>Voltar agora</button>
        </div>
    </body>
    </html>
    ";
    exit();
}

$nome = mysqli_real_escape_string($conn, $_POST['nome']);
$senha = $_POST['senha'];

// Verificar se usuário já existe
$check = $conn->prepare("SELECT usuid FROM usuarios WHERE usulogin = ?");
$check->bind_param("s", $nome);
$check->execute();
$check->store_result();

if($check->num_rows > 0) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Usuário Existente - DWS</title>
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
                max-width: 450px;
                animation: fadeIn 0.5s ease;
            }
            .warning-icon{
                width: 100px;
                height: 100px;
                background: #ff9800;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                animation: pulse 0.8s ease;
            }
            .warning-icon span{
                font-size: 50px;
                color: white;
            }
            h1{
                color: white;
                margin-bottom: 15px;
                font-size: 28px;
            }
            p{
                color: #ccc;
                margin-bottom: 10px;
            }
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
            .btn-voltar:hover{
                transform: scale(1.05);
                background: #c91f2c;
            }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-30px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes pulse{
                0%{ transform: scale(1); }
                50%{ transform: scale(1.1); }
                100%{ transform: scale(1); }
            }
        </style>
        <script>
            setTimeout(() => {
                window.history.back();
            }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='warning-icon'>
                <span>⚠</span>
            </div>
            <h1>Usuário já existe!</h1>
            <p>Este email já está cadastrado em nosso sistema.</p>
            <p>Tente outro email ou faça login.</p>
            <p>Redirecionando em 3 segundos...</p>
            <button class='btn-voltar' onclick='window.location.href=\"../../telas/cadastro.html\"'>Tentar novamente</button>
        </div>
    </body>
    </html>
    ";
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Inserir usuário com tipo 'adm'
$stmt = $conn->prepare("INSERT INTO usuarios (usulogin, ususenha, usutipo) VALUES (?, ?, 'adm')");
$stmt->bind_param("ss", $nome, $senha);

if($stmt->execute()) {
    // SUCESSO - ANIMAÇÃO BONITA
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Cadastro Realizado - DWS</title>
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
                max-width: 450px;
                animation: fadeIn 0.6s ease;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            }
            .success-icon{
                width: 120px;
                height: 120px;
                background: #4CAF50;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                animation: bounce 0.8s ease;
            }
            .success-icon span{
                font-size: 60px;
                color: white;
                animation: check 0.5s ease 0.3s both;
            }
            h1{
                color: white;
                margin-bottom: 15px;
                font-size: 28px;
                animation: slideUp 0.5s ease 0.2s both;
            }
            p{
                color: #ccc;
                margin-bottom: 10px;
                animation: slideUp 0.5s ease 0.4s both;
            }
            .user-email{
                background: #2b2b2b;
                padding: 10px;
                border-radius: 10px;
                margin: 20px 0;
                color: #F23535;
                font-weight: bold;
                animation: slideUp 0.5s ease 0.6s both;
            }
            .loader{
                width: 100%;
                height: 4px;
                background: #555;
                border-radius: 10px;
                margin: 30px auto 0;
                overflow: hidden;
                animation: slideUp 0.5s ease 0.8s both;
            }
            .loader span{
                display: block;
                height: 100%;
                width: 100%;
                background: #F23535;
                animation: loading 2s linear forwards;
            }
            .btn-login{
                background: #4CAF50;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 30px;
                font-size: 16px;
                cursor: pointer;
                margin-top: 20px;
                transition: transform 0.3s, background 0.3s;
                animation: slideUp 0.5s ease 1s both;
            }
            .btn-login:hover{
                transform: scale(1.05);
                background: #45a049;
            }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-50px) scale(0.9); }
                to{ opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes bounce{
                0%, 100%{ transform: scale(1); }
                50%{ transform: scale(1.2); }
            }
            @keyframes check{
                0%{ opacity: 0; transform: scale(0); }
                100%{ opacity: 1; transform: scale(1); }
            }
            @keyframes slideUp{
                from{ opacity: 0; transform: translateY(20px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes loading{
                0%{ width: 0; }
                100%{ width: 100%; }
            }
        </style>
        <script>
            setTimeout(() => {
                window.location.href = '../../telas/login.html';
            }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='success-icon'>
                <span>✓</span>
            </div>
            <h1>Cadastro Realizado!</h1>
            <p>Seu cadastro foi concluído com sucesso.</p>
            <div class='user-email'>
                📧 $nome
            </div>
            <p>Você será redirecionado para o login.</p>
            <div class='loader'><span></span></div>
            <button class='btn-login' onclick='window.location.href=\"../../telas/login.html\"'>Ir para o Login</button>
        </div>
    </body>
    </html>
    ";
} else {
    // ERRO NO BANCO
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro no Sistema - DWS</title>
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
                max-width: 450px;
                animation: fadeIn 0.5s ease;
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
            .error-icon span{
                font-size: 50px;
                color: white;
            }
            h1{
                color: white;
                margin-bottom: 15px;
                font-size: 28px;
            }
            p{
                color: #ccc;
                margin-bottom: 10px;
            }
            .error-detail{
                background: #2b2b2b;
                padding: 10px;
                border-radius: 10px;
                margin: 15px 0;
                color: #ff9800;
                font-size: 12px;
                word-break: break-all;
            }
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
            .btn-voltar:hover{
                transform: scale(1.05);
            }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-30px); }
                to{ opacity: 1; transform: translateY(0); }
            }
            @keyframes shake{
                0%, 100%{ transform: translateX(0); }
                25%{ transform: translateX(-8px); }
                75%{ transform: translateX(8px); }
            }
        </style>
        <script>
            setTimeout(() => {
                window.history.back();
            }, 4000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='error-icon'>
                <span>✗</span>
            </div>
            <h1>Erro no Cadastro!</h1>
            <p>Ocorreu um erro ao tentar cadastrar.</p>
            <div class='error-detail'>
                " . addslashes($stmt->error) . "
            </div>
            <p>Redirecionando em 4 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>Tentar novamente</button>
        </div>
    </body>
    </html>
    ";
}

$stmt->close();
$conn->close();
?>