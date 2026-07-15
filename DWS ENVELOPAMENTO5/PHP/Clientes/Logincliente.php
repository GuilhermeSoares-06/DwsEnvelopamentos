<?php
// =============================================
// LoginClientes.php  (NOVO ARQUIVO)
// Este arquivo não existia no projeto - o formulário de loginClientes.html
// apontava para ele, mas ao clicar em "Entrar" o navegador caía em uma
// página 404, então login nunca funcionou. Criado agora usando sessão
// nativa do PHP (session_start), sem depender de biblioteca de token/JWT.
// =============================================
session_start();
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

$nome  = trim($_POST['nome']  ?? '');
$senha = trim($_POST['senha'] ?? '');

if (!$nome || !$senha) {
    paginaErro('Campos incompletos', 'Preencha usuário e senha.');
}

// ATENÇÃO / LIMITAÇÃO CONHECIDA:
// O cadastro (CadastroCliente.php) não pede um "usuário" único, só o nome
// completo. Então aqui a busca é feita pelo nome, e se existir mais de um
// cliente cadastrado com o mesmo nome, o login usa o cadastro mais recente
// (ORDER BY cliid DESC). O ideal, no futuro, é ter uma coluna de usuário/e-mail
// única na tabela clientes - mas isso mudaria o formulário de cadastro também,
// então não fiz essa mudança sem sua confirmação.
$stmt = $pdo->prepare("SELECT cliid, clinome, clisenha FROM clientes WHERE clinome = :nome ORDER BY cliid DESC LIMIT 1");
$stmt->execute([':nome' => $nome]);
$cliente = $stmt->fetch();

if (!$cliente || empty($cliente['clisenha'])) {
    paginaErro('Login inválido', 'Usuário ou senha incorretos.');
}

$senhaConfere = false;

// password_hash() sempre gera um texto que começa com $2y$ (ou $2a$/$argon2...).
// Se o que está salvo no banco tem essa "cara" de hash, comparamos com password_verify.
if (password_get_info($cliente['clisenha'])['algo'] !== null) {
    $senhaConfere = password_verify($senha, $cliente['clisenha']);
} else {
    // Senha antiga, salva em texto puro (de antes do password_hash existir aqui,
    // ou inserida manualmente no banco). Comparamos direto e, se bater, já
    // convertemos pra hash agora, pra não ficar em texto puro no banco de novo.
    if (hash_equals($cliente['clisenha'], $senha)) {
        $senhaConfere = true;
        $novoHash = password_hash($senha, PASSWORD_DEFAULT);
        $upd = $pdo->prepare("UPDATE clientes SET clisenha = :h WHERE cliid = :id");
        $upd->execute([':h' => $novoHash, ':id' => $cliente['cliid']]);
    }
}

if (!$senhaConfere) {
    paginaErro('Login inválido', 'Usuário ou senha incorretos.');
}

// Login OK - cria a sessão
$_SESSION['cliid']   = (int)$cliente['cliid'];
$_SESSION['clinome'] = $cliente['clinome'];

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
        }
        .success-icon span{ font-size: 50px; color: white; }
        h1{ color: white; margin-bottom: 15px; }
        p{ color: #ccc; margin-bottom: 10px; }
        @keyframes fadeIn{
            from{ opacity: 0; transform: translateY(-30px); }
            to{ opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        setTimeout(() => {
            window.location.href = '../../telas/Cliente/principal.html';
        }, 1200);
    </script>
</head>
<body>
    <div class='box'>
        <div class='success-icon'><span>✓</span></div>
        <h1>Bem-vindo, " . htmlspecialchars($cliente['clinome']) . "!</h1>
        <p>Redirecionando...</p>
    </div>
</body>
</html>
";