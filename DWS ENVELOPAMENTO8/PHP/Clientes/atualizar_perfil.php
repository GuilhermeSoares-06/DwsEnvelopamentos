<?php
// =============================================
// atualizar_perfil.php - Cliente atualiza o próprio perfil
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);


header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderErro('Método não permitido.', 405);
}
if (empty($_SESSION['cliid'])) {
    responderErro('Não autenticado', 401);
}
exigirCSRF();

$idSessao = (int)$_SESSION['cliid'];
$idPost   = (int)($_POST['id'] ?? 0);

if ($idSessao !== $idPost) {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado.']);
    exit;
}

$nome     = trim($_POST['nome'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$senha    = (string) ($_POST['senha'] ?? '');

if (empty($nome) || mb_strlen($nome) < 3) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Nome é obrigatório (mín. 3 letras).']);
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'E-mail inválido.']);
    exit;
}

if (mb_strlen($nome) > 255 || mb_strlen($email) > 150 || mb_strlen($endereco) > 255) {
    responderErro('Algum campo excede o tamanho máximo permitido.', 400);
}

if ($senha !== '' && (strlen($senha) < 8 || !preg_match('/[A-Za-z]/', $senha)
        || !preg_match('/[0-9]/', $senha) || !preg_match('/[^A-Za-z0-9]/', $senha))) {
    responderErro('A nova senha deve ter 8+ caracteres, com letra, número e caractere especial.', 400);
}

if ($email !== '') {
    $stmt = $pdo->prepare("SELECT cliid FROM clientes WHERE cliemail = :em AND cliid <> :id LIMIT 1");
    $stmt->execute([':em' => $email, ':id' => $idSessao]);
    if ($stmt->fetch()) responderErro('Este e-mail já está em uso por outra conta.', 409);
}

try {
    if (!empty($senha)) {
        // Atualiza tudo incluindo senha (hash)
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            UPDATE clientes 
            SET clinome = :n, cliemail = :em, clitel = :t, cliendereco = :e, clisenha = :s 
            WHERE cliid = :id
        ");
        $stmt->execute([
            ':n'  => $nome,
            ':em' => $email,
            ':t'  => $telefone,
            ':e'  => $endereco,
            ':s'  => $senhaHash,
            ':id' => $idSessao
        ]);
    } else {
        // Mantém senha atual
        $stmt = $pdo->prepare("
            UPDATE clientes 
            SET clinome = :n, cliemail = :em, clitel = :t, cliendereco = :e 
            WHERE cliid = :id
        ");
        $stmt->execute([
            ':n'  => $nome,
            ':em' => $email,
            ':t'  => $telefone,
            ':e'  => $endereco,
            ':id' => $idSessao
        ]);
    }

    // Atualiza sessão
    $_SESSION['clinome']     = $nome;
    $_SESSION['cliemail']    = $email;
    $_SESSION['clitel']      = $telefone;
    $_SESSION['cliendereco'] = $endereco;

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Perfil atualizado com sucesso!'
    ]);

} catch (PDOException $e) {
    error_log("Erro atualizar_perfil: " . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar perfil.']);
}