<?php
// =============================================
// CadastroCliente.php - Backend do cadastro
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../telas/Cliente/CadastroCliente.php');
    exit;
}

// =============================================
// COLETA + SANITIZAÇÃO
// =============================================
$nome      = trim($_POST['nome'] ?? '');
$email     = trim($_POST['email'] ?? '');
$cpf       = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$telefone  = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
$cep       = preg_replace('/\D/', '', $_POST['cep'] ?? '');
$rua       = trim($_POST['rua'] ?? '');
$numero    = trim($_POST['numero'] ?? '');
$compl     = trim($_POST['complemento'] ?? '');
$bairro    = trim($_POST['bairro'] ?? '');
$cidade    = trim($_POST['cidade'] ?? '');
$estado    = strtoupper(trim($_POST['estado'] ?? ''));
$senha     = $_POST['Senha'] ?? '';
$senha2    = $_POST['Senha2'] ?? '';

// =============================================
// VALIDAÇÕES NO SERVIDOR (front não é segurança)
// =============================================
$erros = [];

// --- NOME ---
if ($nome === '') {
    $erros[] = 'Informe seu nome completo.';
} elseif (mb_strlen($nome) < 3) {
    $erros[] = 'Nome muito curto (mínimo 3 letras).';
} elseif (preg_match('/\d/', $nome)) {
    $erros[] = 'O nome não pode conter números.';
} elseif (!preg_match('/^[A-Za-zÀ-ÿ\s\'\-]+$/u', $nome)) {
    $erros[] = 'O nome contém caracteres inválidos.';
} elseif (!preg_match('/\s/', $nome)) {
    $erros[] = 'Digite nome e sobrenome.';
}

// --- EMAIL ---
if ($email === '') {
    $erros[] = 'Informe seu e-mail.';
} elseif (!validarEmail($email)) {
    $erros[] = 'E-mail inválido.';
} elseif (mb_strlen($email) > 150) {
    $erros[] = 'E-mail muito longo.';
}

// --- CPF ---
if ($cpf === '') {
    $erros[] = 'Informe seu CPF.';
} elseif (strlen($cpf) !== 11) {
    $erros[] = 'CPF deve ter 11 dígitos.';
} elseif (!validarCPF($cpf)) {
    $erros[] = 'CPF inválido.';
}

// --- TELEFONE ---
if ($telefone === '') {
    $erros[] = 'Informe seu telefone.';
} elseif (strlen($telefone) < 10 || strlen($telefone) > 11) {
    $erros[] = 'Telefone deve ter 10 ou 11 dígitos (com DDD).';
}

// --- ENDEREÇO ---
if ($cep !== '' && strlen($cep) !== 8) $erros[] = 'CEP inválido.';
if ($rua === '')    $erros[] = 'Informe a rua/avenida.';
if ($numero === '') $erros[] = 'Informe o número.';
if ($bairro === '') $erros[] = 'Informe o bairro.';
if ($cidade === '') $erros[] = 'Informe a cidade.';

$ufsValidas = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
if ($estado === '' || !in_array($estado, $ufsValidas, true)) {
    $erros[] = 'Estado (UF) inválido.';
}

// --- SENHA ---
if ($senha === '') {
    $erros[] = 'Informe uma senha.';
} elseif (strlen($senha) < 8) {
    $erros[] = 'A senha deve ter pelo menos 8 caracteres.';
} elseif (!preg_match('/[A-Z]/', $senha)) {
    $erros[] = 'A senha deve ter pelo menos 1 letra MAIÚSCULA.';
} elseif (!preg_match('/[a-z]/', $senha)) {
    $erros[] = 'A senha deve ter pelo menos 1 letra minúscula.';
} elseif (!preg_match('/[0-9]/', $senha)) {
    $erros[] = 'A senha deve ter pelo menos 1 número.';
} elseif (!preg_match('/[^A-Za-z0-9]/', $senha)) {
    $erros[] = 'A senha deve ter pelo menos 1 caractere especial (!@#$…).';
}
if ($senha !== $senha2) {
    $erros[] = 'As senhas não são iguais.';
}

if (!empty($erros)) {
    responderErro(implode(' ', $erros), 400, ['campos' => $erros]);
}

// =============================================
// MONTA ENDEREÇO (a tabela só tem cliendereco)
// =============================================
$partesEndereco = array_filter([
    $rua,
    $numero . ($compl ? ' - ' . $compl : ''),
    $bairro,
    $cidade . '/' . $estado,
    $cep ? 'CEP ' . $cep : ''
], fn($p) => $p !== '');
$enderecoCompleto = implode(', ', $partesEndereco);

// =============================================
// INSERE
// =============================================
try {
    // CPF único
    $stmt = $pdo->prepare("SELECT cliid FROM clientes WHERE clicpf = :cpf LIMIT 1");
    $stmt->execute([':cpf' => formatarCPF($cpf)]);
    if ($stmt->fetch()) {
        responderErro('Este CPF já está cadastrado.', 409);
    }

    // E-mail único (se a coluna existir)
    try {
        $stmt = $pdo->prepare("SELECT cliid FROM clientes WHERE cliemail = :em LIMIT 1");
        $stmt->execute([':em' => $email]);
        if ($stmt->fetch()) {
            responderErro('Este e-mail já está cadastrado.', 409);
        }
    } catch (PDOException $e) {
        error_log("Aviso: coluna cliemail não existe ou falhou: " . $e->getMessage());
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO clientes
            (clinome, clicpf, clitel, cliendereco, clisenha, tipocliente)
        VALUES
            (:nome, :cpf, :tel, :end, :senha, 'cliente')
    ");
    $stmt->execute([
        ':nome'  => $nome,
        ':cpf'   => formatarCPF($cpf),
        ':tel'   => formatarTelefone($telefone),
        ':end'   => $enderecoCompleto,
        ':senha' => $senhaHash,
    ]);

    $novoId = (int)$pdo->lastInsertId();

    // Salva e-mail separado (se a coluna existir)
    try {
        $stmt = $pdo->prepare("UPDATE clientes SET cliemail = :em WHERE cliid = :id");
        $stmt->execute([':em' => $email, ':id' => $novoId]);
    } catch (PDOException $e) {
        // ignora
    }

    logAuditoria('cliente_cadastrado', ['cliid' => $novoId, 'cpf' => formatarCPF($cpf)]);

    header('Location: ../../telas/Cliente/loginClientes.html?cadastro=ok');
    exit;

} catch (PDOException $e) {
    error_log("Erro CadastroCliente: " . $e->getMessage());
    responderErro('Erro ao criar conta. Tente novamente.', 500);
}