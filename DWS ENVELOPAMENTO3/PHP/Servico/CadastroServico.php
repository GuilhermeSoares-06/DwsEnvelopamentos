<?php
include("../Banco/conexao.php");

// DELETAR SERVIÇO
if(isset($_GET['deletar'])) {
    $id = intval($_GET['deletar']);
    $stmt = $conn->prepare("DELETE FROM servicos WHERE serid = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Serviço deletado!'); window.location.href='listar_servicos.php';</script>";
    exit();
}

// ATUALIZAR SERVIÇO
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'editar') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $valor = $_POST['valor'];
    
    $stmt = $conn->prepare("UPDATE servicos SET servalor = ? WHERE serid = ?");
    $stmt->bind_param("si", $valor, $id);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('Serviço atualizado!'); window.location.href='listar_servicos.php';</script>";
    exit();
}

// BUSCAR SERVIÇOS COM NOME DO CLIENTE
$sql = "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor, s.serdata_servico, 
               c.clinome, c.clitel 
        FROM servicos s 
        JOIN clientes c ON s.cliid = c.cliid 
        ORDER BY s.serid DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Serviços - DWS</title>
    <style>
        *{ margin:0; padding:0; box-sizing:border-box; }
        body{
            background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 40px;
            min-height: 100vh;
        }
        .container{
            max-width: 1300px;
            margin: 0 auto;
            background: #403E3F;
            padding: 30px;
            border-radius: 25px;
            border: 1px solid #F23535;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1{
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        table{
            width: 100%;
            border-collapse: collapse;
            color: white;
        }
        th{
            background: #F23535;
            padding: 12px;
            text-align: left;
        }
        td{
            padding: 10px;
            border-bottom: 1px solid #555;
        }
        tr:hover{
            background: #4a4a4a;
        }
        .btn-editar{
            background: #4CAF50;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-deletar{
            background: #F23535;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-voltar{
            display: inline-block;
            background: #F23535;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 10px;
            margin-top: 20px;
        }
        .status{
            color: #ff9800;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📋 Lista de Pedidos</h1>
        
        <div style='overflow-x: auto;'>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Telefone</th>
                        <th>Serviço</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['serid']; ?></td>
                                <td><?php echo htmlspecialchars($row['clinome']); ?></td>
                                <td><?php echo htmlspecialchars($row['clitel']); ?></td>
                                <td><?php echo htmlspecialchars($row['tipo_servico']); ?></td>
                                <td><?php echo htmlspecialchars($row['serdescricao']); ?></td>
                                <td class='status'><?php echo htmlspecialchars($row['servalor']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['serdata_servico'])); ?></td>
                                <td>
                                    <a href='editar_servico.php?id=<?php echo $row['serid']; ?>' class='btn-editar'>✏️ Editar</a>
                                    <a href='?deletar=<?php echo $row['serid']; ?>' class='btn-deletar' onclick='return confirm("Deletar este pedido?")'>🗑️ Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan='8'>Nenhum pedido cadastrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <a href='../../telas/principal.html' class='btn-voltar'>🏠 Voltar ao Menu</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>