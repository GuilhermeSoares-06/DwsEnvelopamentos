<?php
// =============================================
// LoginClientes.php
// POST: nome + senha  →  JSON { status, token, usuario }
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../Banco/conexao.php';
require_once __DIR__ . '/../Auth/token.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'erro','mensagem'=>'Método não permitido.']);
    exit;
}

$nome  = trim($_POST['nome']  ?? '');
$senha = trim($_POST['senha'] ?? '');

if (!$nome || !$senha) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','mensagem'=>'Usuário e senha são obrigatórios.']);
    exit;
}

try {
    // CORREÇÃO: Usando 'clisenha' (nome correto da coluna no banco)
    $stmt = $pdo->prepare(
        "SELECT cliid, clinome, clicpf, clitel, cliendereco, clisenha, 
                COALESCE(tipocliente, 'cliente') AS tipocliente
         FROM clientes 
         WHERE clinome = :nome 
         LIMIT 1"
    );
    $stmt->execute([':nome' => $nome]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['status'=>'erro','mensagem'=>'Usuário não encontrado.']);
        exit;
    }

    // CORREÇÃO: Usando 'clisenha' que foi selecionada
    if ($user['clisenha'] !== $senha) {
        http_response_code(401);
        echo json_encode(['status'=>'erro','mensagem'=>'Senha incorreta.']);
        exit;
    }

    $token = Token::gerar([
        'id'    => $user['cliid'],
        'nome'  => $user['clinome'],
        'tipo'  => $user['tipocliente'],
    ]);

    // Cookie HttpOnly
    setcookie('dws_token', $token, [
        'expires'  => time() + 60 * 60 * 8,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    echo json_encode([
        'status'  => 'sucesso',
        'mensagem'=> 'Login realizado com sucesso!',
        'token'   => $token,
        'usuario' => [
            'id'    => $user['cliid'],
            'nome'  => $user['clinome'],
            'tipo'  => $user['tipocliente'],
        ],
    ]);

} catch (PDOException $e) {
    // Log do erro para debug
    error_log("Erro no login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status'=>'erro',
        'mensagem'=>'Erro interno no servidor: ' . $e->getMessage()
    ]);
}
?>