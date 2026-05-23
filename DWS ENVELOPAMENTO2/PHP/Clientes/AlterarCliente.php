<?php
include("../Banco/conexao.php");

// BUSCAR CLIENTE PARA EDITAR
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM clientes WHERE cliid = $id";
    $result = $conn->query($sql);
    $cliente = $result->fetch_assoc();
}

// ATUALIZAR CLIENTE
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $cpf = mysqli_real_escape_string($conn, $_POST['cpf']);
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
    $endereco = mysqli_real_escape_string($conn, $_POST['endereco']);
    
    $stmt = $conn->prepare("UPDATE clientes SET clinome = ?, clicpf = ?, clitel = ?, cliendereco = ? WHERE cliid = ?");
    $stmt->bind_param("ssssi", $nome, $cpf, $telefone, $endereco, $id);
    
    if($stmt->execute()) {
        echo "<script>alert('Cliente atualizado com sucesso!'); window.location.href='listar_clientes.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Editar Cliente - DWS</title>
    <style>
        body{
            background: #3b3b3b;
            font-family: Arial;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container{
            background: #403E3F;
            padding: 40px;
            border-radius: 25px;
            border: 1px solid #F23535;
            width: 500px;
        }
        h1{
            color: white;
            text-align: center;
        }
        input{
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: none;
            border-radius: 10px;
            background: #2b2b2b;
            color: white;
        }
        button{
            width: 100%;
            padding: 12px;
            background: #F23535;
            border: none;
            color: white;
            border-radius: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Editar Cliente</h1>
        <form method='POST'>
            <input type='hidden' name='id' value='<?php echo $cliente['cliid']; ?>'>
            <input type='text' name='nome' value='<?php echo $cliente['clinome']; ?>' required>
            <input type='text' name='cpf' value='<?php echo $cliente['clicpf']; ?>' required>
            <input type='text' name='telefone' value='<?php echo $cliente['clitel']; ?>' required>
            <input type='text' name='endereco' value='<?php echo $cliente['cliendereco']; ?>' required>
            <button type='submit'>ATUALIZAR</button>
        </form>
    </div>
</body>
</html>