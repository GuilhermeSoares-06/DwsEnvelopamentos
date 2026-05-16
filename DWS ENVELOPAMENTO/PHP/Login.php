<?php
session_start();
include("conexao.php");

$nome = mysqli_real_escape_string($conn, $_POST['nome']);
$senha = $_POST['senha'];

// Buscar usuário no banco
$stmt = $conn->prepare("SELECT usuid, usulogin, ususenha FROM usuarios WHERE usulogin = ?");
$stmt->bind_param("s", $nome);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 1) {

    $usuario = $result->fetch_assoc();

    // Validando senha
    if($senha == $usuario['ususenha']) {

        $_SESSION['usuario_id'] = $usuario['usuid'];
        $_SESSION['usulogin'] = $usuario['usulogin'];

        echo "
        <html>

        <head>
        <title>Bem-vindo</title>

        <style>

        body{
            margin:0;
            padding:0;
            background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            font-family:Arial;
        }

        .box{
            background:#808080;
            padding:50px;
            border-radius:25px;
            text-align:center;
            box-shadow:0 0 20px rgba(0,0,0,0.5);
            animation: aparecer 1s ease;
        }

        h1{
            color:white;
            font-size:40px;
            margin-bottom:20px;
        }

        p{
            color:#f0f0f0;
            font-size:22px;
        }

        .loader{
            margin:20px auto;
            border:6px solid #ccc;
            border-top:6px solid #F23535;
            border-radius:50%;
            width:50px;
            height:50px;
            animation: girar 1s linear infinite;
        }

        @keyframes girar{
            100%{
                transform: rotate(360deg);
            }
        }

        @keyframes aparecer{
            from{
                opacity:0;
                transform:scale(0.8);
            }

            to{
                opacity:1;
                transform:scale(1);
            }
        }

        </style>

        <script>

        setTimeout(function(){
            window.location.href='../telas/principal.html';
        },3000);

        </script>

        </head>

        <body>

            <div class='box'>

                <h1>Bem-vindo!</h1>

                <p>
                    " . $usuario['usulogin'] . "
                </p>

                <div class='loader'></div>

                <p>Entrando no sistema...</p>

            </div>

        </body>

        </html>
        ";

    } else {

        echo "
        <script>
            alert('Senha incorreta!');
            window.history.back();
        </script>
        ";

    }

} else {

    echo "
    <script>
        alert('Usuário não encontrado!');
        window.history.back();
    </script>
    ";

}

$stmt->close();
$conn->close();
?>