<?php

include("../Banco/conexao.php");

// Verifica se os campos foram enviados
if (
    !isset($_POST['nome']) ||
    !isset($_POST['senha'])
) {

    echo "<script>
        alert('Preencha todos os campos!');
        window.history.back();
    </script>";

    exit();
}

// Recebe os dados
$nome  = trim($_POST['nome']);
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

// Tipo padrão
$tipo = 'adm';

// Verifica se usuário já existe
$check = $conn->prepare("
    SELECT usuid
    FROM usuarios
    WHERE usulogin = ?
");

$check->bind_param("s", $nome);
$check->execute();
$check->store_result();

// Se usuário existir
if ($check->num_rows > 0) {

    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
    <meta charset='UTF-8'>
    <title>Erro</title>

    <style>

    body{
        margin:0;
        padding:0;
        background:#3B3B3B;
        display:flex;
        justify-content:center;
        align-items:center;
        height:100vh;
        font-family:Arial, sans-serif;
    }

    .box{
        text-align:center;
        color:white;
    }

    .error{
        width:120px;
        height:120px;
        border-radius:50%;
        border:6px solid #F23535;
        display:flex;
        justify-content:center;
        align-items:center;
        margin:auto;
        animation:pop 0.5s ease;
    }

    .error::after{
        content:'✖';
        font-size:60px;
        color:#F23535;
    }

    h1{
        margin-top:25px;
        font-size:32px;
    }

    p{
        color:#dcdcdc;
        margin-top:10px;
        font-size:18px;
    }

    @keyframes pop{
        0%{
            transform:scale(0);
        }
        100%{
            transform:scale(1);
        }
    }

    </style>

    <script>
        setTimeout(function(){
            window.history.back();
        }, 2500);
    </script>

    </head>

    <body>

        <div class='box'>
            <div class='error'></div>
            <h1>Usuário já existe!</h1>
            <p>Voltando...</p>
        </div>

    </body>
    </html>
    ";

    $check->close();
    $conn->close();
    exit();
}

$check->close();

// Inserir usuário
$stmt = $conn->prepare("
    INSERT INTO usuarios
    (usulogin, ususenha, usu_tipo)
    VALUES (?, ?, ?)
");

$stmt->bind_param("sss", $nome, $senha, $tipo);

// Executa
if ($stmt->execute()) {

    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
    <meta charset='UTF-8'>
    <title>Cadastro realizado</title>

    <style>

    body{
        margin:0;
        padding:0;
        background:#3B3B3B;
        display:flex;
        justify-content:center;
        align-items:center;
        height:100vh;
        font-family:Arial, sans-serif;
        overflow:hidden;
    }

    .box{
        text-align:center;
        color:white;
        animation:fadeIn 1s ease;
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
        animation:pop 0.6s ease;
    }

    .check::after{
        content:'✔';
        font-size:60px;
        color:#4CAF50;
    }

    h1{
        margin-top:25px;
        font-size:32px;
    }

    p{
        color:#dcdcdc;
        margin-top:10px;
        font-size:18px;
    }

    .loader{
        width:220px;
        height:6px;
        background:#5a5a5a;
        margin:30px auto;
        border-radius:20px;
        overflow:hidden;
    }

    .loader span{
        display:block;
        height:100%;
        width:0%;
        background:#F23535;
        animation:loading 2.5s linear forwards;
    }

    @keyframes loading{
        from{
            width:0%;
        }
        to{
            width:100%;
        }
    }

    @keyframes pop{
        0%{
            transform:scale(0);
        }
        100%{
            transform:scale(1);
        }
    }

    @keyframes fadeIn{
        from{
            opacity:0;
        }
        to{
            opacity:1;
        }
    }

    </style>

    <script>

    setTimeout(function(){
        window.location.href='../../telas/principal.html';
    }, 2500);

    </script>

    </head>

    <body>

    <div class='box'>

        <div class='check'></div>

        <h1>Cadastro realizado!</h1>

        <p>Redirecionando para a página principal...</p>

        <div class='loader'>
            <span></span>
        </div>

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
    <title>Erro</title>

    <style>

    body{
        margin:0;
        padding:0;
        background:#3B3B3B;
        display:flex;
        justify-content:center;
        align-items:center;
        height:100vh;
        font-family:Arial, sans-serif;
    }

    .box{
        text-align:center;
        color:white;
    }

    .error{
        width:120px;
        height:120px;
        border-radius:50%;
        border:6px solid #F23535;
        display:flex;
        justify-content:center;
        align-items:center;
        margin:auto;
    }

    .error::after{
        content:'✖';
        font-size:60px;
        color:#F23535;
    }

    h1{
        margin-top:25px;
        font-size:32px;
    }

    p{
        color:#dcdcdc;
        margin-top:10px;
        font-size:18px;
    }

    </style>

    <script>
        setTimeout(function(){
            window.history.back();
        }, 3000);
    </script>

    </head>

    <body>

        <div class='box'>
            <div class='error'></div>
            <h1>Erro ao cadastrar!</h1>
            <p>Voltando...</p>
        </div>

    </body>
    </html>
    ";

    echo "Erro MYSQL: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>