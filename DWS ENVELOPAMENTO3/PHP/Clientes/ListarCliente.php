<?php
include("../Banco/conexao.php");

// DELETAR CLIENTE
if(isset($_GET['deletar'])) {
    $id = intval($_GET['deletar']);
    $stmt = $conn->prepare("DELETE FROM clientes WHERE cliid = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        echo "<script>alert('Cliente deletado com sucesso!'); window.location.href='listar_clientes.php';</script>";
    } else {
        echo "<script>alert('Erro ao deletar cliente!');</script>";
    }
    $stmt->close();
}

// ATUALIZAR CLIENTE (via POST do formulário)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'editar') {
    $id = $_POST['id'];
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $senha = $_POST['senha'];
    $cpf = mysqli_real_escape_string($conn, $_POST['cpf']);
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
    $endereco = mysqli_real_escape_string($conn, $_POST['endereco']);
    
    if(!empty($senha)) {
        $stmt = $conn->prepare("UPDATE clientes SET clinome = ?, senha = ?, clicpf = ?, clitel = ?, cliendereco = ? WHERE cliid = ?");
        $stmt->bind_param("sssssi", $nome, $senha, $cpf, $telefone, $endereco, $id);
    } else {
        $stmt = $conn->prepare("UPDATE clientes SET clinome = ?, clicpf = ?, clitel = ?, cliendereco = ? WHERE cliid = ?");
        $stmt->bind_param("ssssi", $nome, $cpf, $telefone, $endereco, $id);
    }
    
    if($stmt->execute()) {
        echo "<script>alert('Cliente atualizado com sucesso!'); window.location.href='listar_clientes.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}

// BUSCAR TODOS OS CLIENTES
$sql = "SELECT cliid, clinome, senha, clicpf, clitel, cliendereco FROM clientes ORDER BY cliid DESC";
$result = $conn->query($sql);

// BUSCAR CLIENTE PARA EDITAR (se tiver id na URL)
$cliente_editar = null;
if(isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $sql_editar = "SELECT * FROM clientes WHERE cliid = $id_editar";
    $result_editar = $conn->query($sql_editar);
    $cliente_editar = $result_editar->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Clientes - DWS Envelopamento</title>
    <style>
        *{ margin:0; padding:0; box-sizing:border-box; }
        body{
            background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 40px;
            min-height: 100vh;
        }
        .container{
            max-width: 1400px;
            margin: 0 auto;
            background: #403E3F;
            padding: 30px;
            border-radius: 25px;
            border: 1px solid #F23535;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease;
        }
        h1{
            color: white;
            text-align: center;
            margin-bottom: 10px;
            font-size: 32px;
        }
        h1 i{
            color: #F23535;
            margin-right: 10px;
        }
        .subtitulo{
            text-align: center;
            color: #aaa;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .total-clientes{
            text-align: right;
            color: #aaa;
            margin-bottom: 20px;
            padding: 10px;
            background: #2b2b2b;
            border-radius: 10px;
        }
        .total-clientes span{
            color: #F23535;
            font-weight: bold;
            font-size: 18px;
        }
        .table-wrapper{
            overflow-x: auto;
            border-radius: 15px;
        }
        table{
            width: 100%;
            border-collapse: collapse;
            color: white;
            min-width: 700px;
        }
        th{
            background: #F23535;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }
        td{
            padding: 12px 15px;
            border-bottom: 1px solid #555;
        }
        tr:hover{
            background: #4a4a4a;
            transition: 0.3s;
        }
        .btn-editar{
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 8px;
            margin-right: 5px;
            transition: all 0.2s;
            display: inline-block;
        }
        .btn-editar:hover{
            transform: scale(1.05);
            background: #45a049;
        }
        .btn-deletar{
            background: #F23535;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
            display: inline-block;
        }
        .btn-deletar:hover{
            transform: scale(1.05);
            background: #c91f2c;
        }
        .btn-voltar{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #F23535;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 30px;
            margin-top: 30px;
            transition: all 0.3s;
            font-weight: bold;
        }
        .btn-voltar:hover{
            transform: scale(1.05);
            background: #c91f2c;
        }
        .sem-clientes{
            text-align: center;
            padding: 40px;
            color: #aaa;
        }
        .footer-botoes{
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        /* MODAL DE EDIÇÃO */
        .modal{
            display: <?php echo ($cliente_editar) ? 'flex' : 'none'; ?>;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }
        .modal-content{
            background: #403E3F;
            padding: 40px;
            border-radius: 25px;
            border: 1px solid #F23535;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .modal-content h2{
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        .modal-content input{
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: none;
            border-radius: 10px;
            background: #2b2b2b;
            color: white;
            font-size: 16px;
        }
        .modal-content button{
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            border: none;
            color: white;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
        }
        .modal-content button.cancelar{
            background: #F23535;
            margin-top: 10px;
        }
        .aviso-senha{
            color: #888;
            font-size: 12px;
            margin-bottom: 15px;
            text-align: left;
        }
        @keyframes fadeIn{
            from{ opacity: 0; transform: translateY(-20px); }
            to{ opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px){
            body{ padding: 20px; }
            .container{ padding: 20px; }
            th, td{ padding: 10px; }
            .btn-editar, .btn-deletar{ padding: 5px 10px; font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1><i></i> Lista de Clientes</h1>
        <div class='subtitulo'>Gerencie todos os clientes cadastrados no sistema</div>
        
        <div class='total-clientes'>
            Total de clientes: <span><?php echo $result->num_rows; ?></span>
        </div>
        
        <div class='table-wrapper'>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th style='text-align: center'>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['cliid']; ?></td>
                                <td><?php echo htmlspecialchars($row['clinome']); ?></td>
                                <td><?php echo htmlspecialchars($row['clicpf']); ?></td>
                                <td><?php echo htmlspecialchars($row['clitel']); ?></td>
                                <td><?php echo htmlspecialchars($row['cliendereco']); ?></td>
                                <td style='text-align: center'>
                                    <a href='?deletar=<?php echo $row['cliid']; ?>' 
                                       class='btn-deletar' 
                                       onclick='return confirm("Tem certeza que deseja excluir o cliente \"<?php echo addslashes($row['clinome']); ?>\"?")'>
                                         Excluir
                                    </a>
                                </a>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan='6' class='sem-clientes'>
                                 Nenhum cliente cadastrado ainda
                                <br><br>
                                <a href='../../telas/cadastrocliente.html' style='color: #F23535;'>Clique aqui para cadastrar</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class='footer-botoes'>
            <a href='../../telas/principal.html' class='btn-voltar'>
                Voltar ao Menu
            </a>
            <a href='../../telas/cadastrocliente.html' class='btn-voltar' style='background: #4CAF50;'>
                Novo Cliente
            </a>
        </div>
    </div>
    
    <!-- MODAL DE EDIÇÃO -->
    <?php if($cliente_editar): ?>
    <div class='modal' id='modalEditar'>
        <div class='modal-content'>
            <h2> Editar Cliente</h2>
            <form method='POST'>
                <input type='hidden' name='acao' value='editar'>
                <input type='hidden' name='id' value='<?php echo $cliente_editar['cliid']; ?>'>
                
                <input type='text' name='nome' value='<?php echo htmlspecialchars($cliente_editar['clinome']); ?>' placeholder="NOME COMPLETO" required>
                
                <input type='password' name='senha' placeholder="NOVA SENHA (deixe em branco para manter)">
                <div class='aviso-senha'>⚠️ Se não quiser mudar a senha, deixe o campo em branco.</div>
                
                <input type='text' name='cpf' value='<?php echo htmlspecialchars($cliente_editar['clicpf']); ?>' placeholder="CPF" required>
                <input type='text' name='telefone' value='<?php echo htmlspecialchars($cliente_editar['clitel']); ?>' placeholder="TELEFONE" required>
                <input type='text' name='endereco' value='<?php echo htmlspecialchars($cliente_editar['cliendereco']); ?>' placeholder="ENDEREÇO" required>
                
                <button type='submit'>💾 SALVAR ALTERAÇÕES</button>
                <button type='button' class='cancelar' onclick="window.location.href='listarCliente.PHP'">❌ CANCELAR</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        // Fechar modal ao clicar fora
        const modal = document.getElementById('modalEditar');
        if(modal) {
            modal.addEventListener('click', function(e) {
                if(e.target === modal) {
                    window.location.href = 'listar_clientes.php';
                }
            });
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>