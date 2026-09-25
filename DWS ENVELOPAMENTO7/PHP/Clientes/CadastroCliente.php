<?php
// =============================================
// CadastroCliente.php - Cadastro de clientes
// =============================================
include(__DIR__ . "/../Banco/conexao.php");
include(__DIR__ . "/validacao.php");

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
                background:#111; display:flex; justify-content:center; align-items:center;
                min-height:100vh; padding:20px; font-family:'Segoe UI',Arial,sans-serif;
            }
            .box{
                background:#1e1e1e; padding:40px 30px; border-radius:20px;
                text-align:center; border:2px solid #F23535;
                width:100%; max-width:480px; box-shadow:0 15px 40px rgba(0,0,0,0.8);
            }
            .error-icon{
                width:90px; height:90px; background:#F23535; border-radius:50%;
                display:flex; align-items:center; justify-content:center; margin:0 auto 20px;
            }
            .error-icon span{ font-size:50px; color:white; font-weight:bold; }
            h1{ color:#fff; margin-bottom:15px; font-size:2rem; }
            p{ color:#bbb; margin-bottom:10px; font-size:1.1rem; line-height:1.5; }
            .btn-voltar{
                background:#F23535; color:white; border:none; padding:16px 40px;
                border-radius:50px; font-size:1.1rem; font-weight:bold;
                cursor:pointer; margin-top:25px; width:100%; max-width:280px;
                transition:all 0.3s; box-shadow:0 6px 20px rgba(242,53,53,0.4);
            }
            .btn-voltar:hover{ transform:translateY(-3px); background:#d62c2c; }
        </style>
        <script>setTimeout(() => { window.history.back(); }, 4000);</script>
    </head>
    <body>
        <div class='box'>
            <div class='error-icon'><span>✗</span></div>
            <h1>$titulo</h1>
            <p>$mensagem</p>
            <p style='color:#666; font-size:0.9rem;'>Redirecionando...</p>
            <button class='btn-voltar' onclick='window.history.back()'>🔄 Voltar agora</button>
        </div>
    </body>
    </html>
    ";
    exit();
}

// Verifica campos
if (!isset($_POST['nome']) || !isset($_POST['email']) || !isset($_POST['Senha'])
    || !isset($_POST['cpf']) || !isset($_POST['telefone']) || !isset($_POST['cep'])) {
    paginaErroCadastro('Campos Incompletos', 'Preencha todos os campos do formulário.');
}

$nome        = trim($_POST['nome']);
$email       = trim($_POST['email']);
$senha       = $_POST['Senha'];
$cpf         = preg_replace('/\D/', '', $_POST['cpf']);
$telefone    = trim($_POST['telefone']);
$cep         = preg_replace('/\D/', '', $_POST['cep']);
$rua         = trim($_POST['rua'] ?? '');
$numero      = trim($_POST['numero'] ?? '');
$complemento = trim($_POST['complemento'] ?? '');
$bairro      = trim($_POST['bairro'] ?? '');
$cidade      = trim($_POST['cidade'] ?? '');
$estado      = strtoupper(trim($_POST['estado'] ?? ''));

// =============================================
// VALIDAÇÕES
// =============================================

// Nome
if (mb_strlen($nome) < 3) {
    paginaErroCadastro('Nome Inválido', 'O nome precisa ter no mínimo 3 caracteres.');
}

// E-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    paginaErroCadastro('E-mail Inválido', 'O e-mail informado não é válido.');
}

// CPF
if ($cpf === '') {
    paginaErroCadastro('CPF Obrigatório', 'Informe um CPF para concluir o cadastro.');
}
if (!validarCPF($cpf)) {
    paginaErroCadastro('CPF Inválido', 'O CPF informado não é válido.');
}

// Senha forte
$temMaiuscula = preg_match('/[A-Z]/', $senha);
$temMinuscula = preg_match('/[a-z]/', $senha);
$temNumero    = preg_match('/[0-9]/', $senha);
$temEspecial  = preg_match('/[^A-Za-z0-9]/', $senha);
$temTamanho   = strlen($senha) >= 8;

if (!$temMaiuscula || !$temMinuscula || !$temNumero || !$temEspecial || !$temTamanho) {
    paginaErroCadastro(
        'Senha Fraca',
        'A senha precisa ter: letra maiúscula, letra minúscula, número, caractere especial e no mínimo 8 caracteres.'
    );
}

// Confirma senha
$senha2 = $_POST['Senha2'] ?? '';
if ($senha !== $senha2) {
    paginaErroCadastro('Senhas diferentes', 'As duas senhas precisam ser iguais.');
}

// Endereço
if (strlen($cep) !== 8) {
    paginaErroCadastro('CEP Inválido', 'O CEP precisa ter 8 dígitos.');
}
if (empty($rua) || empty($numero) || empty($bairro) || empty($cidade) || empty($estado)) {
    paginaErroCadastro('Endereço Incompleto', 'Preencha todos os campos do endereço.');
}
if (strlen($estado) !== 2) {
    paginaErroCadastro('Estado Inválido', 'Informe a sigla do estado com 2 letras (ex: SP).');
}

// Monta endereço completo
$endereco_completo = $rua . ', ' . $numero;
if (!empty($complemento)) $endereco_completo .= ' - ' . $complemento;
$endereco_completo .= ' - ' . $bairro;
$endereco_completo .= ', ' . $cidade . ' - ' . $estado;
$endereco_completo .= ' - CEP ' . $cep;

// =============================================
// VERIFICA DUPLICADOS
// =============================================
$check = $pdo->prepare("SELECT cliid FROM clientes WHERE clicpf = :cpf");
$check->execute([':cpf' => $cpf]);
if ($check->fetch()) {
    paginaErroCadastro('CPF já cadastrado', 'Já existe um cliente com este CPF.');
}

$check = $pdo->prepare("SELECT cliid FROM clientes WHERE cliemail = :email");
$check->execute([':email' => $email]);
if ($check->fetch()) {
    paginaErroCadastro('E-mail já cadastrado', 'Já existe um cliente com este e-mail.');
}

// =============================================
// INSERE
// =============================================
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO clientes (clinome, clisenha, clicpf, clitel, cliendereco, cliemail, tipocliente) 
        VALUES (:n, :s, :c, :t, :e, :em, 'cliente')
    ");
    $sucesso = $stmt->execute([
        ':n'  => $nome,
        ':s'  => $senhaHash,
        ':c'  => $cpf,
        ':t'  => $telefone,
        ':e'  => $endereco_completo,
        ':em' => $email
    ]);
} catch (PDOException $e) {
    error_log("Erro cadastro: " . $e->getMessage());
    $sucesso = false;
    $erroMsg = $e->getMessage();
}

if ($sucesso) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Cadastro Realizado - DWS</title>
        <style>
            *{ margin:0; padding:0; box-sizing:border-box; }
            body{
                background:#111; display:flex; justify-content:center; align-items:center;
                min-height:100vh; padding:20px; font-family:'Segoe UI',Arial,sans-serif;
            }
            .box{
                background:#1e1e1e; padding:45px 35px; border-radius:20px;
                text-align:center; border:2px solid #4CAF50;
                width:100%; max-width:480px;
                box-shadow:0 15px 50px rgba(76,175,80,0.2);
            }
            .success-icon{
                width:100px; height:100px; background:#4CAF50; border-radius:50%;
                display:flex; align-items:center; justify-content:center; margin:0 auto 20px;
            }
            .success-icon span{ font-size:55px; color:white; font-weight:bold; }
            h1{ color:#fff; margin-bottom:20px; font-size:2rem; }
            .info{
                background:#0f0f0f; padding:20px; border-radius:12px; margin:20px 0;
                border:1px solid #333;
            }
            .info p{ color:#4CAF50; font-weight:bold; font-size:1.2rem; margin:5px 0; }
            .info small{ color:#aaa; font-size:13px; }
            p{ color:#bbb; margin-bottom:10px; font-size:1.1rem; }
            .loader{
                width:100%; height:5px; background:#333; border-radius:10px;
                margin:30px auto 0; overflow:hidden;
            }
            .loader span{
                display:block; height:100%; width:100%; background:#4CAF50;
                animation:loading 2.5s linear forwards;
            }
            @keyframes loading{ 0%{ width:0; } 100%{ width:100%; } }
        </style>
        <script>
            setTimeout(() => {
                window.location.href = '../../telas/Cliente/loginClientes.html';
            }, 3000);
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='success-icon'><span>✓</span></div>
            <h1>Cadastro Realizado!</h1>
            <div class='info'>
                <p>👤 " . htmlspecialchars($nome) . "</p>
                <small>Cliente cadastrado com sucesso</small>
            </div>
            <p>Redirecionando para o login...</p>
            <div class='loader'><span></span></div>
        </div>
    </body>
    </html>
    ";
} else {
    paginaErroCadastro('Erro no Cadastro', 'Ocorreu um erro: ' . htmlspecialchars($erroMsg));
}