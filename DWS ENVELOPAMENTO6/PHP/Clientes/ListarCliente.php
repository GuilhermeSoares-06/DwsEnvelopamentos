<?php
// =============================================
// ListarCliente.php  (área ADM)
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

// ---- Ações POST ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'deletar') {
        $id = (int)($_POST['id'] ?? 0);
        header('Content-Type: application/json');
        try {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE cliid = :id");
            $ok   = $stmt->execute([':id' => $id]);
            echo json_encode($ok
                ? ['status'=>'sucesso','mensagem'=>'Cliente excluído com sucesso.']
                : ['status'=>'erro',   'mensagem'=>'Erro ao excluir cliente.']
            );
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'erro',
                'mensagem' => 'Não é possível excluir este cliente pois possui serviços/agendamentos vinculados.'
            ]);
        }
        exit;
    }

    if ($acao === 'editar') {
        $id       = (int)($_POST['id'] ?? 0);
        $nome     = trim($_POST['nome']     ?? '');
        $cpf      = preg_replace('/\D/','',$_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $senha    = trim($_POST['senha']    ?? '');

        header('Content-Type: application/json');
        try {
            if ($senha) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    "UPDATE clientes SET clinome=:n, clisenha=:s, clicpf=:c, clitel=:t, cliendereco=:e WHERE cliid=:id"
                );
                $stmt->execute([':n'=>$nome,':s'=>$senhaHash,':c'=>$cpf,':t'=>$telefone,':e'=>$endereco,':id'=>$id]);
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE clientes SET clinome=:n, clicpf=:c, clitel=:t, cliendereco=:e WHERE cliid=:id"
                );
                $stmt->execute([':n'=>$nome,':c'=>$cpf,':t'=>$telefone,':e'=>$endereco,':id'=>$id]);
            }
            echo json_encode(['status'=>'sucesso','mensagem'=>'Cliente atualizado com sucesso.']);
        } catch (PDOException $e) {
            echo json_encode(['status'=>'erro','mensagem'=>'Erro ao atualizar cliente.']);
        }
        exit;
    }
}

// ---- Busca ----
$busca = trim($_GET['busca'] ?? '');
if ($busca) {
    $stmt = $pdo->prepare(
        "SELECT cliid, clinome, clicpf, clitel, cliendereco
         FROM clientes WHERE clinome LIKE :b ORDER BY cliid DESC"
    );
    $stmt->execute([':b' => "%$busca%"]);
} else {
    $stmt = $pdo->query(
        "SELECT cliid, clinome, clicpf, clitel, cliendereco FROM clientes ORDER BY cliid DESC"
    );
}
$clientes = $stmt->fetchAll();
$total    = count($clientes);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar Clientes - DWS</title>
<style>
/* ===== RESET & BASE ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #0f0f0f;
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Arial, sans-serif;
    padding: 30px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

/* ===== CONTAINER PRINCIPAL ===== */
.container {
    max-width: 1400px;
    width: 100%;
    background: #1a1a1a;
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.8), 0 0 0 1px rgba(242, 53, 53, 0.15);
    transition: all 0.3s ease;
}

/* ===== HEADER ===== */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid rgba(242, 53, 53, 0.2);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-left .icon {
    font-size: 32px;
    background: #2a2a2a;
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    border: 1px solid #F23535;
}

.header-left h1 {
    color: #fff;
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.5px;
}

.header-left h1 span {
    color: #F23535;
}

.header-left .subtitle {
    color: #888;
    font-size: 13px;
    margin-top: 4px;
}

.badge-admin {
    background: #F23535;
    color: #fff;
    padding: 6px 18px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* ===== BARRA DE BUSCA ===== */
.search-section {
    background: #222;
    border-radius: 14px;
    padding: 20px 25px;
    margin-bottom: 25px;
    border: 1px solid #333;
}

.search-form {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.search-form .search-wrapper {
    flex: 1;
    min-width: 250px;
    position: relative;
}

.search-form .search-wrapper .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 18px;
}

.search-form .search-wrapper input {
    width: 100%;
    padding: 12px 16px 12px 46px;
    background: #2a2a2a;
    border: 2px solid #333;
    border-radius: 10px;
    color: #fff;
    font-size: 15px;
    transition: all 0.3s ease;
}

.search-form .search-wrapper input:focus {
    outline: none;
    border-color: #F23535;
    background: #1f1f1f;
}

.search-form .search-wrapper input::placeholder {
    color: #666;
}

.search-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #F23535;
    color: #fff;
}

.btn-primary:hover {
    background: #d42d2d;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(242, 53, 53, 0.3);
}

.btn-secondary {
    background: #333;
    color: #ccc;
}

.btn-secondary:hover {
    background: #444;
    transform: translateY(-2px);
}

.btn-success {
    background: #2e7d32;
    color: #fff;
}

.btn-success:hover {
    background: #1b5e20;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3);
}

.btn-danger {
    background: #c62828;
    color: #fff;
}

.btn-danger:hover {
    background: #b71c1c;
    transform: translateY(-2px);
}

.btn-outline {
    background: transparent;
    color: #aaa;
    border: 2px solid #444;
}

.btn-outline:hover {
    border-color: #F23535;
    color: #fff;
    background: rgba(242, 53, 53, 0.1);
}

/* ===== STATS BAR ===== */
.stats-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    background: #222;
    padding: 14px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    border: 1px solid #333;
}

.stats-bar .total {
    color: #aaa;
    font-size: 14px;
}

.stats-bar .total strong {
    color: #F23535;
    font-size: 18px;
}

.stats-bar .actions-quick {
    display: flex;
    gap: 10px;
}

/* ===== TABELA ===== */
.table-container {
    background: #1a1a1a;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #2a2a2a;
}

.table-scroll {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

table thead {
    background: #252525;
}

table thead th {
    padding: 16px 18px;
    text-align: left;
    color: #ccc;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #333;
}

table tbody tr {
    transition: background 0.2s ease;
    border-bottom: 1px solid #2a2a2a;
}

table tbody tr:last-child {
    border-bottom: none;
}

table tbody tr:hover {
    background: #252525;
}

table tbody td {
    padding: 16px 18px;
    color: #e0e0e0;
    font-size: 14px;
    vertical-align: middle;
}

table tbody td .cell-id {
    color: #888;
    font-weight: 600;
    font-size: 13px;
}

table tbody td .cell-name {
    font-weight: 500;
}

table tbody td .cell-cpf {
    font-family: 'Courier New', monospace;
    color: #aaa;
    font-size: 13px;
}

/* ===== AÇÕES ===== */
.actions-cell {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    justify-content: center;
}

.btn-action {
    padding: 7px 14px;
    border: none;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-action.services {
    background: #1565C0;
    color: #fff;
}

.btn-action.services:hover {
    background: #0d47a1;
    transform: scale(1.05);
}

.btn-action.edit {
    background: #2e7d32;
    color: #fff;
}

.btn-action.edit:hover {
    background: #1b5e20;
    transform: scale(1.05);
}

.btn-action.delete {
    background: #c62828;
    color: #fff;
}

.btn-action.delete:hover {
    background: #b71c1c;
    transform: scale(1.05);
}

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #666;
}

.empty-state .empty-icon {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    font-size: 16px;
}

/* ===== FOOTER ===== */
.footer-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid rgba(242, 53, 53, 0.15);
}

/* ===== MODAL ===== */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    justify-content: center;
    align-items: center;
    z-index: 1000;
    padding: 20px;
    backdrop-filter: blur(4px);
}

.modal.ativo {
    display: flex;
}

.modal-content {
    background: #1e1e1e;
    border-radius: 20px;
    padding: 40px;
    width: 500px;
    max-width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    border: 1px solid #F23535;
    box-shadow: 0 30px 80px rgba(0,0,0,0.9);
    animation: modalIn 0.3s ease;
}

@keyframes modalIn {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-content h2 {
    color: #fff;
    font-size: 24px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-content h2::after {
    content: '';
    flex: 1;
    height: 2px;
    background: linear-gradient(to right, #F23535, transparent);
}

.modal-content .form-group {
    margin-bottom: 16px;
}

.modal-content label {
    display: block;
    color: #aaa;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.modal-content input,
.modal-content select {
    width: 100%;
    padding: 12px 16px;
    background: #2a2a2a;
    border: 2px solid #333;
    border-radius: 10px;
    color: #fff;
    font-size: 14px;
    transition: all 0.3s ease;
}

.modal-content input:focus,
.modal-content select:focus {
    outline: none;
    border-color: #F23535;
    background: #1f1f1f;
}

.modal-content .helper-text {
    color: #666;
    font-size: 12px;
    margin-top: 6px;
}

.modal-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

.modal-actions .btn {
    flex: 1;
    justify-content: center;
    padding: 14px;
}

/* ===== TOAST ===== */
.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    padding: 16px 28px;
    border-radius: 12px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    display: none;
    z-index: 9999;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    animation: toastIn 0.4s ease;
    max-width: 400px;
}

.toast.success {
    background: #2e7d32;
    border-left: 4px solid #4caf50;
}

.toast.error {
    background: #c62828;
    border-left: 4px solid #ef5350;
}

@keyframes toastIn {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* ===== SCROLLBAR ===== */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #1a1a1a;
}

::-webkit-scrollbar-thumb {
    background: #333;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #444;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    body {
        padding: 15px;
    }
    
    .container {
        padding: 20px;
    }
    
    .header-left h1 {
        font-size: 20px;
    }
    
    .header-left .icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    
    .search-form .search-wrapper {
        min-width: 100%;
    }
    
    .search-actions {
        width: 100%;
    }
    
    .search-actions .btn {
        flex: 1;
        justify-content: center;
    }
    
    .stats-bar {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    
    .stats-bar .actions-quick {
        justify-content: center;
    }
    
    .modal-content {
        padding: 25px;
    }
    
    .modal-actions {
        flex-direction: column;
    }
    
    .footer-actions {
        flex-direction: column;
    }
    
    .footer-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .actions-cell {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-action {
        justify-content: center;
    }
}
</style>
</head>
<body>

<div class="container">
    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="icon">👥</div>
            <div>
                <h1>Gerenciar <span>Clientes</span></h1>
                <div class="subtitle">Visualize e gerencie todos os clientes cadastrados</div>
            </div>
        </div>
        <span class="badge-admin">Área Administrativa</span>
    </div>

    <!-- SEARCH -->
    <div class="search-section">
        <form class="search-form" method="GET">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" name="busca" 
                       placeholder="Pesquisar por nome..." 
                       value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="search-actions">
                <button class="btn btn-primary" type="submit">Buscar</button>
                <?php if ($busca): ?>
                    <a class="btn btn-secondary" href="ListarCliente.php">✖ Limpar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- STATS -->
    <div class="stats-bar">
        <div class="total">
            Total de clientes: <strong><?= $total ?></strong>
        </div>
        <div class="actions-quick">
            <a class="btn btn-success" href="../../telas/Cliente/CadastroCliente.html">Novo Cliente</a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-container">
        <div class="table-scroll">
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
                    <?php if ($clientes): ?>
                        <?php foreach ($clientes as $r): ?>
                            <tr>
                                <td><span class="cell-id">#<?= $r['cliid'] ?></span></td>
                                <td><span class="cell-name"><?= htmlspecialchars($r['clinome']) ?></span></td>
                                <td><span class="cell-cpf"><?= htmlspecialchars($r['clicpf']) ?></span></td>
                                <td><?= htmlspecialchars($r['clitel']) ?></td>
                                <td><?= htmlspecialchars($r['cliendereco']) ?></td>
                                <td>
                                    <div class="actions-cell">
                                        <button class="btn-action edit" onclick="abrirEditar(<?= htmlspecialchars(json_encode($r)) ?>)">
                                            Editar
                                        </button>
                                        <button class="btn-action delete" onclick="deletar(<?= $r['cliid'] ?>, '<?= htmlspecialchars($r['clinome']) ?>')">
                                            Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <p><?= $busca ? "Nenhum resultado encontrado para \"$busca\"" : 'Nenhum cliente cadastrado ainda.' ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer-actions">
        <a class="btn btn-outline" href="../../telas/ADM/principalFUN.html">Voltar ao Menu</a>
        <a class="btn btn-success" href="../../telas/Cliente/CadastroCliente.html"> Novo Cliente</a>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal" id="modalEditar">
    <div class="modal-content">
        <h2>✏️ Editar Cliente</h2>
        <input type="hidden" id="edit_id">
        
        <div class="form-group">
            <label for="edit_nome">Nome Completo</label>
            <input type="text" id="edit_nome" placeholder="Digite o nome completo" required>
        </div>
        
        <div class="form-group">
            <label for="edit_senha">Nova Senha</label>
            <input type="password" id="edit_senha" placeholder="Deixe em branco para manter a atual">
            <div class="helper-text">⚠️ Deixe em branco para não alterar a senha</div>
        </div>
        
        <div class="form-group">
            <label for="edit_cpf">CPF</label>
            <input type="text" id="edit_cpf" placeholder="000.000.000-00">
        </div>
        
        <div class="form-group">
            <label for="edit_telefone">Telefone</label>
            <input type="text" id="edit_telefone" placeholder="(00) 00000-0000">
        </div>
        
        <div class="form-group">
            <label for="edit_endereco">Endereço</label>
            <input type="text" id="edit_endereco" placeholder="Rua, número, bairro, cidade">
        </div>
        
        <div class="modal-actions">
            <button class="btn btn-success" onclick="salvarEdicao()">Salvar Alterações</button>
            <button class="btn btn-danger" onclick="fecharModal()">Cancelar</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
function showToast(msg, ok = true) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast ' + (ok ? 'success' : 'error');
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3500);
}

function abrirEditar(dados) {
    document.getElementById('edit_id').value       = dados.cliid;
    document.getElementById('edit_nome').value     = dados.clinome;
    document.getElementById('edit_senha').value    = '';
    document.getElementById('edit_cpf').value      = dados.clicpf   || '';
    document.getElementById('edit_telefone').value = dados.clitel    || '';
    document.getElementById('edit_endereco').value = dados.cliendereco || '';
    document.getElementById('modalEditar').classList.add('ativo');
}

function fecharModal() {
    document.getElementById('modalEditar').classList.remove('ativo');
}

async function salvarEdicao() {
    const fd = new FormData();
    fd.append('acao',     'editar');
    fd.append('id',       document.getElementById('edit_id').value);
    fd.append('nome',     document.getElementById('edit_nome').value);
    fd.append('senha',    document.getElementById('edit_senha').value);
    fd.append('cpf',      document.getElementById('edit_cpf').value);
    fd.append('telefone', document.getElementById('edit_telefone').value);
    fd.append('endereco', document.getElementById('edit_endereco').value);

    const res  = await fetch('ListarCliente.php', { 
        method:'POST', 
        body:fd 
    });
    const data = await res.json();
    showToast(data.mensagem, data.status === 'sucesso');
    if (data.status === 'sucesso') {
        setTimeout(() => location.reload(), 1200);
    }
}

async function deletar(id, nome) {
    if (!confirm(`⚠️ Tem certeza que deseja excluir o cliente "${nome}"?\nEsta ação não pode ser desfeita.`)) return;
    
    const fd = new FormData();
    fd.append('acao', 'deletar');
    fd.append('id', id);
    const res  = await fetch('ListarCliente.php', { 
        method:'POST', 
        body:fd 
    });
    const data = await res.json();
    showToast(data.mensagem, data.status === 'sucesso');
    if (data.status === 'sucesso') {
        setTimeout(() => location.reload(), 1200);
    }
}

// Fecha modal ao clicar fora
document.getElementById('modalEditar').addEventListener('click', function(e){
    if (e.target === this) fecharModal();
});
</script>

</body>
</html>