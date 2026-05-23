<?php
include("../Banco/conexao.php");

$sql = "SELECT cliid, clinome, clicpf, clitel, cliendereco FROM clientes ORDER BY cliid DESC";
$result = $conn->query($sql);

echo "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Lista de Clientes - DWS</title>
    <style>
        body{
            background: #3b3b3b;
            font-family: Arial;
            padding: 40px;
        }
        .container{
            max-width: 1200px;
            margin: 0 auto;
            background: #403E3F;
            padding: 30px;
            border-radius: 25px;
            border: 1px solid #F23535;
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
        .btn-voltar{
            display: inline-block;
            background: #F23535;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Lista de Clientes</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                </tr>
            </thead>
            <tbody>
";

if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "
        <tr>
            <td>" . $row['cliid'] . "</td>
            <td>" . $row['clinome'] . "</td>
            <td>" . $row['clicpf'] . "</td>
            <td>" . $row['clitel'] . "</td>
            <td>" . $row['cliendereco'] . "</td>
        </tr>
        ";
    }
} else {
    echo "<tr><td colspan='5'>Nenhum cliente cadastrado</td></tr>";
}

echo "
            </tbody>
        </table>
        <br>
        <a href='../../telas/principal.html' class='btn-voltar'>Voltar ao Menu</a>
    </div>
</body>
</html>
";

$conn->close();
?>