<?php

// Dados do banco
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "dws";

// Criando conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificando conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Definindo UTF-8
$conn->set_charset("utf8");

?>