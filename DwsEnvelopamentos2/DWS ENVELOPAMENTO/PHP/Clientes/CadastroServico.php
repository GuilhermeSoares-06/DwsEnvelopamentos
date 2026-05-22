<?php
include("../Banco/conexao.php");

// VERIFICA CAMPOS
if (!isset($_POST['assunto']) || !isset($_POST['mensagem'])) {
    exit("<script>
        alert('Preencha todos os campos!');
        window.history.back();
    </script>");
}

$servico   = trim($_POST['assunto']);
$descricao = trim($_POST['mensagem']);

// VALIDAÇÃO EXTRA
if ($servico === "" || $descricao === "") {
    exit("<script>
        alert('Campos vazios!');
        window.history.back();
    </script>");
}

// PREPARA SQL
$stmt = $conn->prepare("
    INSERT INTO servicos (tipo_servico, serdescricao)
    VALUES (?, ?)
");

// ERRO DE PREPARE (IMPORTANTE)
if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

// BIND CORRETO
$stmt->bind_param("ss", $servico, $descricao);

// EXECUTA
if ($stmt->execute()) {

    // SUCESSO
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Sucesso</title>

        <style>
            body{
                margin:0;
                background:linear-gradient(135deg,#2b2b2b,#3b3b3b);
                display:flex;
                justify-content:center;
                align-items:center;
                height:100vh;
                font-family:Arial;
                color:white;
            }

            .box{
                text-align:center;
            }

            .check{
                width:120px;
                height:120px;
                border-radius:50%;
                border:6px solid #4CAF50;
                display:flex;
                justify-content:center;
                align-items:center;
                margin:auto;
                font-size:60px;
                color:#4CAF50;
            }

            h1{ margin-top:20px; }

            .loader{
                width:220px;
                height:6px;
                background:#555;
                margin:25px auto;
                border-radius:20px;
                overflow:hidden;
            }

            .loader span{
                display:block;
                height:100%;
                width:100%;
                background:#F23535;
                animation:load 2s linear forwards;
            }

            @keyframes load{
                from{width:0%;}
                to{width:100%;}
            }
        </style>

        <script>
            setTimeout(() => {
                window.location.href = '../../telas/contato.html';
            }, 2500);
        </script>
    </head>

    <body>
        <div class='box'>
            <div class='check'>✔</div>
            <h1>Pedido enviado com sucesso!</h1>
            <p>Redirecionando...</p>

            <div class='loader'><span></span></div>
        </div>
    </body>
    </html>
    ";

} else {

    // ERRO REAL DO BANCO
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro</title>

        <style>
            body{
                margin:0;
                background:#2b2b2b;
                display:flex;
                justify-content:center;
                align-items:center;
                height:100vh;
                font-family:Arial;
                color:white;
            }

            .box{ text-align:center; }

            .error{
                width:120px;
                height:120px;
                border-radius:50%;
                border:6px solid #F23535;
                display:flex;
                justify-content:center;
                align-items:center;
                margin:auto;
                font-size:60px;
                color:#F23535;
            }
        </style>

    </head>

    <body>
        <div class='box'>
            <div class='error'>✖</div>
            <h1>Erro ao salvar no banco</h1>
            <p>" . $stmt->error . "</p>
        </div>
    </body>
    </html>
    ";
}

$stmt->close();
$conn->close();
?>