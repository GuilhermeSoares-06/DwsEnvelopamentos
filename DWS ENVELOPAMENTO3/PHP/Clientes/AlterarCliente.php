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
    $senha = $_POST['senha'];
    $cpf = mysqli_real_escape_string($conn, $_POST['cpf']);
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
    $endereco = mysqli_real_escape_string($conn, $_POST['endereco']);
    
    // Se a senha foi preenchida, atualiza com a nova senha
    if(!empty($senha)) {
        $stmt = $conn->prepare("UPDATE clientes SET clinome = ?, clisenha = ?, clicpf = ?, clitel = ?, cliendereco = ? WHERE cliid = ?");
        $stmt->bind_param("sssssi", $nome, $senha, $cpf, $telefone, $endereco, $id);
    } else {
        // Se a senha está vazia, mantém a senha atual
        $stmt = $conn->prepare("UPDATE clientes SET clinome = ?, clicpf = ?, clitel = ?, cliendereco = ? WHERE cliid = ?");
        $stmt->bind_param("ssssi", $nome, $cpf, $telefone, $endereco, $id);
    }
    
    if($stmt->execute()) {
        echo "
        <!DOCTYPE html>
        <html lang='pt-br'>
        <head>
            <meta charset='UTF-8'>
            <title>Atualizado - DWS</title>
            <style>
                *{ margin:0; padding:0; box-sizing:border-box; }
                body{
                    background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    font-family: 'Segoe UI', Arial, sans-serif;
                }
                .box{
                    background: #403E3F;
                    padding: 50px;
                    border-radius: 25px;
                    text-align: center;
                    border: 1px solid #4CAF50;
                    animation: fadeIn 0.6s ease;
                }
                .success-icon{
                    width: 100px;
                    height: 100px;
                    background: #4CAF50;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 20px;
                    animation: bounce 0.8s ease;
                }
                .success-icon span{ font-size: 50px; color: white; }
                h1{ color: white; margin-bottom: 15px; }
                p{ color: #ccc; margin-bottom: 10px; }
                .loader{
                    width: 100%;
                    height: 4px;
                    background: #555;
                    border-radius: 10px;
                    margin: 30px auto 0;
                    overflow: hidden;
                }
                .loader span{
                    display: block;
                    height: 100%;
                    width: 100%;
                    background: #F23535;
                    animation: loading 2s forwards;
                }
                @keyframes fadeIn{
                    from{ opacity: 0; transform: translateY(-30px); }
                    to{ opacity: 1; transform: translateY(0); }
                }
                @keyframes bounce{
                    0%,100%{ transform: scale(1); }
                    50%{ transform: scale(1.1); }
                }
                @keyframes loading{
                    0%{ width: 0; }
                    100%{ width: 100%; }
                }
            </style>
            <script>
                setTimeout(() => {
                    window.location.href = 'listar_clientes.php';
                }, 2000);
            </script>
        </head>
        <body>
            <div class='box'>
                <div class='success-icon'><span>✓</span></div>
                <h1>Cliente Atualizado!</h1>
                <p>Cliente: $nome</p>
                <p>Redirecionando...</p>
                <div class='loader'><span></span></div>
            </div>
        </body>
        </html>
        ";
    } else {
        echo "<script>
            alert('Erro ao atualizar: " . addslashes($stmt->error) . "');
            window.history.back();
        </script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Editar Cliente - DWS Envelopamento</title>
    <style>
        *{ margin:0; padding:0; box-sizing:border-box; }
        body{
            background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .container{
            background: #403E3F;
            padding: 40px;
            border-radius: 25px;
            border: 1px solid #F23535;
            width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease;
        }
        h1{
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        h1 i{
            color: #F23535;
            margin-right: 10px;
        }
        .input-box{
            margin-bottom: 20px;
        }
        .input-box input{
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #2b2b2b;
            color: white;
            font-size: 16px;
            outline: none;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .input-box input:focus{
            border: 1px solid #F23535;
            background: #1e1e1e;
        }
        .input-box input::placeholder{
            color: #888;
        }
        button{
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #F23535, #c91f2c);
            border: none;
            color: white;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        button:hover{
            transform: scale(1.02);
            box-shadow: 0 5px 20px rgba(242, 53, 53, 0.4);
        }
        .aviso-senha{
            color: #888;
            font-size: 12px;
            margin-top: -15px;
            margin-bottom: 15px;
            text-align: left;
        }
        @keyframes fadeIn{
            from{ opacity: 0; transform: translateY(-30px); }
            to{ opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1><i>✏️</i> Editar Cliente</h1>
        <form method='POST'>
            <input type='hidden' name='id' value='<?php echo $cliente['cliid']; ?>'>
            
            <div class='input-box'>
                <input type='text' name='nome' value='<?php echo htmlspecialchars($cliente['clinome']); ?>' placeholder="NOME COMPLETO" required>
            </div>
            
            <div class='input-box'>
                <input type='password' name='senha' placeholder="NOVA SENHA (deixe em branco para manter a atual)">
            </div>
            <div class='aviso-senha'>
                ⚠️ Se não quiser mudar a senha, deixe o campo em branco.
            </div>
            
            <div class='input-box'>
                <input type='text' name='cpf' value='<?php echo htmlspecialchars($cliente['clicpf']); ?>' placeholder="CPF" required>
            </div>
            
            <div class='input-box'>
                <input type='text' name='telefone' value='<?php echo htmlspecialchars($cliente['clitel']); ?>' placeholder="TELEFONE" required>
            </div>
            
            <div class='input-box'>
                <input type='text' name='endereco' value='<?php echo htmlspecialchars($cliente['cliendereco']); ?>' placeholder="ENDEREÇO" required>
            </div>
            
            <button type='submit'>ATUALIZAR CLIENTE</button>
        </form>
    </div>
</body>
</html>