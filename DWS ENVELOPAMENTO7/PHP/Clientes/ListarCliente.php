<?php
// =============================================
// ListarCliente.php  (Área ADM - VERSÃO PREMIUM)
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// VERIFICA SE É ADMIN
// =============================================
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../telas/ADM/login.html');
    exit;
}

// =============================================
// AÇÕES POST (JSON)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $acao = $_POST['acao'] ?? '';

    // ----- CONTAR SERVIÇOS DO CLIENTE -----
    if ($acao === 'contar_servicos') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'ID inválido.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM servicos WHERE cliid = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['status' => 'sucesso', 'total' => (int)$stmt->fetchColumn()]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao contar serviços.']);
        }
        exit;
    }

    // ----- DELETAR -----
    if ($acao === 'deletar') {
        $id = (int)($_POST['id'] ?? 0);
        $deletarServicos = ($_POST['deletar_servicos'] ?? 'nao') === 'sim';

        if ($id <= 0) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'ID inválido.']);
            exit;
        }

        try {
            // Conta serviços vinculados
            $check = $pdo->prepare("SELECT COUNT(*) FROM servicos WHERE cliid = :id");
            $check->execute([':id' => $id]);
            $totalServicos = (int)$check->fetchColumn();

            // Se tem serviços e NÃO confirmou deletar, recusa
            if ($totalServicos > 0 && !$deletarServicos) {
                echo json_encode([
                    'status' => 'precisa_confirmar',
                    'mensagem' => "Este cliente possui $totalServicos serviço(s). Confirme a exclusão.",
                    'total_servicos' => $totalServicos
                ]);
                exit;
            }

            // Inicia transação (garante que ou deleta tudo, ou nada)
            $pdo->beginTransaction();

            // Deleta serviços primeiro (se confirmado)
            if ($deletarServicos) {
                $delServ = $pdo->prepare("DELETE FROM servicos WHERE cliid = :id");
                $delServ->execute([':id' => $id]);
            }

            // Deleta cliente
            $delCli = $pdo->prepare("DELETE FROM clientes WHERE cliid = :id");
            $delCli->execute([':id' => $id]);

            $pdo->commit();

            $msg = 'Cliente excluído com sucesso.';
            if ($totalServicos > 0) {
                $msg = "Cliente e $totalServicos serviço(s) excluídos com sucesso.";
            }

            echo json_encode(['status' => 'sucesso', 'mensagem' => $msg]);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("Erro ao deletar cliente: " . $e->getMessage());
            echo json_encode([
                'status' => 'erro',
                'mensagem' => 'Erro ao excluir cliente.'
            ]);
        }
        exit;
    }

    // ----- EDITAR -----
    if ($acao === 'editar') {
        $id       = (int)($_POST['id'] ?? 0);
        $nome     = trim($_POST['nome'] ?? '');
        $cpf      = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $senha    = trim($_POST['senha'] ?? '');

        try {
            if ($senha) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE clientes 
                    SET clinome = :n, clisenha = :s, clicpf = :c, clitel = :t, cliendereco = :e 
                    WHERE cliid = :id
                ");
                $stmt->execute([
                    ':n' => $nome,
                    ':s' => $senhaHash,
                    ':c' => $cpf,
                    ':t' => $telefone,
                    ':e' => $endereco,
                    ':id' => $id
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE clientes 
                    SET clinome = :n, clicpf = :c, clitel = :t, cliendereco = :e 
                    WHERE cliid = :id
                ");
                $stmt->execute([
                    ':n' => $nome,
                    ':c' => $cpf,
                    ':t' => $telefone,
                    ':e' => $endereco,
                    ':id' => $id
                ]);
            }
            echo json_encode(['status' => 'sucesso', 'mensagem' => 'Cliente atualizado com sucesso.']);
        } catch (PDOException $e) {
            error_log("Erro ao editar cliente: " . $e->getMessage());
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar cliente.']);
        }
        exit;
    }

    echo json_encode(['status' => 'erro', 'mensagem' => 'Ação desconhecida.']);
    exit;
}

// =============================================
// BUSCA CLIENTES
// =============================================
$busca = trim($_GET['busca'] ?? '');

$sqlBase = "
    SELECT 
        c.cliid, 
        c.clinome, 
        c.clicpf, 
        c.clitel, 
        c.cliendereco,
        (SELECT COUNT(*) FROM servicos s WHERE s.cliid = c.cliid) AS total_servicos,
        (SELECT COUNT(*) FROM servicos s WHERE s.cliid = c.cliid AND s.serstatus_servico = 'finalizado') AS servicos_finalizados
    FROM clientes c
";

if ($busca) {
    $stmt = $pdo->prepare($sqlBase . " WHERE c.clinome LIKE :b ORDER BY c.cliid DESC");
    $stmt->execute([':b' => "%$busca%"]);
} else {
    $stmt = $pdo->query($sqlBase . " ORDER BY c.cliid DESC");
}

$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($clientes);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar Clientes - DWS Admin</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Space Grotesk', sans-serif;
    background: #050505;
    color: #fff;
    min-height: 100vh;
    padding: 30px 20px;
    background-image:
        radial-gradient(ellipse at top left, rgba(242, 53, 53, 0.08) 0%, transparent 50%),
        radial-gradient(ellipse at bottom right, rgba(242, 53, 53, 0.05) 0%, transparent 50%);
    background-attachment: fixed;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
}

/* HEADER */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    flex-wrap: wrap;
    gap: 18px;
}

.page-header h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.8rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -0.5px;
    margin: 0;
}

.page-header h1 i {
    color: #f23535;
    margin-right: 12px;
}

.page-header h1 span {
    background: linear-gradient(135deg, #fff, #f23535);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.btn-voltar {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.05);
    color: #aaa;
    text-decoration: none;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: 0.3s;
    border: 1px solid rgba(255,255,255,0.05);
}

.btn-voltar:hover {
    background: rgba(242,53,53,0.1);
    color: #f23535;
    transform: translateX(-5px);
}

/* SEARCH */
.search-section {
    background: linear-gradient(145deg, rgba(20,20,20,0.8), rgba(10,10,10,0.9));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px;
    padding: 20px 25px;
    margin-bottom: 30px;
    backdrop-filter: blur(20px);
}

.search-form {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.search-wrapper {
    flex: 1;
    min-width: 250px;
    position: relative;
}

.search-wrapper i {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.search-wrapper input {
    width: 100%;
    padding: 14px 18px 14px 48px;
    background: #1a1a1a;
    border: 2px solid #333;
    border-radius: 12px;
    color: #fff;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    transition: 0.3s;
}

.search-wrapper input:focus {
    border-color: #f23535;
    box-shadow: 0 0 0 4px rgba(242,53,53,0.1);
}

.btn-search {
    padding: 14px 28px;
    background: linear-gradient(135deg, #f23535, #c91f2c);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    font-size: 0.8rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-search:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(242,53,53,0.4);
}

.btn-limpar {
    padding: 14px 22px;
    background: rgba(255,255,255,0.05);
    color: #aaa;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85rem;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-limpar:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}

/* STATS BAR */
.stats-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    background: linear-gradient(145deg, rgba(20,20,20,0.8), rgba(10,10,10,0.9));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 18px 28px;
    margin-bottom: 30px;
}

.stats-bar .total {
    color: #888;
    font-size: 0.9rem;
}

.stats-bar .total strong {
    font-family: 'Orbitron', sans-serif;
    color: #f23535;
    font-size: 1.4rem;
    margin-left: 8px;
}

.btn-novo {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #4CAF50, #388E3C);
    color: #fff;
    text-decoration: none;
    border-radius: 12px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    font-size: 0.75rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: 0.3s;
}

.btn-novo:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(76,175,80,0.3);
}

/* TABLE */
.table-wrap {
    background: linear-gradient(145deg, rgba(20,20,20,0.8), rgba(10,10,10,0.9));
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.06);
    overflow: hidden;
    backdrop-filter: blur(20px);
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
}

thead {
    background: rgba(242,53,53,0.08);
}

th {
    text-align: left;
    padding: 18px 20px;
    color: #aaa;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
}

td {
    padding: 18px 20px;
    color: #ccc;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    font-size: 0.9rem;
}

tbody tr {
    transition: 0.2s;
}

tbody tr:hover td {
    background: rgba(242,53,53,0.04);
}

.cell-id {
    font-family: 'Orbitron', sans-serif;
    color: #888;
    font-weight: 600;
    font-size: 0.85rem;
}

.cell-nome {
    color: #fff;
    font-weight: 600;
}

.cell-cpf {
    font-family: 'Courier New', monospace;
    color: #aaa;
    font-size: 0.85rem;
}

.campo-vazio {
    color: #666;
    font-style: italic;
    font-size: 0.8rem;
    background: rgba(255,255,255,0.03);
    padding: 3px 12px;
    border-radius: 50px;
    border: 1px dashed rgba(255,255,255,0.1);
    display: inline-block;
}

/* ACTIONS */
.actions {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.btn-action {
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 0.75rem;
    cursor: pointer;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: inherit;
}

.btn-edit {
    background: rgba(76,175,80,0.15);
    color: #4CAF50;
    border: 1px solid rgba(76,175,80,0.25);
}

.btn-edit:hover {
    background: rgba(76,175,80,0.3);
    transform: scale(1.05);
}

.btn-delete {
    background: rgba(242,53,53,0.15);
    color: #f23535;
    border: 1px solid rgba(242,53,53,0.25);
}

.btn-delete:hover {
    background: rgba(242,53,53,0.3);
    transform: scale(1.05);
}

/* EMPTY */
.empty-state {
    text-align: center;
    padding: 70px 20px;
    color: #666;
}

.empty-state i {
    font-size: 4rem;
    color: rgba(242,53,53,0.15);
    margin-bottom: 20px;
    display: block;
}

.empty-state h3 {
    font-family: 'Orbitron', sans-serif;
    color: #888;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}

.empty-state p {
    color: #555;
    font-size: 0.9rem;
}

/* MODAL */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(8px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-overlay.active {
    display: flex;
}

.modal-box {
    background: linear-gradient(145deg, #1a1a1a, #0a0a0a);
    border-radius: 24px;
    padding: 40px;
    width: 500px;
    max-width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    animation: modalIn 0.3s ease;
    border: 1px solid rgba(242,53,53,0.3);
}

.modal-box::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 24px;
    padding: 2px;
    background: linear-gradient(var(--angle, 0deg), transparent, #f23535, #ff6b6b, #f23535, transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: ledRotate 4s linear infinite;
    pointer-events: none;
}

@keyframes ledRotate {
    0% { --angle: 0deg; }
    100% { --angle: 360deg; }
}

@keyframes modalIn {
    from { opacity: 0; transform: scale(0.9) translateY(-20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-box h2 {
    font-family: 'Orbitron', sans-serif;
    color: #fff;
    font-size: 1.3rem;
    margin-bottom: 25px;
    text-transform: uppercase;
    letter-spacing: -0.3px;
}

.modal-box h2 i {
    color: #f23535;
    margin-right: 12px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    color: #aaa;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.form-group input {
    width: 100%;
    padding: 14px 18px;
    background: #1a1a1a;
    border: 2px solid #333;
    border-radius: 12px;
    color: #fff;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    transition: 0.3s;
}

.form-group input:focus {
    border-color: #f23535;
    box-shadow: 0 0 0 4px rgba(242,53,53,0.1);
}

.form-group .helper {
    color: #666;
    font-size: 0.75rem;
    margin-top: 6px;
}

.modal-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

.btn-modal {
    flex: 1;
    padding: 14px;
    border: none;
    border-radius: 12px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    font-size: 0.8rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    transition: 0.3s;
}

.btn-salvar {
    background: linear-gradient(135deg, #4CAF50, #388E3C);
    color: #fff;
}

.btn-salvar:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(76,175,80,0.3);
}

.btn-cancelar {
    background: rgba(255,255,255,0.05);
    color: #888;
    border: 1px solid rgba(255,255,255,0.1);
}

.btn-cancelar:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}

/* ===== MODAL CONFIRMAR EXCLUSÃO ===== */
.modal-confirm {
    text-align: center;
    padding: 40px 35px 30px !important;
    width: 480px;
    max-width: 95%;
}

.modal-icon-warn {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.2), rgba(255, 152, 0, 0.05));
    border: 2px solid rgba(255, 152, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2.2rem;
    color: #FF9800;
    animation: pulseWarn 2s ease-in-out infinite;
}

@keyframes pulseWarn {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.3); }
    50% { transform: scale(1.05); box-shadow: 0 0 0 12px rgba(255, 152, 0, 0); }
}

.modal-confirm h2 {
    font-family: 'Orbitron', sans-serif;
    color: #fff;
    font-size: 1.3rem;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: -0.3px;
}

.modal-confirm h2 i { color: #FF9800; margin-right: 8px; }

.modal-confirm > p {
    color: #aaa;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 20px;
}

.modal-info-cliente {
    background: rgba(255, 152, 0, 0.08);
    border: 1px solid rgba(255, 152, 0, 0.25);
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 25px;
    text-align: left;
    font-size: 0.9rem;
    color: #ddd;
    line-height: 1.6;
}

.modal-info-cliente strong { color: #fff; }
.modal-info-cliente .destaque {
    color: #FF9800;
    font-weight: 700;
}

.modal-actions-triplo,
.modal-actions-duplo {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.modal-actions-triplo .btn-modal { flex: 1; min-width: 0; }
.modal-actions-duplo .btn-modal { flex: 1; }

.btn-so-cliente {
    background: rgba(255, 152, 0, 0.15);
    color: #FF9800;
    border: 1px solid rgba(255, 152, 0, 0.35);
}
.btn-so-cliente:hover {
    background: rgba(255, 152, 0, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(255, 152, 0, 0.3);
}

.btn-tudo {
    background: linear-gradient(135deg, #f23535, #c91f2c);
    color: #fff;
    border: none;
}
.btn-tudo:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(242, 53, 53, 0.4);
}

@media (max-width: 500px) {
    .modal-actions-triplo,
    .modal-actions-duplo {
        flex-direction: column;
    }
    .modal-confirm { padding: 30px 22px 22px !important; }
}

/* TOAST */
.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    padding: 18px 28px;
    border-radius: 14px;
    font-weight: 600;
    color: #fff;
    font-size: 0.95rem;
    z-index: 10000;
    display: none;
    align-items: center;
    gap: 12px;
    box-shadow: 0 15px 50px rgba(0,0,0,0.5);
    max-width: 400px;
    animation: toastIn 0.3s ease;
}

.toast.show { display: flex; }

.toast.success {
    background: linear-gradient(135deg, #4CAF50, #388E3C);
    border-left: 4px solid #66BB6A;
}

.toast.error {
    background: linear-gradient(135deg, #f23535, #c91f2c);
    border-left: 4px solid #ff5555;
}

@keyframes toastIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* RESPONSIVO */
@media (max-width: 768px) {
    body { padding: 20px 12px; }
    .page-header h1 { font-size: 1.3rem; }
    .search-form { flex-direction: column; }
    .search-wrapper { min-width: 100%; }
    .btn-search, .btn-limpar, .btn-novo { width: 100%; justify-content: center; }
    .stats-bar { flex-direction: column; text-align: center; }
    .actions { flex-direction: column; }
    .btn-action { justify-content: center; }
    .modal-box { padding: 28px 20px; }
    .modal-actions { flex-direction: column; }
}
</style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-users"></i> Gerenciar <span>Clientes</span></h1>
        <a href="../../telas/ADM/principalFUN.html" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- SEARCH -->
    <div class="search-section">
        <form class="search-form" method="GET">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="busca" placeholder="Pesquisar por nome..." value="<?= htmlspecialchars($busca) ?>">
            </div>
            <button class="btn-search" type="submit">
                <i class="fas fa-search"></i> BUSCAR
            </button>
            <?php if ($busca): ?>
                <a href="ListarCliente.php" class="btn-limpar">
                    <i class="fas fa-times"></i> Limpar
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- STATS -->
    <div class="stats-bar">
        <div class="total">
            <i class="fas fa-user-friends" style="color: #f23535; margin-right: 8px;"></i>
            Total de clientes: <strong><?= $total ?></strong>
        </div>
        <a href="../../telas/Cliente/CadastroCliente.html" class="btn-novo">
            <i class="fas fa-user-plus"></i> NOVO CLIENTE
        </a>
    </div>

    <!-- TABLE -->
    <div class="table-wrap">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clientes)): ?>
                        <?php foreach ($clientes as $r): ?>
                            <tr>
                                <td><span class="cell-id">#<?= (int)$r['cliid'] ?></span></td>
                                <td><span class="cell-nome"><?= htmlspecialchars($r['clinome']) ?></span></td>
                                <td>
                                    <?php if (empty(trim($r['clicpf'] ?? ''))): ?>
                                        <span class="campo-vazio">Não informado</span>
                                    <?php else: ?>
                                        <span class="cell-cpf"><?= htmlspecialchars($r['clicpf']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (empty(trim($r['clitel'] ?? ''))): ?>
                                        <span class="campo-vazio">Não informado</span>
                                    <?php else: ?>
                                        <?= htmlspecialchars($r['clitel']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (empty(trim($r['cliendereco'] ?? ''))): ?>
                                        <span class="campo-vazio">Não informado</span>
                                    <?php else: ?>
                                        <?= htmlspecialchars($r['cliendereco']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-action btn-edit" onclick='abrirEditar(<?= json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button class="btn-action btn-delete" onclick='deletar(<?= (int)$r["cliid"] ?>, <?= json_encode($r["clinome"], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <h3>Nenhum cliente</h3>
                                    <p><?= $busca ? "Nenhum resultado para \"$busca\"" : 'Nenhum cliente cadastrado ainda.' ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal-overlay" id="modalEditar">
    <div class="modal-box">
        <h2><i class="fas fa-user-edit"></i> Editar Cliente</h2>
        <input type="hidden" id="edit_id">

        <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" id="edit_nome" placeholder="Nome do cliente" required>
        </div>

        <div class="form-group">
            <label>Nova Senha</label>
            <input type="password" id="edit_senha" placeholder="Deixe em branco para manter">
            <div class="helper">⚠️ Deixe vazio para não alterar</div>
        </div>

        <div class="form-group">
            <label>CPF</label>
            <input type="text" id="edit_cpf" placeholder="000.000.000-00">
        </div>

        <div class="form-group">
            <label>Telefone</label>
            <input type="text" id="edit_telefone" placeholder="(00) 00000-0000">
        </div>

        <div class="form-group">
            <label>Endereço</label>
            <input type="text" id="edit_endereco" placeholder="Rua, número, bairro, cidade">
        </div>

        <div class="modal-actions">
            <button class="btn-modal btn-salvar" onclick="salvarEdicao()">
                <i class="fas fa-check"></i> SALVAR
            </button>
            <button class="btn-modal btn-cancelar" onclick="fecharModal()">
                <i class="fas fa-times"></i> CANCELAR
            </button>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMAR EXCLUSÃO -->
<div class="modal-overlay" id="modalExcluir">
    <div class="modal-box modal-confirm">
        <div class="modal-icon-warn">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h2 id="modalTitulo">Excluir cliente?</h2>
        <p id="modalMensagem">Esta ação não pode ser desfeita.</p>

        <div class="modal-info-cliente" id="modalInfoCliente"></div>

        <div class="modal-actions-triplo" id="modalAcoesTriplo" style="display: none;">
            <button class="btn-modal btn-cancelar" onclick="fecharModalExcluir()">
                <i class="fas fa-times"></i> CANCELAR
            </button>
            <button class="btn-modal btn-so-cliente" onclick="confirmarExclusao(false)">
                <i class="fas fa-user-minus"></i> SÓ O CLIENTE
            </button>
            <button class="btn-modal btn-tudo" onclick="confirmarExclusao(true)">
                <i class="fas fa-trash-alt"></i> EXCLUIR TUDO
            </button>
        </div>

        <div class="modal-actions-duplo" id="modalAcoesDuplo" style="display: none;">
            <button class="btn-modal btn-cancelar" onclick="fecharModalExcluir()">
                <i class="fas fa-times"></i> CANCELAR
            </button>
            <button class="btn-modal btn-tudo" onclick="confirmarExclusao(false)">
                <i class="fas fa-trash-alt"></i> EXCLUIR
            </button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastText">Mensagem</span>
</div>

<script>
// =============================================
// LED ROTATION no modal
// =============================================
const modalBox = document.querySelector('.modal-box');
let angle = 0;
function animarLed() {
    angle = (angle + 1.5) % 360;
    modalBox.style.setProperty('--angle', angle + 'deg');
    requestAnimationFrame(animarLed);
}
animarLed();

// =============================================
// TOAST
// =============================================
function showToast(msg, tipo = 'success') {
    const toast = document.getElementById('toast');
    const text = document.getElementById('toastText');
    text.textContent = msg;
    toast.className = 'toast show ' + tipo;

    const icon = toast.querySelector('i');
    icon.className = tipo === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => {
        toast.classList.remove('show');
    }, 3500);
}

// =============================================
// HELPER
// =============================================
function escapeHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

// =============================================
// MODAL EDITAR
// =============================================
function abrirEditar(dados) {
    document.getElementById('edit_id').value = dados.cliid;
    document.getElementById('edit_nome').value = dados.clinome || '';
    document.getElementById('edit_senha').value = '';
    document.getElementById('edit_cpf').value = dados.clicpf || '';
    document.getElementById('edit_telefone').value = dados.clitel || '';
    document.getElementById('edit_endereco').value = dados.cliendereco || '';
    document.getElementById('modalEditar').classList.add('active');
}

function fecharModal() {
    document.getElementById('modalEditar').classList.remove('active');
}

document.getElementById('modalEditar').addEventListener('click', function(e) {
    if (e.target === this) fecharModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModal();
        fecharModalExcluir();
    }
});

// =============================================
// SALVAR EDIÇÃO
// =============================================
async function salvarEdicao() {
    const nome = document.getElementById('edit_nome').value.trim();

    if (!nome || nome.length < 3) {
        showToast('Nome é obrigatório (mín. 3 letras).', 'error');
        return;
    }

    const fd = new FormData();
    fd.append('acao', 'editar');
    fd.append('id', document.getElementById('edit_id').value);
    fd.append('nome', nome);
    fd.append('senha', document.getElementById('edit_senha').value);
    fd.append('cpf', document.getElementById('edit_cpf').value);
    fd.append('telefone', document.getElementById('edit_telefone').value);
    fd.append('endereco', document.getElementById('edit_endereco').value);

    try {
        const res = await fetch('ListarCliente.php', { method: 'POST', body: fd });
        const data = await res.json();
        showToast(data.mensagem, data.status === 'sucesso' ? 'success' : 'error');
        if (data.status === 'sucesso') {
            setTimeout(() => location.reload(), 1200);
        }
    } catch (e) {
        showToast('Erro de conexão.', 'error');
    }
}

// =============================================
// EXCLUSÃO DE CLIENTE — MODAL DE CONFIRMAÇÃO
// =============================================
let _clienteParaExcluir = null;

async function deletar(id, nome) {
    _clienteParaExcluir = { id, nome };

    // Conta serviços do cliente antes de abrir o modal
    const fdCount = new FormData();
    fdCount.append('acao', 'contar_servicos');
    fdCount.append('id', id);

    let totalServicos = 0;
    try {
        const res = await fetch('ListarCliente.php', { method: 'POST', body: fdCount });
        const data = await res.json();
        if (data.status === 'sucesso') totalServicos = data.total || 0;
    } catch (e) {
        console.warn('Não foi possível contar serviços:', e);
    }

    // Monta o modal
    const info = document.getElementById('modalInfoCliente');
    const titulo = document.getElementById('modalTitulo');
    const msg = document.getElementById('modalMensagem');
    const acoesTriplo = document.getElementById('modalAcoesTriplo');
    const acoesDuplo = document.getElementById('modalAcoesDuplo');

    if (totalServicos > 0) {
        titulo.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Atenção!';
        msg.textContent = 'Este cliente possui serviços cadastrados.';
        info.innerHTML = `
            👤 <strong>${escapeHtml(nome)}</strong><br>
            📋 Possui <span class="destaque">${totalServicos} serviço(s)</span> vinculado(s).<br><br>
            <span style="color: #888; font-size: 0.85rem;">
                • <strong>SÓ O CLIENTE</strong>: exclui o cliente, mas mantém os serviços.<br>
                • <strong>EXCLUIR TUDO</strong>: apaga o cliente E todos os serviços dele.
            </span>
        `;
        acoesTriplo.style.display = 'flex';
        acoesDuplo.style.display = 'none';
    } else {
        titulo.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Excluir cliente?';
        msg.textContent = 'Esta ação não pode ser desfeita.';
        info.innerHTML = `
            👤 <strong>${escapeHtml(nome)}</strong><br>
            <span style="color: #888; font-size: 0.85rem;">Nenhum serviço vinculado.</span>
        `;
        acoesTriplo.style.display = 'none';
        acoesDuplo.style.display = 'flex';
    }

    document.getElementById('modalExcluir').classList.add('active');
}

function fecharModalExcluir() {
    document.getElementById('modalExcluir').classList.remove('active');
    _clienteParaExcluir = null;
}

async function confirmarExclusao(deletarServicos) {
    if (!_clienteParaExcluir) return;

    const { id, nome } = _clienteParaExcluir;

    const fd = new FormData();
    fd.append('acao', 'deletar');
    fd.append('id', id);
    fd.append('deletar_servicos', deletarServicos ? 'sim' : 'nao');

    try {
        const res = await fetch('ListarCliente.php', { method: 'POST', body: fd });
        const data = await res.json();

        // Se o backend pediu confirmação, força deletar tudo (fallback raro)
        if (data.status === 'precisa_confirmar') {
            return confirmarExclusao(true);
        }

        showToast(data.mensagem, data.status === 'sucesso' ? 'success' : 'error');

        if (data.status === 'sucesso') {
            fecharModalExcluir();
            setTimeout(() => location.reload(), 1300);
        }
    } catch (e) {
        console.error(e);
        showToast('Erro de conexão.', 'error');
    }
}

// Fecha modal clicando fora
document.getElementById('modalExcluir').addEventListener('click', function(e) {
    if (e.target === this) fecharModalExcluir();
});
</script>

</body>
</html>