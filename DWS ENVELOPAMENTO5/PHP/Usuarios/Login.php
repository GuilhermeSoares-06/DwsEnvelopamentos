<?php
// =============================================
// Login.php  - Login do Funcionário/ADM
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
    $stmt = $pdo->prepare(
        "SELECT cliid, clinome, COALESCE(clisenha, senha) AS clisenha,
                COALESCE(tipocliente, 'funcionario') AS tipocliente
         FROM clientes
         WHERE clinome = :nome
           AND COALESCE(tipocliente,'cliente') = 'funcionario'
         LIMIT 1"
    );
    $stmt->execute([':nome' => $nome]);
    $user = $stmt->fetch();

    if (!$user || $user['clisenha'] !== $senha) {
        http_response_code(401);
        echo json_encode(['status'=>'erro','mensagem'=>'Credenciais inválidas ou sem permissão de acesso.']);
        exit;
    }

    $token = Token::gerar([
        'id'   => $user['cliid'],
        'nome' => $user['clinome'],
        'tipo' => 'funcionario',
    ]);

    setcookie('dws_token', $token, [
        'expires'  => time() + 60 * 60 * 8,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    echo json_encode([
        'status'  => 'sucesso',
        'mensagem'=> 'Login de funcionário realizado!',
        'token'   => $token,
        'usuario' => [
            'id'   => $user['cliid'],
            'nome' => $user['clinome'],
            'tipo' => 'funcionario',
        ],
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Erro interno no servidor.']);
}
?>
