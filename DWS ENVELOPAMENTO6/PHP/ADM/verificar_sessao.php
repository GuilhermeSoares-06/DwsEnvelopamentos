<?php
session_start();

if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_nome'])) {
    echo json_encode([
        'logado' => true,
        'nome' => $_SESSION['admin_nome'],
        'id' => $_SESSION['admin_id']
    ]);
} else {
    echo json_encode(['logado' => false, 'session' => $_SESSION]);
}
?>