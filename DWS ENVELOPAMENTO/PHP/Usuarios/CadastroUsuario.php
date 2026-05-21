<?php
include("../Banco/conexao.php");

// Verificar se os campos foram enviados
if(!isset($_POST['nome']) || !isset($_POST['senha'])) {
    echo "<script>
        alert('Preencha todos os campos!');
        window.history.back();
    </script>";
    exit();
}

$nome = mysqli_real_escape_string($conn, $_POST['nome']);
$senha = $_POST['senha'];

// Verificar se usuário já existe
$check = $conn->prepare("SELECT usuid FROM usuarios WHERE usulogin = ?");
$check->bind_param("s", $nome);
$check->execute();
$check->store_result();

if($check->num_rows > 0) {
    echo "<script>
        alert('Usuário já existe!');
        window.history.back();
    </script>";
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Inserir usuário (sem hash, senha em texto puro)
$stmt = $conn->prepare("INSERT INTO usuarios (usulogin, ususenha, usu_tipo) VALUES (?, ?,'?')");
$stmt->bind_param("ss", $nome, $senha);

if($stmt->execute()) {
    echo "<script>
        alert('Usuário cadastrado com sucesso!');
        window.location.href='../../telas/principal.html';
    </script>";
} else {
    echo "<script>
        alert('Erro ao cadastrar usuário!');
        window.history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>