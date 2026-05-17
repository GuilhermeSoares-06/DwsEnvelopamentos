<?php
include("conexao.php");

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

// Verificar se usuário já existe (usei 'id' como campo, não 'usuid')
$check = $conn->prepare("SELECT id FROM usuario WHERE nome = ?");
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

// Gerar hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir com prepared statement (tabela correta: usuario)
$stmt = $conn->prepare("INSERT INTO usuario (nome, senha_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $nome, $senha_hash);

if($stmt->execute()) {
    echo "<script>
        alert('Usuário cadastrado com sucesso!');
        window.location.href='../telas/login.html';
    </script>";
} else {
    echo "<script>
        alert('Erro ao cadastrar usuário: " . addslashes($stmt->error) . "');
        window.history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>