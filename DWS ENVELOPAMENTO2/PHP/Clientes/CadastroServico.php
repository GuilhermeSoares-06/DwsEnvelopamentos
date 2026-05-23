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

// VALIDAÇÃO
if ($servico === "" || $descricao === "") {
    exit("<script>
        alert('Campos vazios!');
        window.history.back();
    </script>");
}

// CLIENTE PADRÃO (ID 1)
$cliid_padrao = 1;

// VERIFICA SE O CLIENTE PADRÃO EXISTE
$check_cliente = $conn->prepare("SELECT cliid FROM clientes WHERE cliid = ?");
$check_cliente->bind_param("i", $cliid_padrao);
$check_cliente->execute();
$check_cliente->store_result();

if($check_cliente->num_rows == 0) {
    // CRIA CLIENTE PADRÃO
    $insert_cliente = $conn->prepare("INSERT INTO clientes (cliid, clinome, clitel, cliendereco) VALUES (?, 'Cliente Site', '00000000000', 'Site')");
    $insert_cliente->bind_param("i", $cliid_padrao);
    $insert_cliente->execute();
    $insert_cliente->close();
}
$check_cliente->close();

// PREPARA SQL
$stmt = $conn->prepare("
    INSERT INTO servicos (cliid, tipo_servico, serdescricao, servalor, serdata_servico) 
    VALUES (?, ?, ?, 'Aguardando orçamento', NOW())
");

if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

$stmt->bind_param("iss", $cliid_padrao, $servico, $descricao);

// EXECUTA
if ($stmt->execute()) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Sucesso - DWS</title>
        <style>
            body{
                background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: Arial;
            }
            .box{
                background: #403E3F;
                padding: 50px;
                border-radius: 25px;
                text-align: center;
                border: 1px solid #F23535;
            }
            .check{
                width: 100px;
                height: 100px;
                background: #4CAF50;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                font-size: 50px;
                color: white;
            }
            h1{ color: white; }
            p{ color: #ccc; }
            .loader{
                width: 100%;
                height: 5px;
                background: #555;
                border-radius: 10px;
                margin: 30px auto 0;
                overflow: hidden;
            }
            .loader span{
                display: block;
                height: 100%;
                width: 100%;
                background: #F23535;
                animation: loading 2s forwards;
            }
            @keyframes loading{
                0%{ width: 0; }
                100%{ width: 100%; }
            }
        </style>
        <script>
            setTimeout(() => {
                window.location.href = '../../telas/principal.html';
            }, 2000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='check'>✓</div>
            <h1>Pedido Enviado!</h1>
            <p>Seu pedido foi registrado com sucesso.</p>
            <p>Redirecionando...</p>
            <div class='loader'><span></span></div>
        </div>
    </body>
    </html>
    ";
} else {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Erro - DWS</title>
        <style>
            body{
                background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: Arial;
            }
            .box{
                background: #403E3F;
                padding: 50px;
                border-radius: 25px;
                text-align: center;
                border: 1px solid #F23535;
            }
            .error{
                width: 100px;
                height: 100px;
                background: #F23535;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                font-size: 50px;
                color: white;
            }
            h1{ color: white; }
            p{ color: #ccc; }
        </style>
        <script>
            setTimeout(() => {
                window.history.back();
            }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='error'>✗</div>
            <h1>Erro ao enviar</h1>
            <p>" . $stmt->error . "</p>
            <p>Voltando...</p>
        </div>
    </body>
    </html>
    ";
}

$stmt->close();
$conn->close();
?>