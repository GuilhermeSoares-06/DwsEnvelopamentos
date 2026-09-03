<?php
// =============================================
// GerenciarServico.php  (Área ADM - Versão Profissional)
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

// CORREÇÃO DO FUSO HORÁRIO (Horário de Brasília)
date_default_timezone_set('America/Sao_Paulo');

// Função para converter valor brasileiro para decimal (float)
function brToDecimal($valor) {
    $valor = trim($valor);
    $valor = str_replace(['R$', ' ', ' '], '', $valor);
    
    if (strpos($valor, ',') !== false) {
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
    }
    
    return (float)$valor;
}

// Função para formatar valor em brasileiro
function formatBr($valor) {
    return number_format((float)$valor, 2, ',', '.');
}

// ---- Ações POST (JSON) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'editar') {
        $id = (int)($_POST['id'] ?? 0);
        $valor = trim($_POST['valor'] ?? '');
        $valor_decimal = brToDecimal($valor);
        $valor_formatado = formatBr($valor_decimal);
        
        $stmt = $pdo->prepare("UPDATE servicos SET servalor = :v WHERE serid = :id");
        $ok = $stmt->execute([':v' => $valor_formatado, ':id' => $id]);
        
        echo json_encode($ok
            ? ['status'=>'sucesso', 'mensagem'=>"Valor atualizado para R$ $valor_formatado"]
            : ['status'=>'erro', 'mensagem'=>'Erro ao atualizar valor.']
        );
        exit;
    }

    if ($acao === 'deletar') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM servicos WHERE serid = :id");
        $ok = $stmt->execute([':id' => $id]);
        echo json_encode($ok
            ? ['status'=>'sucesso', 'mensagem'=>'Serviço excluído com sucesso.']
            : ['status'=>'erro', 'mensagem'=>'Erro ao excluir serviço.']
        );
        exit;
    }

    echo json_encode(['status'=>'erro', 'mensagem'=>'Ação desconhecida.']);
    exit;
}

// ---- Busca ----
$busca = trim($_GET['busca'] ?? '');
$cliidFiltro = isset($_GET['cliid']) ? (int)$_GET['cliid'] : 0;
$clienteFiltro = null;

if ($cliidFiltro) {
    $stmt = $pdo->prepare(
        "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor, s.serdata_servico,
                c.clinome, c.clitel
         FROM servicos s
         LEFT JOIN clientes c ON s.cliid = c.cliid
         WHERE s.cliid = :cliid
         ORDER BY s.serid DESC"
    );
    $stmt->execute([':cliid' => $cliidFiltro]);

    $cf = $pdo->prepare("SELECT clinome, clitel FROM clientes WHERE cliid = :cliid");
    $cf->execute([':cliid' => $cliidFiltro]);
    $clienteFiltro = $cf->fetch();
} elseif ($busca) {
    $stmt = $pdo->prepare(
        "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor, s.serdata_servico,
                c.clinome, c.clitel
         FROM servicos s
         LEFT JOIN clientes c ON s.cliid = c.cliid
         WHERE c.clinome LIKE :b
         ORDER BY s.serid DESC"
    );
    $stmt->execute([':b' => "%$busca%"]);
} else {
    $stmt = $pdo->query(
        "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor, s.serdata_servico,
                c.clinome, c.clitel
         FROM servicos s
         LEFT JOIN clientes c ON s.cliid = c.cliid
         ORDER BY s.serid DESC"
    );
}
$servicos = $stmt->fetchAll();

// ---- Totais ----
$total_geral = 0;
$qtd_total = 0;

$todosServicos = $pdo->query("SELECT servalor FROM servicos")->fetchAll();
foreach ($todosServicos as $s) {
    $total_geral += brToDecimal($s['servalor']);
    $qtd_total++;
}

$ticket_medio = $qtd_total > 0 ? ($total_geral / $qtd_total) : 0;
$total_geral_formatado = formatBr($total_geral);
$ticket_medio_formatado = formatBr($ticket_medio);

$ultimos = $pdo->query(
    "SELECT s.servalor, c.clinome
     FROM servicos s
     LEFT JOIN clientes c ON s.cliid = c.cliid
     ORDER BY s.serid DESC LIMIT 5"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DWS | Gerenciamento de Pedidos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #0a0a0a;
            background-image: radial-gradient(circle at 20% 50%, rgba(242, 53, 53, 0.05) 0%, transparent 50%),
                              radial-gradient(circle at 80% 50%, rgba(242, 53, 53, 0.03) 0%, transparent 50%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 30px;
            min-height: 100vh;
            color: #e8e8e8;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #1a1a1a; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #F23535; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #c91f2c; }

        /* ===== DASHBOARD LAYOUT ===== */
        .dashboard {
            display: flex;
            gap: 28px;
            max-width: 1600px;
            margin: 0 auto;
            align-items: flex-start;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            background: linear-gradient(145deg, #1e1e1e, #252525);
            padding: 32px;
            border-radius: 24px;
            border: 1px solid rgba(242, 53, 53, 0.15);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        /* ===== HEADER ===== */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, #F23535 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .page-header h1 i {
            -webkit-text-fill-color: #F23535;
            margin-right: 10px;
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(242, 53, 53, 0.15);
            border: 1px solid rgba(242, 53, 53, 0.3);
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
            color: #F23535;
            letter-spacing: 0.3px;
        }

        .admin-badge i {
            font-size: 14px;
        }

        /* ===== SEARCH ===== */
        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .search-wrapper {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 14px;
        }

        .search-wrapper input {
            width: 100%;
            padding: 13px 18px 13px 45px;
            border: 2px solid transparent;
            border-radius: 14px;
            background: #1a1a1a;
            color: #e8e8e8;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
            font-family: inherit;
        }

        .search-wrapper input:focus {
            border-color: #F23535;
            background: #222;
            box-shadow: 0 0 30px rgba(242, 53, 53, 0.1);
        }

        .search-wrapper input::placeholder {
            color: #555;
        }

        .btn-search {
            padding: 13px 28px;
            background: linear-gradient(135deg, #F23535, #c91f2c);
            color: #fff;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(242, 53, 53, 0.3);
        }

        .btn-search:active {
            transform: translateY(0);
        }

        .btn-clear {
            padding: 13px 24px;
            background: rgba(76, 175, 80, 0.15);
            color: #4CAF50;
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-clear:hover {
            background: rgba(76, 175, 80, 0.25);
            transform: translateY(-2px);
        }

        /* ===== RESULT INFO ===== */
        .result-info {
            text-align: center;
            color: #888;
            margin-bottom: 20px;
            font-size: 14px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .result-info strong {
            color: #fff;
        }

        .result-info .highlight {
            color: #F23535;
        }

        /* ===== TABLE ===== */
        .table-wrap {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
            font-size: 14px;
        }

        thead {
            background: linear-gradient(135deg, rgba(242, 53, 53, 0.2), rgba(201, 31, 44, 0.1));
        }

        th {
            padding: 16px 14px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #aaa;
            border-bottom: 2px solid rgba(242, 53, 53, 0.2);
        }

        td {
            padding: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #d4d4d4;
            transition: all 0.2s ease;
        }

        tr {
            transition: all 0.2s ease;
        }

        tr:hover td {
            background: rgba(242, 53, 53, 0.05);
        }

        .cliente-cell {
            font-weight: 500;
            color: #fff;
        }

        .telefone-cell {
            color: #888;
            font-size: 13px;
        }

        .servico-cell {
            color: #F23535;
            font-weight: 500;
        }

        .valor-cell {
            font-weight: 600;
            color: #4CAF50;
            font-size: 15px;
        }

        .data-cell {
            color: #666;
            font-size: 13px;
        }

        /* ===== ACTION BUTTONS ===== */
        .action-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-edit {
            padding: 7px 14px;
            background: rgba(76, 175, 80, 0.15);
            color: #4CAF50;
            border: 1px solid rgba(76, 175, 80, 0.2);
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit:hover {
            background: rgba(76, 175, 80, 0.3);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }

        .btn-delete {
            padding: 7px 14px;
            background: rgba(242, 53, 53, 0.15);
            color: #F23535;
            border: 1px solid rgba(242, 53, 53, 0.2);
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-delete:hover {
            background: rgba(242, 53, 53, 0.3);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(242, 53, 53, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #555;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        .empty-state p {
            font-size: 16px;
        }

        /* ===== BACK BUTTON ===== */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 25px;
            padding: 12px 28px;
            background: rgba(255, 255, 255, 0.05);
            color: #aaa;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateX(-5px);
        }

        /* ===== SIDEBAR ===== */
        .sidebar-total {
            width: 320px;
            background: linear-gradient(145deg, #1a1a1a, #222);
            border-radius: 24px;
            border: 1px solid rgba(242, 53, 53, 0.1);
            padding: 28px;
            position: sticky;
            top: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .sidebar-total:hover {
            border-color: rgba(242, 53, 53, 0.2);
        }

        .total-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(242, 53, 53, 0.1);
        }

        .total-header .icon {
            font-size: 40px;
            margin-bottom: 10px;
            display: block;
        }

        .total-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #e8e8e8;
            letter-spacing: 0.5px;
        }

        .total-header .subtitle {
            font-size: 13px;
            color: #666;
            margin-top: 4px;
        }

        .total-value {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background: rgba(76, 175, 80, 0.05);
            border-radius: 16px;
            border: 1px solid rgba(76, 175, 80, 0.1);
        }

        .total-value .label {
            color: #888;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .total-value .amount {
            font-size: 42px;
            font-weight: 700;
            color: #4CAF50;
            letter-spacing: -1px;
        }

        .total-value .currency {
            font-size: 22px;
            color: #4CAF50;
            margin-right: 4px;
        }

        .total-details {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .total-details .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }

        .total-details .detail-item:last-child {
            border-bottom: none;
        }

        .total-details .detail-label {
            color: #888;
        }

        .total-details .detail-value {
            color: #fff;
            font-weight: 500;
        }

        .total-details .detail-value.highlight {
            color: #ff9800;
        }

        /* ===== RECENT SERVICES ===== */
        .recent-services {
            margin-top: 5px;
        }

        .recent-services h4 {
            font-size: 14px;
            color: #888;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            margin-bottom: 6px;
            transition: all 0.2s ease;
        }

        .service-item:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .service-item .client-name {
            color: #d4d4d4;
            font-size: 13px;
            font-weight: 500;
        }

        .service-item .client-name i {
            color: #F23535;
            margin-right: 6px;
            font-size: 12px;
        }

        .service-item .service-value {
            color: #4CAF50;
            font-weight: 600;
            font-size: 14px;
        }

        /* ===== MODAL ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: linear-gradient(145deg, #1e1e1e, #2a2a2a);
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(242, 53, 53, 0.2);
            width: 500px;
            max-width: 95%;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.8);
            animation: slideUp 0.3s ease;
        }

        .modal-content h2 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-content h2 i {
            color: #F23535;
        }

        .modal-info {
            background: rgba(255, 255, 255, 0.03);
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .modal-info p {
            color: #aaa;
            font-size: 14px;
            margin: 4px 0;
        }

        .modal-info strong {
            color: #fff;
        }

        .modal-info .highlight-text {
            color: #F23535;
        }

        .current-value {
            text-align: center;
            font-size: 20px;
            color: #4CAF50;
            font-weight: 600;
            margin-bottom: 20px;
            padding: 12px;
            background: rgba(76, 175, 80, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(76, 175, 80, 0.1);
        }

        .modal-content input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: #1a1a1a;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            transition: all 0.3s ease;
            outline: none;
            font-family: inherit;
            text-align: center;
        }

        .modal-content input:focus {
            border-color: #F23535;
            background: #222;
            box-shadow: 0 0 30px rgba(242, 53, 53, 0.1);
        }

        .modal-content input::placeholder {
            color: #444;
            font-weight: 400;
            font-size: 16px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .modal-actions button {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .btn-save {
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            color: #fff;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }

        .btn-cancel-modal {
            background: rgba(255, 255, 255, 0.05);
            color: #888;
        }

        .btn-cancel-modal:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* ===== TOAST ===== */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 16px 28px;
            border-radius: 14px;
            color: #fff;
            font-weight: 500;
            font-size: 15px;
            display: none;
            z-index: 9999;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }

        .toast.success {
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .toast.error {
            background: linear-gradient(135deg, #F23535, #c91f2c);
            border: 1px solid rgba(242, 53, 53, 0.3);
        }

        .toast i {
            margin-right: 10px;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .dashboard {
                flex-direction: column;
            }

            .sidebar-total {
                width: 100%;
                position: relative;
                top: 0;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .main-content {
                padding: 20px;
            }

            .page-header h1 {
                font-size: 22px;
            }

            .search-container {
                flex-direction: column;
            }

            .search-wrapper {
                min-width: 100%;
            }

            .btn-search,
            .btn-clear {
                width: 100%;
                justify-content: center;
            }

            .modal-content {
                padding: 25px;
                margin: 15px;
            }

            .modal-actions {
                flex-direction: column;
            }

            .total-value .amount {
                font-size: 32px;
            }

            .sidebar-total {
                padding: 20px;
            }

            table {
                font-size: 13px;
                min-width: 600px;
            }

            th, td {
                padding: 10px 10px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-edit, .btn-delete {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .admin-badge {
                font-size: 12px;
                padding: 6px 14px;
            }

            table {
                font-size: 12px;
                min-width: 500px;
            }

            th, td {
                padding: 8px;
            }
        }

        /* ===== LOADING ===== */
        .loading-shimmer {
            animation: shimmer 1.5s infinite;
            background: linear-gradient(90deg, rgba(255,255,255,0.05) 25%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.05) 75%);
            background-size: 200% 100%;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- HEADER -->
            <div class="page-header">
                <h1><i class="fas fa-clipboard-list"></i>Gerenciamento de Pedidos</h1>
                <div class="admin-badge">
                    <i class="fas fa-shield-alt"></i>
                    Área Administrativa
                </div>
            </div>

             <a class="btn-back" href="../../telas/ADM/principalFUN.html">
                <i class="fas fa-arrow-left"></i> Voltar ao Menu Principal
            </a>

            <!-- SEARCH -->
            <form class="search-container" method="GET">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="busca" placeholder="Buscar por nome do cliente..." value="<?= htmlspecialchars($busca) ?>">
                </div>
                <button class="btn-search" type="submit">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <?php if ($busca): ?>
                    <a class="btn-clear" href="GerenciarServico.php">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                <?php endif; ?>
            </form>

            <!-- RESULT INFO -->
            <?php if ($cliidFiltro && $clienteFiltro): ?>
            <div class="result-info">
                <i class="fas fa-user" style="color: #F23535; margin-right: 8px;"></i>
                Histórico de <strong><?= htmlspecialchars($clienteFiltro['clinome']) ?></strong>
                <span style="color: #666;">•</span> 
                <span style="color: #888;"><?= htmlspecialchars($clienteFiltro['clitel'] ?? '—') ?></span>
                <span style="color: #666;">•</span>
                <span class="highlight"><?= count($servicos) ?></span> serviço(s)
                <a class="btn-clear" style="padding: 4px 16px; font-size: 12px; margin-left: 10px;" href="GerenciarServico.php">
                    <i class="fas fa-times"></i> Ver todos
                </a>
            </div>
            <?php elseif ($busca): ?>
            <div class="result-info">
                <i class="fas fa-search" style="color: #F23535; margin-right: 8px;"></i>
                Resultado para: <strong>"<?= htmlspecialchars($busca) ?>"</strong>
                <span style="color: #666;">•</span>
                <span class="highlight"><?= count($servicos) ?></span> pedido(s) encontrado(s)
            </div>
            <?php endif; ?>

            <!-- TABLE -->
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th><i class=></i> Cliente</th>
                            <th><i class=></i> Telefone</th>
                            <th><i class=></i> Serviço</th>
                            <th>Descrição</th>
                            <th><i class=></i> Valor</th>
                            <th><i class=></i> Data</th>
                            <th style="text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($servicos): foreach ($servicos as $r): ?>
                        <tr>
                            <td class="cliente-cell"><?= htmlspecialchars($r['clinome'] ?? '—') ?></td>
                            <td class="telefone-cell"><?= htmlspecialchars($r['clitel'] ?? '—') ?></td>
                            <td class="servico-cell"><?= htmlspecialchars($r['tipo_servico']) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth($r['serdescricao'] ?? '', 0, 50, '…')) ?></td>
                            <td class="valor-cell">R$ <?= htmlspecialchars($r['servalor'] ?? '0,00') ?></td>
                            <td class="data-cell"><?= date('d/m/Y H:i', strtotime($r['serdata_servico'])) ?></td>
                            <td>
                                <div class="action-buttons" style="justify-content: center;">
                                    <button class="btn-edit" onclick="abrirEditar(<?= htmlspecialchars(json_encode($r)) ?>)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn-delete" onclick="deletar(<?= $r['serid'] ?>)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                                                        <i class="fas fa-inbox"></i>
                                    <p><?= $busca ? "Nenhum pedido encontrado para \"$busca\"" : 'Nenhum pedido cadastrado ainda.' ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- SIDEBAR -->
        <div class="sidebar-total">
            <div class="total-header">
                <span class="icon">💰</span>
                <h3>Faturamento Total</h3>
                <div class="subtitle">Visão geral financeira</div>
            </div>

            <div class="total-value">
                <div class="label">VALOR ACUMULADO</div>
                <div class="amount">
                    <span class="currency">R$</span> <?= $total_geral_formatado ?>
                </div>
            </div>

            <div class="total-details">
                <div class="detail-item">
                    <span class="detail-label"><i class="fas fa-shopping-cart"></i> Total de Pedidos</span>
                    <span class="detail-value"><?= $qtd_total ?></span>
                </div>
            </div>

            <div class="recent-services">
                <h4><i class="fas fa-history"></i> Últimos Serviços</h4>
                <?php foreach ($ultimos as $u): ?>
                <div class="service-item">
                    <span class="client-name">
                        <i class="fas fa-user-circle"></i>
                        <?= htmlspecialchars(mb_strimwidth($u['clinome'] ?? '—', 0, 20, '…')) ?>
                    </span>
                    <span class="service-value">R$ <?= htmlspecialchars($u['servalor'] ?? '0,00') ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($ultimos)): ?>
                <div style="text-align: center; color: #444; padding: 20px; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> Nenhum serviço recente
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR -->
    <div class="modal-overlay" id="modalEditar">
        <div class="modal-content">
            <h2>
                <i class="fas fa-edit"></i>
                Editar Valor do Serviço
            </h2>
            <div class="modal-info" id="infoServico">
                <!-- Preenchido via JS -->
            </div>
            <div class="current-value" id="valorAtual">
                <!-- Preenchido via JS -->
            </div>
            <input type="hidden" id="edit_id">
            <input type="hidden" id="edit_valor_antigo">
            <input type="text" id="edit_valor" placeholder="Digite o novo valor (ex: 350,00)" autofocus>
            <div class="modal-actions">
                <button class="btn-save" onclick="salvarValor()">
                    <i class="fas fa-check"></i> Salvar
                </button>
                <button class="btn-cancel-modal" onclick="fecharModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage">Mensagem</span>
    </div>

    <script>
    // ===== TOAST =====
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        
        toast.className = 'toast ' + type;
        toastMessage.textContent = message;
        
        // Ícone dinâmico
        const icon = toast.querySelector('i');
        if (type === 'success') {
            icon.className = 'fas fa-check-circle';
        } else if (type === 'error') {
            icon.className = 'fas fa-exclamation-circle';
        } else if (type === 'warning') {
            icon.className = 'fas fa-exclamation-triangle';
        } else {
            icon.className = 'fas fa-info-circle';
        }
        
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '12px';
        
        // Auto-esconder
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 3500);
    }

    // ===== MODAL =====
    function abrirEditar(servico) {
        document.getElementById('edit_id').value = servico.serid;
        document.getElementById('edit_valor_antigo').value = servico.servalor || '0';
        document.getElementById('edit_valor').value = '';
        
        document.getElementById('infoServico').innerHTML = `
            <p><i class="fas fa-user" style="color: #F23535; width: 18px;"></i> <strong>Cliente:</strong> ${servico.clinome || '—'}</p>
            <p><i class="fas fa-concierge-bell" style="color: #F23535; width: 18px;"></i> <strong>Serviço:</strong> ${servico.tipo_servico}</p>
            <p><i class="fas fa-align-left" style="color: #F23535; width: 18px;"></i> <strong>Descrição:</strong> ${(servico.serdescricao || '').substring(0, 80)}</p>
        `;
        
        document.getElementById('valorAtual').innerHTML = `
            <i class="fas fa-tag" style="margin-right: 8px;"></i>
            Valor atual: <strong>R$ ${servico.servalor || '0,00'}</strong>
        `;
        
        document.getElementById('modalEditar').classList.add('active');
        
        // Foco no input após animação
        setTimeout(() => {
            document.getElementById('edit_valor').focus();
            document.getElementById('edit_valor').select();
        }, 300);
    }

    function fecharModal() {
        document.getElementById('modalEditar').classList.remove('active');
    }

    // Fechar modal clicando fora
    document.getElementById('modalEditar').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
    });

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') fecharModal();
    });

    // ===== SALVAR VALOR =====
    async function salvarValor() {
        const valor = document.getElementById('edit_valor').value.trim();
        
        if (!valor) {
            showToast('Por favor, digite um valor válido.', 'warning');
            document.getElementById('edit_valor').focus();
            return;
        }
        
        // Validar formato
        if (!/^[\d,.]+$/.test(valor)) {
            showToast('Formato inválido. Use apenas números, vírgula ou ponto.', 'error');
            document.getElementById('edit_valor').focus();
            return;
        }

        const fd = new FormData();
        fd.append('acao', 'editar');
        fd.append('id', document.getElementById('edit_id').value);
        fd.append('valor', valor);
        fd.append('valor_antigo', document.getElementById('edit_valor_antigo').value);

        try {
            const res = await fetch('GerenciarServico.php', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            
            if (data.status === 'sucesso') {
                showToast(data.mensagem, 'success');
                fecharModal();
                // Atualiza a última atualização sem recarregar a página
                atualizarUltimaAtualizacao();
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(data.mensagem || 'Erro ao salvar valor.', 'error');
            }
        } catch (error) {
            showToast('Erro de conexão. Tente novamente.', 'error');
        }
    }

    // ===== DELETAR =====
    async function deletar(id) {
        if (!confirm('⚠️ Tem certeza que deseja excluir este pedido?\n\nEsta ação não pode ser desfeita!')) return;
        
        const fd = new FormData();
        fd.append('acao', 'deletar');
        fd.append('id', id);

        try {
            const res = await fetch('GerenciarServico.php', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            
            if (data.status === 'sucesso') {
                showToast(data.mensagem, 'success');
                // Atualiza a última atualização sem recarregar a página
                atualizarUltimaAtualizacao();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.mensagem || 'Erro ao excluir serviço.', 'error');
            }
        } catch (error) {
            showToast('Erro de conexão. Tente novamente.', 'error');
        }
    }

    // ===== MASK PARA VALOR =====
    document.getElementById('edit_valor').addEventListener('input', function(e) {
        // Permite apenas números, vírgula e ponto
        let value = this.value.replace(/[^0-9,.]/g, '');
        
        // Remove múltiplas vírgulas/pontos
        const parts = value.split(/[,.]/);
        if (parts.length > 2) {
            value = parts[0] + ',' + parts.slice(1).join('');
        }
        
        this.value = value;
    });

    // ===== ENTER PARA SALVAR =====
    document.getElementById('edit_valor').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            salvarValor();
        }
    });


    // ===== ANIMAÇÃO DE ENTRADA =====
    document.addEventListener('DOMContentLoaded', function() {
        // Adiciona classe de animação aos itens da tabela
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            row.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 50 * (index + 1));
        });

        // Atualiza data/hora na sidebar em tempo real a cada 1 segundo
        function updateTime() {
            atualizarUltimaAtualizacao();
        }
        
        // Já atualiza na inicialização e fica atualizando a cada segundo
        updateTime();
        setInterval(updateTime, 1000); // Atualiza a cada 1 segundo
    });

    // ===== EFEITO DE HOVER NAS LINHAS =====
    // Já está no CSS, mas adicionamos um efeito extra
    document.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.2s ease';
        });
    });

    console.log('🚀 DWS - Sistema de Gerenciamento de Pedidos v2.0');
    console.log('📊 Total de pedidos: <?= $qtd_total ?>');
    console.log('💰 Total faturado: R$ <?= $total_geral_formatado ?>');
    </script>
</body>
</html>