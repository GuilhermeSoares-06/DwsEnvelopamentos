<?php
// =============================================
// Logout.php - Invalida o cookie de sessão
// =============================================
setcookie('dws_token', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['status'=>'sucesso','mensagem'=>'Logout realizado.']);
?>
