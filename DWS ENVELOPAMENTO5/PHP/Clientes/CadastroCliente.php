<?php
include("../Banco/conexao.php");
include("../Utils/validacao.php");

function paginaErroCadastro($titulo, $mensagem) {
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
            }
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-30px); }
                to{ opacity: 1; transform: translateY(0); }
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

// VERIFICA CAMPOS
if(!isset($_POST['nome']) || !isset($_POST['Senha']) || !isset($_POST['cpf']) || !isset($_POST['telefone']) || !isset($_POST['endereco'])) {
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

// IMPORTANTE: aqui usamos prepared statements (bind_param), então NAO se deve
// escapar manualmente com mysqli_real_escape_string antes - isso gravava
// barras invertidas extras no nome/endereco de quem tinha apostrofo, aspas etc.
$nome     = trim($_POST['nome']);
$senha    = password_hash($_POST['Senha'], PASSWORD_DEFAULT); 
$cpf      = preg_replace('/\D/', '', $_POST['cpf']);          
$telefone = trim($_POST['telefone']);
$endereco = trim($_POST['endereco']);

if (!validarCPF($cpf)) {
    paginaErroCadastro('CPF inválido', 'O CPF informado não é válido. Confira os números e tente novamente.');
}

// VERIFICA SE CLIENTE JA EXISTE PELO CPF
// (convertido para PDO: o conexao.php do projeto só cria $pdo, não existe $conn/mysqli)
$check = $pdo->prepare("SELECT cliid FROM clientes WHERE clicpf = :cpf");
$check->execute([':cpf' => $cpf]);
$clienteExistente = $check->fetch();

if ($clienteExistente) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Cliente Existente - DWS</title>
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
                border: 1px solid #ff9800;
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
            .warning-icon span{ font-size: 50px; color: white; }
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
            @keyframes pulse{
                0%,100%{ transform: scale(1); }
                50%{ transform: scale(1.1); }
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
            <p>Redirecionando em 3 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>Voltar agora</button>
        </div>
    </body>
    </html>
    ";
    exit();
}

// TRATAMENTO DA FOTO DE PERFIL (opcional)
// O formulário CadastroCliente.html envia enctype="multipart/form-data" com name="foto",
// mas esse arquivo nunca salvava a imagem - ela era descartada. Agora salvamos em
// /uploads/clientes/ e guardamos o caminho na coluna clifoto (se ela existir na tabela).
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

// INSERE CLIENTE (nome da coluna de senha corrigido para "clisenha",
// que é o mesmo nome usado em AlterarCliente.php e ListarCliente.php -
// antes estava gravando em "senha", o que quebrava login/edição do cliente)
$sucesso = false;
$erroMsg = '';
try {
    if ($clifoto !== null) {
        $stmt = $pdo->prepare("INSERT INTO clientes (clinome, clisenha, clicpf, clitel, cliendereco, clifoto, tipocliente) VALUES (:n, :s, :c, :t, :e, :f, 'cliente')");
        $sucesso = $stmt->execute([
            ':n' => $nome, ':s' => $senha, ':c' => $cpf, ':t' => $telefone, ':e' => $endereco, ':f' => $clifoto
        ]);
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
    // SUCESSO COM ANIMAÇÃO BONITA
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
            .cliente-info{
                background: #2b2b2b;
                padding: 15px;
                border-radius: 10px;
                margin: 20px 0;
                animation: slideUp 0.5s ease 0.4s both;
            }
            .cliente-info p{
                color: #F23535;
                font-weight: bold;
                margin: 5px 0;
            }
            .cliente-info small{
                color: #aaa;
                font-size: 12px;
            }
            p{
                color: #ccc;
                margin-bottom: 10px;
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
            @keyframes fadeIn{
                from{ opacity: 0; transform: translateY(-50px) scale(0.9); }
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
                <p>👤 $nome</p>
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
        <title>Erro no Cadastro - DWS</title>
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
            .error-icon span{ font-size: 50px; color: white; }
            h1{ color: white; margin-bottom: 15px; }
            p{ color: #ccc; margin-bottom: 10px; }
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
            setTimeout(() => { window.history.back(); }, 4000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='error-icon'><span>✗</span></div>
            <h1>Erro no Cadastro!</h1>
            <p>Ocorreu um erro ao tentar cadastrar.</p>
            <div class='error-detail'>" . htmlspecialchars($erroMsg) . "</div>
            <p>Redirecionando em 4 segundos...</p>
            <button class='btn-voltar' onclick='window.history.back()'>Tentar novamente</button>
        </div>
    </body>
    </html>
    ";
}
?>