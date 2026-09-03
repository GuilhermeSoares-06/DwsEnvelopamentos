<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'logado' => isset($_SESSION['cliid']),
    'cliid'  => $_SESSION['cliid']   ?? null,
    'nome'   => $_SESSION['clinome'] ?? null,
    'telefone' => $_SESSION['clitel'] ?? null,
    'cpf'      => $_SESSION['clicpf'] ?? null,
]);