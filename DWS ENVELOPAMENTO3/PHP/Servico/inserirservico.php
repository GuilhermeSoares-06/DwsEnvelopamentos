<?php
// Caminho correto para o conexao.php
include("../../PHP/Banco/conexao.php");

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verificar se a conexão existe
    if(!$conn) {
        die("Erro de conexão com o banco de dados!");
    }
    
    // Pegar dados do formulário
    $tipo_servico = mysqli_real_escape_string($conn, $_POST['tipo_servico']);
    $descricao = mysqli_real_escape_string($conn, $_POST['mensagem']);
    
    // Verificar se está logado (sessão)
    session_start();
    $cliid = isset($_SESSION['cliid']) ? $_SESSION['cliid'] : null;
    
    // Se não tiver cliente logado, busca um cliente existente
    if(!$cliid) {
        // Buscar o primeiro cliente disponível no banco
        $sql_busca = "SELECT cliid FROM clientes LIMIT 1";
        $result_busca = $conn->query($sql_busca);
        
        if($result_busca && $result_busca->num_rows > 0) {
            $row = $result_busca->fetch_assoc();
            $cliid = $row['cliid'];
        } else {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Erro</title>
                <style>
                    body {
                        background: linear-gradient(135deg, #1e1e1e, #3b3b3b);
                        font-family: 'Segoe UI', Arial, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                    }
                    .card {
                        background: #403E3F;
                        padding: 40px;
                        border-radius: 25px;
                        border: 1px solid #F23535;
                        text-align: center;
                        max-width: 500px;
                    }
                    .card h2 { color: #F23535; }
                    .card p { color: white; }
                    .btn {
                        display: inline-block;
                        margin-top: 20px;
                        padding: 12px 25px;
                        background: #F23535;
                        color: white;
                        text-decoration: none;
                        border-radius: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="card">
                    <h2>⚠️ Erro</h2>
                    <p>Nenhum cliente cadastrado no sistema!</p>
                    <p>Por favor, cadastre um cliente primeiro.</p>
                    <a href="../Cliente/CadastroCliente.html" class="btn">Cadastrar Cliente</a>
                </div>
            </body>
            </html>
            <?php
            $conn->close();
            exit();
        }
    }
    
    // Inserir no banco de dados
    $stmt = $conn->prepare("INSERT INTO servicos (cliid, tipo_servico, serdescricao, serdata_servico) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $cliid, $tipo_servico, $descricao);
    
    if($stmt->execute()) {
        $servico_id = $stmt->insert_id;
        $status_banco = "sucesso";
        $mensagem_banco = "Serviço salvo com sucesso! (ID: $servico_id)";
    } else {
        $status_banco = "erro";
        $mensagem_banco = "Erro ao salvar: " . $stmt->error;
    }
    $stmt->close();
    
    // Buscar nome do cliente
    $sql_cliente = "SELECT clinome FROM clientes WHERE cliid = $cliid";
    $result_cliente = $conn->query($sql_cliente);
    $cliente = $result_cliente->fetch_assoc();
    $nome_cliente = $cliente ? $cliente['clinome'] : "Cliente";
    
    // WhatsApp
    $telefone = "5514996175617";
    
    $texto = "*📋 NOVO PEDIDO - DWS ENVELOPAMENTO*%0A%0A";
    $texto .= "*Cliente:* $nome_cliente%0A";
    $texto .= "*Tipo de Serviço:* $tipo_servico%0A";
    $texto .= "*Descrição:* $descricao%0A";
    $texto .= "*Data do Pedido:* " . date("d/m/Y H:i") . "%0A%0A";
    $texto .= "Enviado pelo site da DWS Envelopamento.";
    
    $url_whatsapp = "https://wa.me/$telefone?text=$texto";
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Pedido Enviado</title>
        <style>
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
            .card {
                background: #403E3F;
                padding: 40px;
                border-radius: 25px;
                border: 2px solid #F23535;
                text-align: center;
                max-width: 500px;
                animation: fadeIn 0.5s ease;
            }
            .sucesso { color: #4CAF50; font-size: 60px; }
            .erro { color: #F23535; font-size: 60px; }
            .card h2 { color: white; margin: 20px 0; }
            .card p { color: #A4A5A6; margin: 10px 0; }
            .mensagem-banco {
                background: #2b2b2b;
                padding: 12px;
                border-radius: 10px;
                margin: 20px 0;
            }
            .botoes { display: flex; gap: 15px; justify-content: center; margin-top: 25px; }
            .btn {
                padding: 12px 25px;
                border: none;
                border-radius: 10px;
                text-decoration: none;
                display: inline-block;
                transition: 0.3s;
            }
            .btn-voltar { background: #F23535; color: white; }
            .btn-whats { background: #25D366; color: white; }
            .btn:hover { transform: scale(1.05); }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-30px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body>
        <div class="card">
            <?php if($status_banco == "sucesso"): ?>
                <div class="sucesso">✅</div>
                <h2>Pedido Enviado!</h2>
                <div class="mensagem-banco"><?php echo $mensagem_banco; ?></div>
                <p>📱 Redirecionando para o WhatsApp...</p>
            <?php else: ?>
                <div class="erro">⚠️</div>
                <h2>Erro ao Enviar</h2>
                <div class="mensagem-banco"><?php echo $mensagem_banco; ?></div>
            <?php endif; ?>
            
            <div class="botoes">
                <a href="../../PHP/Servico/inserirservico.php" class="btn btn-voltar">◀️ Voltar</a>
                <?php if($status_banco == "sucesso"): ?>
                <a href="<?php echo $url_whatsapp; ?>" class="btn btn-whats" target="_blank">📱 WhatsApp</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if($status_banco == "sucesso"): ?>
        <script>
            setTimeout(function() {
                window.open('<?php echo $url_whatsapp; ?>', '_blank');
            }, 2000);
        </script>
        <?php endif; ?>
    </body>
    </html>
    <?php
    
    $conn->close();
} else {
    header("Location: ../../telas/Cliente/contato.html");
    exit();
}
?>