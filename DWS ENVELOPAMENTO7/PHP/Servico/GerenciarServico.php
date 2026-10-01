<?php
// =============================================
// GerenciarServico.php (Área ADM)
// M0: exige admin + formata valores em BR + responderErro/Sucesso
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

date_default_timezone_set('America/Sao_Paulo');

// === SESSÃO + EXIGE ADMIN (M0) ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Faça login como administrador.']);
    } else {
        header('Location: ../../telas/ADM/login.html');
    }
    exit;
}

// =============================================
// FUNÇÕES AUXILIARES
// =============================================

/**
 * Converte valor monetário (BR ou US) para float.
 * Aceita: "800", "800,00", "800.00", "1.500,00", "1,500.00", "R$ 800,00"
 */
function valorParaFloat($valor): float {
    if (is_numeric($valor)) return (float)$valor;

    $valor = trim((string)$valor);
    $valor = str_replace(['R$', ' ', "\xc2\xa0"], '', $valor);

    $temVirgula = strpos($valor, ',') !== false;
    $temPonto   = strpos($valor, '.') !== false;

    if ($temVirgula && $temPonto) {
        $posVirgula = strrpos($valor, ',');
        $posPonto   = strrpos($valor, '.');
        if ($posVirgula > $posPonto) {
            // Formato BR: 1.234,50
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } else {
            // Formato US: 1,234.50
            $valor = str_replace(',', '', $valor);
        }
    } elseif ($temVirgula) {
        // Só vírgula: 800,00
        $valor = str_replace(',', '.', $valor);
    }
    return (float)$valor;
}

/**
 * Formata número para BR: 1234.5 → "1.234,50"
 */
function formatBr($valor): string {
    return number_format((float)$valor, 2, ',', '.');
}

/**
 * Resposta JSON de sucesso e encerra.
 */
function responderSucesso(string $mensagem, array $extra = []): void {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['status' => 'sucesso', 'mensagem' => $mensagem], $extra));
    exit;
}

/**
 * Resposta JSON de erro e encerra.
 */
function responderErro(string $mensagem, int $httpCode = 400): void {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'erro', 'mensagem' => $mensagem]);
    exit;
}

// =============================================
// AÇÕES POST (JSON)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // ----- EDITAR -----
    if ($acao === 'editar') {
        $id    = (int)($_POST['id'] ?? 0);
        $valor = trim((string)($_POST['valor'] ?? ''));

        if ($id <= 0) {
            responderErro('ID inválido.');
        }
        if ($valor === '') {
            responderErro('Informe um valor.');
        }

        // Normaliza para float
        $valorLimpo = str_replace(['R$', ' ', "\xc2\xa0"], '', $valor);
        $temVirgula = strpos($valorLimpo, ',') !== false;
        $temPonto   = strpos($valorLimpo, '.') !== false;

        if ($temVirgula && $temPonto) {
            $posV = strrpos($valorLimpo, ',');
            $posP = strrpos($valorLimpo, '.');
            if ($posV > $posP) {
                $valorLimpo = str_replace('.', '', $valorLimpo);
                $valorLimpo = str_replace(',', '.', $valorLimpo);
            } else {
                $valorLimpo = str_replace(',', '', $valorLimpo);
            }
        } elseif ($temVirgula) {
            $valorLimpo = str_replace(',', '.', $valorLimpo);
        }

        if (!is_numeric($valorLimpo)) {
            responderErro('O valor deve ser um número válido.');
        }

        $valorFloat = (float)$valorLimpo;

        if ($valorFloat < 0) {
            responderErro('O valor não pode ser negativo.');
        }
        if ($valorFloat > 9999999.99) {
            responderErro('Valor muito alto. Máximo: R$ 9.999.999,99');
        }

        // 🔥 CORREÇÃO M0: grava DECIMAL, não string formatada
        $valorDecimal = number_format($valorFloat, 2, '.', '');
        $valorFormatado = number_format($valorFloat, 2, ',', '.');

        try {
            $stmt = $pdo->prepare("UPDATE servicos SET servalor = :v WHERE serid = :id");
            $ok = $stmt->execute([':v' => $valorDecimal, ':id' => $id]);

            if ($ok) {
                responderSucesso("Valor atualizado para R$ $valorFormatado", [
                    'valor_novo' => $valorFormatado
                ]);
            } else {
                responderErro('Erro ao atualizar valor.', 500);
            }
        } catch (PDOException $e) {
            error_log("Erro ao editar valor: " . $e->getMessage());
            responderErro('Erro ao atualizar valor.', 500);
        }
    }

    // ----- DELETAR -----
    if ($acao === 'deletar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) responderErro('ID inválido.');

        try {
            $stmt = $pdo->prepare("DELETE FROM servicos WHERE serid = :id");
            $ok = $stmt->execute([':id' => $id]);

            if ($ok) {
                responderSucesso('Serviço excluído com sucesso.');
            } else {
                responderErro('Erro ao excluir serviço.', 500);
            }
        } catch (PDOException $e) {
            error_log("Erro ao excluir serviço: " . $e->getMessage());
            responderErro('Erro ao excluir serviço.', 500);
        }
    }

    // ----- FINALIZAR -----
    if ($acao === 'finalizar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) responderErro('ID inválido.');

        try {
            $stmt = $pdo->prepare("
                SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor,
                       s.serdata_servico, s.serstatus_servico,
                       c.clinome, c.clitel
                FROM servicos s
                INNER JOIN clientes c ON c.cliid = s.cliid
                WHERE s.serid = :id
                LIMIT 1
            ");
            $stmt->execute([':id' => $id]);
            $servico = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$servico) {
                responderErro('Serviço não encontrado.', 404);
            }
            if (($servico['serstatus_servico'] ?? '') === 'finalizado') {
                responderErro('Este serviço já está finalizado.', 409);
            }

            $upd = $pdo->prepare("UPDATE servicos SET serstatus_servico = 'finalizado' WHERE serid = :id");
            $upd->execute([':id' => $id]);

            // WhatsApp
            $telefone = preg_replace('/\D/', '', $servico['clitel'] ?? '');
            $whatsappUrl = '';

            if (strlen($telefone) >= 10) {
                if (strlen($telefone) <= 11) {
                    $telefone = '55' . $telefone;
                }

                $msg = "Olá, " . $servico['clinome'] . "! 🎉\n\n"
                     . "Seu serviço de *" . ucfirst($servico['tipo_servico']) . "* na *DWS Envelopamento* foi *FINALIZADO*! ✅\n\n"
                     . "📋 *Resumo:*\n"
                     . "• Serviço: " . ucfirst($servico['tipo_servico']) . "\n"
                     . "• Descrição: " . $servico['serdescricao'] . "\n"
                     . "• Data: " . date('d/m/Y H:i', strtotime($servico['serdata_servico'])) . "\n\n"
                     . "Obrigado pela confiança! Qualquer dúvida, é só chamar. 🚗✨";

                $whatsappUrl = 'https://wa.me/' . $telefone . '?text=' . urlencode($msg);
            }

            responderSucesso('Serviço finalizado!', [
                'whatsapp_url' => $whatsappUrl,
                'cliente' => $servico['clinome'],
                'telefone' => $servico['clitel']
            ]);

        } catch (PDOException $e) {
            error_log("Erro ao finalizar serviço: " . $e->getMessage());
            responderErro('Erro ao finalizar serviço.', 500);
        }
    }

    responderErro('Ação desconhecida.');
}

// =============================================
// BUSCA DE DADOS
// =============================================
$busca = trim($_GET['busca'] ?? '');
$cliidFiltro = isset($_GET['cliid']) ? (int)$_GET['cliid'] : 0;
$clienteFiltro = null;

if ($cliidFiltro) {
    $stmt = $pdo->prepare(
        "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor,
                s.serdata_servico, s.serstatus_servico,
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
        "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor,
                s.serdata_servico, s.serstatus_servico,
                c.clinome, c.clitel
         FROM servicos s
         LEFT JOIN clientes c ON s.cliid = c.cliid
         WHERE c.clinome LIKE :b
         ORDER BY s.serid DESC"
    );
    $stmt->execute([':b' => "%$busca%"]);
} else {
    $stmt = $pdo->query(
        "SELECT s.serid, s.tipo_servico, s.serdescricao, s.servalor,
                s.serdata_servico, s.serstatus_servico,
                c.clinome, c.clitel
         FROM servicos s
         LEFT JOIN clientes c ON s.cliid = c.cliid
         ORDER BY s.serid DESC"
    );
}
$servicos = $stmt->fetchAll();

// =============================================
// TOTAIS — M0: unificado
// =============================================
$total_geral = 0.0;
$qtd_total = 0;

$todosServicos = $pdo->query("SELECT servalor FROM servicos")->fetchAll();
foreach ($todosServicos as $s) {
    $total_geral += valorParaFloat($s['servalor']);
    $qtd_total++;
}

$total_geral_formatado = formatBr($total_geral);

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
    <link rel="icon" type="image/png" href="../../img/logoabas.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DWS | Gerenciamento de Pedidos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #0a0a0a;
            background-image: radial-gradient(circle at 20% 50%, rgba(242, 53, 53, 0.05) 0%, transparent 50%),
                              radial-gradient(circle at 80% 50%, rgba(242, 53, 53, 0.03) 0%, transparent 50%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 30px;
            min-height: 100vh;
            color: #e8e8e8;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #1a1a1a; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #F23535; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #c91f2c; }

        .dashboard {
            display: flex;
            gap: 28px;
            max-width: 1600px;
            margin: 0 auto;
            align-items: flex-start;
        }

        .main-content {
            flex: 1;
            background: linear-gradient(145deg, #1e1e1e, #252525);
            padding: 32px;
            border-radius: 24px;
            border: 1px solid rgba(242, 53, 53, 0.15);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
        }

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
        }

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

        .search-wrapper input::placeholder { color: #555; }

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
            font-family: inherit;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(242, 53, 53, 0.3);
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

        .result-info strong { color: #fff; }
        .result-info .highlight { color: #F23535; }

        .table-wrap {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
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
            white-space: nowrap;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #d4d4d4;
            transition: all 0.2s ease;
        }

        tr:hover td { background: rgba(242, 53, 53, 0.05); }

        .cliente-cell { font-weight: 500; color: #fff; }
        .telefone-cell { color: #888; font-size: 13px; }
        .servico-cell { color: #F23535; font-weight: 500; }
        .valor-cell { font-weight: 600; color: #4CAF50; font-size: 15px; }
        .data-cell { color: #666; font-size: 13px; }

        .status-servico {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .status-servico.pendente {
            background: rgba(255, 152, 0, 0.15);
            color: #FF9800;
            border: 1px solid rgba(255, 152, 0, 0.3);
        }
        .status-servico.finalizado {
            background: rgba(76, 175, 80, 0.15);
            color: #4CAF50;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-edit,
        .btn-delete,
        .btn-finalizar {
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-edit {
            background: rgba(76, 175, 80, 0.15);
            color: #4CAF50;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }
        .btn-edit:hover {
            background: rgba(76, 175, 80, 0.3);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }

        .btn-delete {
            background: rgba(242, 53, 53, 0.15);
            color: #F23535;
            border: 1px solid rgba(242, 53, 53, 0.2);
        }
        .btn-delete:hover {
            background: rgba(242, 53, 53, 0.3);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(242, 53, 53, 0.2);
        }

        .btn-finalizar {
            background: rgba(76, 175, 80, 0.15);
            color: #4CAF50;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        .btn-finalizar:hover {
            background: rgba(76, 175, 80, 0.35);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        .btn-finalizar:disabled {
            opacity: 0.5;
            cursor: wait;
        }

        .empty-state { text-align: center; padding: 50px 20px; color: #555; }
        .empty-state i { font-size: 48px; margin-bottom: 15px; opacity: 0.3; }
        .empty-state p { font-size: 16px; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
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

        .sidebar-total {
            width: 320px;
            background: linear-gradient(145deg, #1a1a1a, #222);
            border-radius: 24px;
            border: 1px solid rgba(242, 53, 53, 0.1);
            padding: 28px;
            position: sticky;
            top: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .total-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(242, 53, 53, 0.1);
        }
        .total-header .icon { font-size: 40px; margin-bottom: 10px; display: block; }
        .total-header h3 { font-size: 18px; font-weight: 600; color: #e8e8e8; }
        .total-header .subtitle { font-size: 13px; color: #666; margin-top: 4px; }

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
            font-size: 32px;
            font-weight: 700;
            color: #4CAF50;
            letter-spacing: -1px;
            word-break: break-word;
        }
        .total-value .currency { font-size: 20px; color: #4CAF50; margin-right: 4px; }

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
        .total-details .detail-item:last-child { border-bottom: none; }
        .total-details .detail-label { color: #888; }
        .total-details .detail-value { color: #fff; font-weight: 500; }

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
        .service-item:hover { background: rgba(255, 255, 255, 0.06); }
        .service-item .client-name { color: #d4d4d4; font-size: 13px; font-weight: 500; }
        .service-item .client-name i { color: #F23535; margin-right: 6px; font-size: 12px; }
        .service-item .service-value { color: #4CAF50; font-weight: 600; font-size: 14px; }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-overlay.active { display: flex; }

        .modal-content {
            background: linear-gradient(145deg, #1e1e1e, #2a2a2a);
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(242, 53, 53, 0.2);
            width: 500px;
            max-width: 95%;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.8);
        }

        .modal-content h2 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .modal-content h2 i { color: #F23535; }

        .modal-info {
            background: rgba(255, 255, 255, 0.03);
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .modal-info p { color: #aaa; font-size: 14px; margin: 4px 0; }
        .modal-info strong { color: #fff; }

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
        .modal-content input::placeholder { color: #444; font-weight: 400; font-size: 16px; }

        .modal-actions { display: flex; gap: 12px; margin-top: 10px; }
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
        .btn-cancel-modal { background: rgba(255, 255, 255, 0.05); color: #888; }
        .btn-cancel-modal:hover { background: rgba(255, 255, 255, 0.1); color: #fff; }

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
            max-width: 400px;
            backdrop-filter: blur(10px);
            align-items: center;
            gap: 12px;
        }
        .toast.success {
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        .toast.error {
            background: linear-gradient(135deg, #F23535, #c91f2c);
            border: 1px solid rgba(242, 53, 53, 0.3);
        }
        .toast.warning {
            background: linear-gradient(135deg, #FF9800, #E65100);
            border: 1px solid rgba(255, 152, 0, 0.3);
        }

        @media (max-width: 1200px) {
            .dashboard { flex-direction: column; }
            .sidebar-total { width: 100%; position: relative; top: 0; }
        }

        @media (max-width: 768px) {
            body { padding: 15px; }
            .main-content { padding: 20px; }
            .page-header h1 { font-size: 22px; }
            .search-container { flex-direction: column; }
            .search-wrapper { min-width: 100%; }
            .btn-search, .btn-clear { width: 100%; justify-content: center; }
            .modal-content { padding: 25px; margin: 15px; }
            .modal-actions { flex-direction: column; }
            .total-value .amount { font-size: 26px; }
            .sidebar-total { padding: 20px; }
            table { font-size: 13px; min-width: 900px; }
            th, td { padding: 10px 8px; }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="main-content">
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

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Telefone</th>
                            <th>Serviço</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th style="text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($servicos): foreach ($servicos as $r):
                        $statusServico = $r['serstatus_servico'] ?? 'pendente';
                    ?>
                        <tr>
                            <td class="cliente-cell"><?= htmlspecialchars($r['clinome'] ?? '—') ?></td>
                            <td class="telefone-cell"><?= htmlspecialchars($r['clitel'] ?? '—') ?></td>
                            <td class="servico-cell"><?= htmlspecialchars($r['tipo_servico']) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth($r['serdescricao'] ?? '', 0, 50, '…')) ?></td>
                            <td class="valor-cell">R$ <?= formatBr(valorParaFloat($r['servalor'] ?? 0)) ?></td>
                            <td class="data-cell"><?= date('d/m/Y H:i', strtotime($r['serdata_servico'])) ?></td>
                            <td>
                                <span class="status-servico <?= htmlspecialchars($statusServico) ?>">
                                    <?= $statusServico === 'finalizado' ? '✅ Finalizado' : '⏳ Pendente' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons" style="justify-content: center;">
                                    <?php if ($statusServico !== 'finalizado'): ?>
                                        <button class="btn-finalizar"
                                                onclick="finalizarServico(<?= (int)$r['serid'] ?>, <?= htmlspecialchars(json_encode($r['clinome']), ENT_QUOTES) ?>)">
                                            <i class="fas fa-check-double"></i> Finalizar
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn-edit" onclick="abrirEditar(<?= htmlspecialchars(json_encode($r), ENT_QUOTES) ?>)">
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
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p><?= $busca ? "Nenhum pedido encontrado para \"" . htmlspecialchars($busca) . "\"" : 'Nenhum pedido cadastrado ainda.' ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sidebar-total">
            <div class="total-header">
                <span class="icon">💰</span>
                <h3>Faturamento Total</h3>
                <div class="subtitle">Visão geral financeira (todos os status)</div>
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
                    <span class="service-value">R$ <?= formatBr(valorParaFloat($u['servalor'] ?? 0)) ?></span>
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

    <div class="modal-overlay" id="modalEditar">
        <div class="modal-content">
            <h2>
                <i class="fas fa-edit"></i>
                Editar Valor do Serviço
            </h2>
            <div class="modal-info" id="infoServico"></div>
            <div class="current-value" id="valorAtual"></div>
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

    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage">Mensagem</span>
    </div>

    <script>
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');

        toast.className = 'toast ' + type;
        toastMessage.textContent = message;

        const icon = toast.querySelector('i');
        if (type === 'success') icon.className = 'fas fa-check-circle';
        else if (type === 'error') icon.className = 'fas fa-exclamation-circle';
        else if (type === 'warning') icon.className = 'fas fa-exclamation-triangle';
        else icon.className = 'fas fa-info-circle';

        toast.style.display = 'flex';

        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 3500);
    }

    function abrirEditar(servico) {
        document.getElementById('edit_id').value = servico.serid;
        document.getElementById('edit_valor_antigo').value = servico.servalor || '0';
        document.getElementById('edit_valor').value = '';

        document.getElementById('infoServico').innerHTML = `
            <p><i class="fas fa-user" style="color: #F23535; width: 18px;"></i> <strong>Cliente:</strong> ${escapeHtml(servico.clinome || '—')}</p>
            <p><i class="fas fa-concierge-bell" style="color: #F23535; width: 18px;"></i> <strong>Serviço:</strong> ${escapeHtml(servico.tipo_servico)}</p>
            <p><i class="fas fa-align-left" style="color: #F23535; width: 18px;"></i> <strong>Descrição:</strong> ${escapeHtml((servico.serdescricao || '').substring(0, 80))}</p>
        `;

        document.getElementById('valorAtual').innerHTML = `
            <i class="fas fa-tag" style="margin-right: 8px;"></i>
            Valor atual: <strong>R$ ${escapeHtml(servico.servalor || '0,00')}</strong>
        `;

        document.getElementById('modalEditar').classList.add('active');

        setTimeout(() => {
            document.getElementById('edit_valor').focus();
            document.getElementById('edit_valor').select();
        }, 300);
    }

    function escapeHtml(t) {
        const d = document.createElement('div');
        d.textContent = (t === null || t === undefined) ? '' : String(t);
        return d.innerHTML;
    }

    function fecharModal() {
        document.getElementById('modalEditar').classList.remove('active');
    }

    document.getElementById('modalEditar').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') fecharModal();
    });

    async function salvarValor() {
        const inputValor = document.getElementById('edit_valor');
        const valor = inputValor.value.trim();

        if (!valor) {
            showToast('Por favor, digite um valor.', 'warning');
            inputValor.focus();
            return;
        }

        if (!/^[\d.,]+$/.test(valor)) {
            showToast('Formato inválido. Use apenas números, vírgula ou ponto.', 'error');
            inputValor.focus();
            return;
        }

        if ((valor.match(/,/g) || []).length > 1) {
            showToast('Use apenas uma vírgula como separador decimal.', 'error');
            inputValor.focus();
            return;
        }

        if ((valor.match(/\./g) || []).length > 1) {
            showToast('Use apenas um ponto como separador decimal.', 'error');
            inputValor.focus();
            return;
        }

        if (valor.includes(',') && valor.includes('.')) {
            showToast('Use vírgula OU ponto, não os dois.', 'error');
            inputValor.focus();
            return;
        }

        let valorNum = parseFloat(valor.replace(',', '.'));
        if (isNaN(valorNum)) {
            showToast('Valor inválido.', 'error');
            inputValor.focus();
            return;
        }

        if (valorNum < 0) {
            showToast('O valor não pode ser negativo.', 'error');
            inputValor.focus();
            return;
        }

        if (valorNum > 9999999.99) {
            showToast('Valor muito alto. Máximo: R$ 9.999.999,99', 'error');
            inputValor.focus();
            return;
        }

        const fd = new FormData();
        fd.append('acao', 'editar');
        fd.append('id', document.getElementById('edit_id').value);
        fd.append('valor', valor);

        const btnSalvar = document.querySelector('.btn-save');
        const txtOriginal = btnSalvar?.innerHTML || '';
        if (btnSalvar) {
            btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            btnSalvar.disabled = true;
        }

        try {
            const res = await fetch('GerenciarServico.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.status === 'sucesso') {
                showToast(data.mensagem, 'success');
                fecharModal();
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(data.mensagem || 'Erro ao salvar valor.', 'error');
            }
        } catch (error) {
            showToast('Erro de conexão. Tente novamente.', 'error');
        } finally {
            if (btnSalvar) {
                btnSalvar.innerHTML = txtOriginal;
                btnSalvar.disabled = false;
            }
        }
    }

    async function deletar(id) {
        if (!confirm('⚠️ Tem certeza que deseja excluir este pedido?\n\nEsta ação não pode ser desfeita!')) return;

        const fd = new FormData();
        fd.append('acao', 'deletar');
        fd.append('id', id);

        try {
            const res = await fetch('GerenciarServico.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.status === 'sucesso') {
                showToast(data.mensagem, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.mensagem || 'Erro ao excluir serviço.', 'error');
            }
        } catch (error) {
            showToast('Erro de conexão. Tente novamente.', 'error');
        }
    }

    async function finalizarServico(id, nomeCliente) {
        if (!confirm(
            `✅ Finalizar o serviço do cliente "${nomeCliente}"?\n\n` +
            `Isso vai:\n` +
            `• Marcar como FINALIZADO no sistema\n` +
            `• Abrir WhatsApp com mensagem pronta para o cliente`
        )) return;

        const fd = new FormData();
        fd.append('acao', 'finalizar');
        fd.append('id', id);

        const btn = event?.target?.closest('.btn-finalizar');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Finalizando...';
        }

        try {
            const res = await fetch('GerenciarServico.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.status === 'sucesso') {
                showToast(data.mensagem, 'success');

                if (data.whatsapp_url) {
                    setTimeout(() => window.open(data.whatsapp_url, '_blank'), 600);
                    setTimeout(() => showToast(`📱 WhatsApp aberto para ${data.cliente}`, 'success'), 1200);
                } else {
                    setTimeout(() => showToast('⚠️ Cliente sem telefone cadastrado.', 'warning'), 800);
                }

                setTimeout(() => location.reload(), 2500);
            } else {
                showToast(data.mensagem || 'Erro ao finalizar.', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-double"></i> Finalizar';
                }
            }
        } catch (e) {
            showToast('Erro de conexão.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-double"></i> Finalizar';
            }
        }
    }

    document.getElementById('edit_valor').addEventListener('input', function(e) {
        let value = this.value.replace(/[^0-9,.]/g, '');

        const primeiroSep = value.search(/[,.]/);
        if (primeiroSep !== -1) {
            const antes = value.substring(0, primeiroSep + 1);
            const depois = value.substring(primeiroSep + 1).replace(/[,.]/g, '');
            value = antes + depois;
        }

        const sep = value.search(/[,.]/);
        if (sep !== -1) {
            const inteiro = value.substring(0, sep);
            const decimal = value.substring(sep + 1).substring(0, 2);
            value = inteiro + value.charAt(sep) + decimal;
        }

        this.value = value;
    });

    document.getElementById('edit_valor').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            salvarValor();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
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
    });
    </script>
</body>
</html>