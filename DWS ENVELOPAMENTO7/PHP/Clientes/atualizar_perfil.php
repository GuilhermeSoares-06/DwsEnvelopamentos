<?php
// =============================================
// atualizar_perfil.php - Cliente atualiza o próprio perfil
// =============================================
include("../Banco/conexao.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['cliid'])) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não autenticado']);
    exit;
}

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
$senha    = trim($_POST['senha'] ?? '');

if (empty($nome) || mb_strlen($nome) < 3) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Nome é obrigatório (mín. 3 letras).']);
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'E-mail inválido.']);
    exit;
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