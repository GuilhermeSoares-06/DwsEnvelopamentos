<?php
// =============================================
// logoutCliente.php - LOGOUT PARA CLIENTES
// =============================================

// Inicia a sessão
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destroi o cookie da sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroi a sessão
session_destroy();

// =============================================
// TELA DE LOGOUT COM ANIMAÇÃO
// =============================================
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saindo - DWS Envelopamento</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .box {
            background: #403E3F;
            padding: 50px;
            border-radius: 25px;
            text-align: center;
            border: 1px solid #F23535;
            animation: fadeIn 0.5s ease;
            max-width: 400px;
            width: 90%;
        }
        .logout-icon {
            width: 100px;
            height: 100px;
            background: #F23535;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 0.8s ease;
        }
        .logout-icon span {
            font-size: 50px;
            color: white;
        }
        h1 {
            color: white;
            margin-bottom: 10px;
            font-size: 24px;
        }
        p {
            color: #ccc;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .loader {
            width: 100%;
            height: 4px;
            background: #555;
            border-radius: 10px;
            margin: 25px auto 0;
            overflow: hidden;
        }
        .loader span {
            display: block;
            height: 100%;
            width: 100%;
            background: #F23535;
            animation: loading 2s forwards;
        }
        .btn-voltar {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #F23535;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: transform 0.3s;
        }
        .btn-voltar:hover {
            transform: scale(1.05);
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        @keyframes loading {
            0% {
                width: 0;
            }
            100% {
                width: 100%;
            }
        }
        @media (max-width: 480px) {
            .box {
                padding: 30px 20px;
            }
            .logout-icon {
                width: 80px;
                height: 80px;
            }
            .logout-icon span {
                font-size: 40px;
            }
            h1 {
                font-size: 20px;
            }
        }
    </style>
    <script>
        // Redireciona após 2 segundos
        setTimeout(() => {
            window.location.href = '../../telas/Cliente/principal.html';
        }, 2000);
    </script>
</head>
<body>
    <div class="box">
        <div class="logout-icon">
            <span>👋</span>
        </div>
        <h1>Até logo!</h1>
        <p>Você saiu da sua conta com sucesso.</p>
        <p style="font-size: 14px; color: #888;">Redirecionando para o início...</p>
        <div class="loader">
            <span></span>
        </div>
        <a href="../../telas/Cliente/principal.html" class="btn-voltar">Voltar agora</a>
    </div>
</body>
</html>
<?php
exit;
?>