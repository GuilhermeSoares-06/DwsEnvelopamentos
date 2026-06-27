<?php
// =============================================
// CadastroCliente.php - Cadastro de cliente
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../Banco/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'erro','mensagem'=>'Método não permitido.']);
    exit;
}

$nome     = trim($_POST['nome']     ?? '');
$senha    = trim($_POST['Senha']    ?? $_POST['senha'] ?? '');
$cpf      = preg_replace('/\D/','',$_POST['cpf']      ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');

// ---- Validações ----
$erros = [];
if (!$nome)                              $erros[] = 'Nome é obrigatório.';
if (strlen($senha) < 4)                  $erros[] = 'Senha deve ter pelo menos 4 caracteres.';
if (strlen($cpf) !== 11)                 $erros[] = 'CPF inválido (informe 11 dígitos).';
if (!$telefone)                          $erros[] = 'Telefone é obrigatório.';
if (!$endereco)                          $erros[] = 'Endereço é obrigatório.';

if ($erros) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','mensagem'=>implode(' ', $erros)]);
    exit;
}

try {
    // Verifica CPF duplicado
    $chk = $pdo->prepare("SELECT cliid FROM clientes WHERE clicpf = :cpf LIMIT 1");
    $chk->execute([':cpf' => $cpf]);
    if ($chk->fetch()) {
        http_response_code(409);
        echo json_encode(['status'=>'erro','mensagem'=>'Já existe um cliente cadastrado com este CPF.']);
        exit;
    }

    // Insere — coluna senha mapeada como clisenha (ajuste se seu banco usar outro nome)
    $ins = $pdo->prepare(
        "INSERT INTO clientes (clinome, clisenha, clicpf, clitel, cliendereco, tipocliente)
         VALUES (:nome, :senha, :cpf, :tel, :end, 'cliente')"
    );
    $ins->execute([
        ':nome'  => $nome,
        ':senha' => $senha,   // Em produção use password_hash($senha, PASSWORD_DEFAULT)
        ':cpf'   => $cpf,
        ':tel'   => $telefone,
        ':end'   => $endereco,
    ]);

    echo json_encode([
        'status'   => 'sucesso',
        'mensagem' => "Cliente '$nome' cadastrado com sucesso!",
        'id'       => $pdo->lastInsertId(),
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Erro ao cadastrar. Tente novamente.']);
}
?>
