<?php
include("../Banco/conexao.php");

// VERIFICA CAMPOS
if(!isset($_POST['nome']) || !isset($_POST['cpf']) || !isset($_POST['telefone']) || !isset($_POST['endereco'])) {
    echo "<script>
        alert('Preencha todos os campos!');
        window.history.back();
    </script>";
    exit();
}

$nome = mysqli_real_escape_string($conn, $_POST['nome']);
$cpf = mysqli_real_escape_string($conn, $_POST['cpf']);
$telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
$endereco = mysqli_real_escape_string($conn, $_POST['endereco']);

// VERIFICA SE CLIENTE JÁ EXISTE PELO CPF
$check = $conn->prepare("SELECT cliid FROM clientes WHERE clicpf = ?");
$check->bind_param("s", $cpf);
$check->execute();
$check->store_result();

if($check->num_rows > 0) {
    echo "<script>
        alert('Cliente com este CPF já existe!');
        window.history.back();
    </script>";
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// INSERE CLIENTE
$stmt = $conn->prepare("INSERT INTO clientes (clinome, clicpf, clitel, cliendereco) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $cpf, $telefone, $endereco);

if($stmt->execute()) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Cadastro Realizado - DWS</title>
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
            <h1>Cliente Cadastrado!</h1>
            <p>Cliente: $nome</p>
            <p>Redirecionando...</p>
            <div class='loader'><span></span></div>
        </div>
    </body>
    </html>
    ";
} else {
    echo "<script>
        alert('Erro ao cadastrar cliente: " . addslashes($stmt->error) . "');
        window.history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>