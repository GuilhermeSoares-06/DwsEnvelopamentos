<?php
// =============================================
// verificar.php - Retorna dados do usuário logado
// GET → JSON { logado, usuario }
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../Auth/token.php';

$token = Token::doHeader() ?? Token::doCookie();

if (!$token) {
    echo json_encode(['logado' => false]);
    exit;
}

$data = Token::validar($token);

if (!$data) {
    // Cookie inválido/expirado - limpa
    setcookie('dws_token', '', ['expires' => time()-3600,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
    echo json_encode(['logado' => false]);
    exit;
}

echo json_encode([
    'logado'  => true,
    'usuario' => [
        'id'   => $data['id']   ?? null,
        'nome' => $data['nome'] ?? '',
        'tipo' => $data['tipo'] ?? 'cliente',
    ],
]);
?>
