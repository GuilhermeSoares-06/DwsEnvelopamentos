<?php
include("../Banco/conexao.php");

// ATUALIZAR SERVIÇO (via POST do formulário de editar)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'editar') {
    $id = $_POST['id'];
    $valor = $_POST['valor'];
    $valor_antigo = $_POST['valor_antigo'];
    
    $stmt = $conn->prepare("UPDATE servicos SET servalor = ? WHERE serid = ?");
    $stmt->bind_param("si", $valor, $id);
    
    if($stmt->execute()) {
        // Sucesso - mostrar modal bonito
        $status = "sucesso";
        $mensagem = "Valor atualizado com sucesso!";
        $icone = "✅";
        $cor = "#4CAF50";
        $titulo = "SUCESSO!";
    } else {
        // Erro - mostrar modal de erro
        $status = "erro";
        $mensagem = "Erro ao atualizar o valor!";
        $icone = "❌";
        $cor = "#F23535";
        $titulo = "ERRO!";
    }
    $stmt->close();
    
    // Mostrar modal bonito antes de redirecionar
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Atualizando - DWS</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
                font-family: 'Segoe UI', Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                padding: 20px;
            }
            .modal-sucesso {
                background: #403E3F;
                padding: 50px;
                border-radius: 30px;
                text-align: center;
                animation: fadeInScale 0.5s ease;
                border: 2px solid <?php echo $cor; ?>;
                max-width: 500px;
                width: 90%;
                box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            }
            .icone {
                font-size: 80px;
                margin-bottom: 20px;
                animation: bounce 0.6s ease;
            }
            .modal-sucesso h2 {
                color: <?php echo $cor; ?>;
                font-size: 32px;
                margin-bottom: 15px;
                font-weight: bold;
            }
            .modal-sucesso h3 {
                color: white;
                font-size: 24px;
                margin-bottom: 20px;
            }
            .modal-sucesso p {
                color: #A4A5A6;
                font-size: 16px;
                margin-bottom: 20px;
                line-height: 1.5;
            }
            .valor-container {
                background: #2b2b2b;
                border-radius: 15px;
                padding: 20px;
                margin: 20px 0;
            }
            .valor-antigo {
                color: #ff9800;
                font-size: 18px;
                margin-bottom: 10px;
                text-decoration: line-through;
            }
            .valor-novo {
                color: #4CAF50;
                font-size: 32px;
                font-weight: bold;
                margin-top: 10px;
            }
            .valor-novo small {
                font-size: 16px;
            }
            .seta {
                font-size: 30px;
                color: #F23535;
                margin: 10px 0;
            }
            .progress-bar {
                width: 100%;
                height: 4px;
                background: #2b2b2b;
                border-radius: 2px;
                margin-top: 30px;
                overflow: hidden;
            }
            .progress {
                width: 0%;
                height: 100%;
                background: <?php echo $cor; ?>;
                animation: loadProgress 2s linear forwards;
            }
            .tempo {
                font-size: 12px;
                color: #888;
                margin-top: 15px;
            }
            @keyframes fadeInScale {
                from {
                    opacity: 0;
                    transform: scale(0.8);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            @keyframes bounce {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.2);
                }
            }
            @keyframes loadProgress {
                0% {
                    width: 0%;
                }
                100% {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div class="modal-sucesso">
            <div class="icone"><?php echo $icone; ?></div>
            <h2><?php echo $titulo; ?></h2>
            <h3><?php echo $mensagem; ?></h3>
            
            <div class="valor-container">
                <div class="valor-antigo">
                    📉 Valor anterior: R$ <?php echo htmlspecialchars($valor_antigo); ?>
                </div>
                <div class="seta">▼</div>
                <div class="valor-novo">
                    📈 Novo valor: R$ <?php echo htmlspecialchars($valor); ?>
                </div>
            </div>
            
            <p>✅ O valor do serviço foi atualizado no sistema com sucesso!</p>
            
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            <div class="tempo">⏳ Redirecionando em 2 segundos...</div>
        </div>
        
        <script>
            setTimeout(function() {
                window.location.href = 'GerenciarServico.php';
            }, 2000);
        </script>
    </body>
    </html>
    <?php
    exit();
}

// PEGAR TERMO DE BUSCA
$busca = isset($_GET['busca']) ? mysqli_real_escape_string($conn, $_GET['busca']) : '';

// BUSCAR SERVIÇOS COM NOME DO CLIENTE (COM OU SEM FILTRO)
if($busca != '') {
    $sql = "SELECT serid, tipo_servico, serdescricao, servalor, serdata_servico, 
                   clinome, clitel 
            FROM servicos  
            JOIN clientes ON cliid = cliid 
            WHERE clinome LIKE '%$busca%' 
            ORDER BY serid DESC";
} else {
    $sql = "SELECT serid, tipo_servico, serdescricao, servalor, serdata_servico, 
                   clinome, clitel 
            FROM servicos  
            JOIN clientes  ON cliid = cliid 
            ORDER BY serid DESC";
}
$result = $conn->query($sql);

// CALCULAR TOTAL ACUMULADO
$sql_total = "SELECT SUM(CAST(REPLACE(servalor, ',', '.') AS DECIMAL(10,2))) as total FROM servicos";
$result_total = $conn->query($sql_total);
$total_row = $result_total->fetch_assoc();
$total_geral = $total_row['total'] ? number_format($total_row['total'], 2, ',', '.') : '0,00';

// CALCULAR TOTAL DOS SERVIÇOS FILTRADOS
if($busca != '') {
    $sql_total_filtrado = "SELECT SUM(CAST(REPLACE(servalor, ',', '.') AS DECIMAL(10,2))) as total 
                           FROM servicos s 
                           JOIN clientes c ON s.cliid = c.cliid 
                           WHERE c.clinome LIKE '%$busca%'";
    $result_total_filtrado = $conn->query($sql_total_filtrado);
    $total_filtrado_row = $result_total_filtrado->fetch_assoc();
    $total_filtrado = $total_filtrado_row['total'] ? number_format($total_filtrado_row['total'], 2, ',', '.') : '0,00';
}

// BUSCAR SERVIÇO PARA EDITAR (se tiver id na URL)
$servico_editar = null;
if(isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $sql_editar = "SELECT s.*, c.clinome, c.clitel 
                   FROM servicos s 
                   JOIN clientes c ON s.cliid = c.cliid 
                   WHERE s.serid = $id_editar";
    $result_editar = $conn->query($sql_editar);
    $servico_editar = $result_editar->fetch_assoc();
}
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
        
        /* LAYOUT COM SIDEBAR */
        .dashboard{
            display: flex;
            gap: 25px;
            max-width: 1500px;
            margin: 0 auto;
            align-items: flex-start;
        }
        
        /* CONTEÚDO PRINCIPAL */
        .main-content{
            flex: 1;
            background: #403E3F;
            padding: 30px;
            border-radius: 25px;
            border: 1px solid #F23535;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        /* SIDEBAR DO TOTAL */
        .sidebar-total{
            width: 320px;
            background: #403E3F;
            border-radius: 25px;
            border: 1px solid #F23535;
            padding: 25px;
            position: sticky;
            top: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .total-header{
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #F23535;
        }
        .total-header h3{
            color: #F23535;
            font-size: 20px;
        }
        .total-header .icone{
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .total-valor{
            text-align: center;
            margin-bottom: 30px;
        }
        .total-valor .label{
            color: #A4A5A6;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .total-valor .valor{
            font-size: 48px;
            font-weight: bold;
            color: #4CAF50;
        }
        .total-valor .moeda{
            font-size: 24px;
            color: #4CAF50;
        }
        
        .total-detalhes{
            background: #2b2b2b;
            border-radius: 15px;
            padding: 15px;
            margin-top: 20px;
        }
        .total-detalhes p{
            color: #A4A5A6;
            margin: 8px 0;
            font-size: 14px;
        }
        .total-detalhes strong{
            color: white;
        }
        
        .servicos-list{
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        .servicos-list h4{
            color: white;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .servico-item{
            background: #2b2b2b;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            font-size: 12px;
        }
        .servico-item .nome{
            color: #F23535;
        }
        .servico-item .valor-item{
            color: #4CAF50;
            float: right;
        }
        
        h1{
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        
        /* BARRA DE PESQUISA */
        .busca-container{
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .busca-input{
            flex: 1;
            max-width: 400px;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            background: #2b2b2b;
            color: white;
            font-size: 16px;
            outline: none;
        }
        .busca-input:focus{
            border: 2px solid #F23535;
        }
        .busca-btn{
            padding: 12px 25px;
            background: #F23535;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s;
        }
        .busca-btn:hover{
            background: #c91f2c;
            transform: scale(1.02);
        }
        .limpar-btn{
            padding: 12px 25px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: 0.3s;
            display: inline-block;
        }
        .limpar-btn:hover{
            background: #45a049;
            transform: scale(1.02);
        }
        .resultado-info{
            text-align: center;
            color: #A4A5A6;
            margin-bottom: 20px;
            font-size: 14px;
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
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-editar:hover{
            transform: scale(1.05);
            background: #45a049;
        }
        .btn-voltar{
            display: inline-block;
            background: #F23535;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 10px;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn-voltar:hover{
            transform: scale(1.05);
            background: #c91f2c;
        }
        .status{
            color: #ff9800;
            font-weight: bold;
        }
        
        /* MODAL DE EDIÇÃO */
        .modal{
            display: <?php echo ($servico_editar) ? 'flex' : 'none'; ?>;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }
        .modal-content{
            background: #403E3F;
            padding: 40px;
            border-radius: 25px;
            border: 2px solid #F23535;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .modal-content h2{
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }
        .info-servico{
            background: #2b2b2b;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-servico p{
            color: #A4A5A6;
            margin: 5px 0;
            font-size: 14px;
        }
        .info-servico strong{
            color: #F23535;
        }
        .modal-content input{
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: none;
            border-radius: 10px;
            background: #2b2b2b;
            color: white;
            font-size: 18px;
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
            transition: 0.3s;
        }
        .modal-content button:hover{
            transform: scale(1.02);
        }
        .modal-content button.cancelar{
            background: #F23535;
            margin-top: 10px;
        }
        .modal-content button.cancelar:hover{
            background: #c91f2c;
        }
        .valor-atual{
            text-align: center;
            color: #4CAF50;
            font-size: 20px;
            margin-bottom: 15px;
        }
        @keyframes fadeIn{
            from{ opacity: 0; transform: translateY(-20px); }
            to{ opacity: 1; transform: translateY(0); }
        }
        
        /* RESPONSIVO */
        @media (max-width: 1000px){
            .dashboard{
                flex-direction: column;
            }
            .sidebar-total{
                width: 100%;
                position: relative;
                margin-bottom: 20px;
            }
            body{ padding: 20px; }
            .main-content{ padding: 20px; }
        }
        
        @media (max-width: 768px){
            th, td{ font-size: 12px; padding: 8px; }
            .btn-editar{ padding: 5px 10px; font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class='dashboard'>
        <!-- CONTEÚDO PRINCIPAL -->
        <div class='main-content'>
            <h1>Lista de Pedidos</h1>
            
            <!-- BARRA DE PESQUISA -->
            <div class='busca-container'>
                <form method='GET' style='display: flex; gap: 10px; width: 100%; justify-content: center; flex-wrap: wrap;'>
                    <input type='text' name='busca' class='busca-input' placeholder='Pesquisar por nome do cliente...' value='<?php echo htmlspecialchars($busca); ?>'>
                    <button type='submit' class='busca-btn'> Buscar</button>
                    <?php if($busca != ''): ?>
                        <a href='GerenciarServico.php' class='limpar-btn'> Limpar</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- INFORMAÇÃO DO RESULTADO -->
            <?php if($busca != ''): ?>
                <div class='resultado-info'>
                    Resultados para: <strong>"<?php echo htmlspecialchars($busca); ?>"</strong> - 
                    <?php echo $result->num_rows; ?> pedido(s) encontrado(s)
                </div>
            <?php endif; ?>
            
            <div style='overflow-x: auto;'>
                <table>
                    <thead>
                        <tr>
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
                                    <td><?php echo htmlspecialchars($row['clinome']); ?></td>
                                    <td><?php echo htmlspecialchars($row['clitel']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tipo_servico']); ?></td>
                                    <td><?php echo htmlspecialchars($row['serdescricao']); ?></td>
                                    <td class='status'><?php echo empty($row['servalor']) ? 'R$ 0,00' : 'R$ ' . htmlspecialchars($row['servalor']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['serdata_servico'])); ?></td>
                                    <td>
                                        <a href='?editar=<?php echo $row['serid']; ?><?php echo ($busca != '') ? '&busca=' . urlencode($busca) : ''; ?>' class='btn-editar'>Editar Valor</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan='7' style='text-align:center'>
                                    <?php if($busca != ''): ?>
                                         Nenhum pedido encontrado para "<strong><?php echo htmlspecialchars($busca); ?></strong>"
                                    <?php else: ?>
                                         Nenhum pedido cadastrado
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <a href='../../telas/ADM/principalFUN.html' class='btn-voltar'>🏠 Voltar ao Menu</a>
        </div>
        
        <!-- SIDEBAR COM TOTAL ACUMULADO -->
        <div class='sidebar-total'>
            <div class='total-header'>
                <div class='icone'>💰</div>
                <h3>Faturamento Total</h3>
            </div>
            
            <div class='total-valor'>
                <div class='label'>VALOR ACUMULADO</div>
                <div class='valor'>
                    <span class='moeda'>R$</span> <?php echo $total_geral; ?>
                </div>
            </div>
            
            <div class='total-detalhes'>
                <p> <strong>Resumo Financeiro</strong></p>
                <?php if($busca != ''): ?>
                    <p>Filtro atual: <strong><?php echo htmlspecialchars($busca); ?></strong></p>
                    <p>Pedidos encontrados: <strong><?php echo $result->num_rows; ?></strong></p>
                    <p>Total filtrado: <strong style="color:#4CAF50">R$ <?php echo $total_filtrado; ?></strong></p>
                    <hr style="border-color:#555; margin: 10px 0;">
                <?php endif; ?>
                <p>Total de pedidos: <strong><?php echo $conn->query("SELECT COUNT(*) as total FROM servicos")->fetch_assoc()['total']; ?></strong></p>
                <p>Ticket médio: <strong style="color:#ff9800">R$ 
                    <?php 
                    $total_pedidos = $conn->query("SELECT COUNT(*) as total FROM servicos")->fetch_assoc()['total'];
                    $valor_numerico = str_replace(',', '.', str_replace('.', '', $total_geral));
                    $media = ($total_pedidos > 0) ? number_format($valor_numerico / $total_pedidos, 2, ',', '.') : '0,00';
                    echo $media;
                    ?>
                </strong></p>
            </div>
            
            <!-- ÚLTIMOS SERVIÇOS -->
            <div class='servicos-list'>
                <h4>Últimos serviços</h4>
                <?php
                $sql_ultimos = "SELECT s.servalor, c.clinome 
                               FROM servicos s 
                               JOIN clientes c ON s.cliid = c.cliid 
                               ORDER BY s.serid DESC LIMIT 5";
                $ultimos = $conn->query($sql_ultimos);
                while($ultimo = $ultimos->fetch_assoc()):
                ?>
                <div class='servico-item'>
                    <span class='nome'><?php echo htmlspecialchars(substr($ultimo['clinome'], 0, 20)); ?></span>
                    <span class='valor-item'>R$ <?php echo empty($ultimo['servalor']) ? '0,00' : htmlspecialchars($ultimo['servalor']); ?></span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- MODAL DE EDIÇÃO -->
    <?php if($servico_editar): ?>
    <div class='modal' id='modalEditar'>
        <div class='modal-content'>
            <h2>Editar Valor do Serviço</h2>
            
            <div class='info-servico'>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($servico_editar['clinome']); ?></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($servico_editar['clitel']); ?></p>
                <p><strong>Serviço:</strong> <?php echo htmlspecialchars($servico_editar['tipo_servico']); ?></p>
                <p><strong>Descrição:</strong> <?php echo htmlspecialchars($servico_editar['serdescricao']); ?></p>
            </div>
            
            <div class='valor-atual'>
                Valor atual: R$ <?php echo empty($servico_editar['servalor']) ? '0,00' : htmlspecialchars($servico_editar['servalor']); ?>
            </div>
            
            <form method='POST'>
                <input type='hidden' name='acao' value='editar'>
                <input type='hidden' name='id' value='<?php echo $servico_editar['serid']; ?>'>
                <input type='hidden' name='valor_antigo' value='<?php echo $servico_editar['servalor']; ?>'>
                
                <input type='text' name='valor' placeholder="Digite o novo valor (ex: 350,00)" required autofocus>
                
                <button type='submit'>SALVAR VALOR</button>
                <button type='button' class='cancelar' onclick="window.location.href='GerenciarServico.php<?php echo ($busca != '') ? '?busca=' . urlencode($busca) : ''; ?>'">❌ CANCELAR</button>
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
                    window.location.href = 'GerenciarServico.php<?php echo ($busca != '') ? '?busca=' . urlencode($busca) : ''; ?>';
                }
            });
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>