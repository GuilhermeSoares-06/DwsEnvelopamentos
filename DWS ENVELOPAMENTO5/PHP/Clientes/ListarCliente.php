<?php
// =============================================
// ListarCliente.php  (área ADM)
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

// ---- Ações POST ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'deletar') {
        $id   = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE cliid = :id");
        $ok   = $stmt->execute([':id' => $id]);
        header('Content-Type: application/json');
        echo json_encode($ok
            ? ['status'=>'sucesso','mensagem'=>'Cliente excluído.']
            : ['status'=>'erro',   'mensagem'=>'Erro ao excluir.']
        );
        exit;
    }

    if ($acao === 'editar') {
        $id       = (int)($_POST['id'] ?? 0);
        $nome     = trim($_POST['nome']     ?? '');
        $cpf      = preg_replace('/\D/','',$_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $senha    = trim($_POST['senha']    ?? '');

        if ($senha) {
            $stmt = $pdo->prepare(
                "UPDATE clientes SET clinome=:n, clisenha=:s, clicpf=:c, clitel=:t, cliendereco=:e WHERE cliid=:id"
            );
            $stmt->execute([':n'=>$nome,':s'=>$senha,':c'=>$cpf,':t'=>$telefone,':e'=>$endereco,':id'=>$id]);
        } else {
            $stmt = $pdo->prepare(
                "UPDATE clientes SET clinome=:n, clicpf=:c, clitel=:t, cliendereco=:e WHERE cliid=:id"
            );
            $stmt->execute([':n'=>$nome,':c'=>$cpf,':t'=>$telefone,':e'=>$endereco,':id'=>$id]);
        }
        header('Content-Type: application/json');
        echo json_encode(['status'=>'sucesso','mensagem'=>'Cliente atualizado.']);
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
<title>Lista de Clientes - DWS</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:linear-gradient(135deg,#1e1e1e,#3b3b3b);font-family:'Segoe UI',Arial,sans-serif;padding:40px;min-height:100vh}
.container{max-width:1300px;margin:0 auto;background:#403E3F;padding:30px;border-radius:25px;border:1px solid #F23535;box-shadow:0 10px 30px rgba(0,0,0,.3)}
h1{color:#fff;text-align:center;margin-bottom:10px;font-size:30px}
.subtitulo{text-align:center;color:#aaa;margin-bottom:25px;font-size:14px}
.admin-badge{text-align:center;margin-bottom:20px}
.admin-badge span{background:#F23535;color:#fff;padding:5px 16px;border-radius:20px;font-size:13px}
.busca-container{display:flex;gap:10px;justify-content:center;margin-bottom:25px;flex-wrap:wrap}
.busca-input{flex:1;max-width:380px;padding:11px 18px;border:none;border-radius:25px;background:#2b2b2b;color:#fff;font-size:15px;outline:none;border:2px solid transparent}
.busca-input:focus{border-color:#F23535}
.busca-btn{padding:11px 22px;background:#F23535;color:#fff;border:none;border-radius:25px;cursor:pointer;transition:.3s;font-weight:bold}
.busca-btn:hover{background:#c91f2c;transform:scale(1.02)}
.limpar-btn{padding:11px 22px;background:#4CAF50;color:#fff;text-decoration:none;border-radius:25px;transition:.3s;font-weight:bold;display:inline-flex;align-items:center}
.limpar-btn:hover{background:#45a049;transform:scale(1.02)}
.total-bar{text-align:right;color:#aaa;margin-bottom:15px;padding:9px 15px;background:#2b2b2b;border-radius:10px;font-size:14px}
.total-bar span{color:#F23535;font-weight:bold;font-size:17px}
.table-wrap{overflow-x:auto;border-radius:12px}
table{width:100%;border-collapse:collapse;color:#fff;min-width:650px}
th{background:#F23535;padding:13px;text-align:left;font-size:14px}
td{padding:11px 13px;border-bottom:1px solid #555;font-size:14px}
tr:hover td{background:#4a4a4a}
.btn-editar{background:#4CAF50;color:#fff;padding:6px 13px;text-decoration:none;border-radius:7px;margin-right:4px;transition:.2s;display:inline-block;font-size:13px;cursor:pointer;border:none}
.btn-editar:hover{transform:scale(1.05);background:#45a049}
.btn-deletar{background:#F23535;color:#fff;padding:6px 13px;border:none;border-radius:7px;transition:.2s;cursor:pointer;font-size:13px}
.btn-deletar:hover{transform:scale(1.05);background:#c91f2c}
.footer-botoes{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-top:25px}
.btn-voltar{display:inline-flex;align-items:center;gap:7px;background:#F23535;color:#fff;padding:11px 22px;text-decoration:none;border-radius:25px;transition:.3s;font-weight:bold}
.btn-voltar:hover{transform:scale(1.05);background:#c91f2c}
.btn-novo{display:inline-flex;align-items:center;gap:7px;background:#4CAF50;color:#fff;padding:11px 22px;text-decoration:none;border-radius:25px;transition:.3s;font-weight:bold}
.btn-novo:hover{background:#45a049;transform:scale(1.05)}
/* MODAL */
.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);justify-content:center;align-items:center;z-index:1000}
.modal.ativo{display:flex}
.modal-content{background:#403E3F;padding:38px;border-radius:22px;border:2px solid #F23535;width:480px;max-width:94%;box-shadow:0 10px 30px rgba(0,0,0,.5);animation:fadeIn .3s ease}
.modal-content h2{color:#fff;text-align:center;margin-bottom:22px;font-size:22px}
.modal-content input{width:100%;padding:13px;margin-bottom:13px;border:none;border-radius:9px;background:#2b2b2b;color:#fff;font-size:15px;box-sizing:border-box;outline:none;transition:.3s}
.modal-content input:focus{border:2px solid #F23535}
.aviso-senha{color:#888;font-size:12px;margin-top:-10px;margin-bottom:12px}
.modal-btn{width:100%;padding:13px;border:none;color:#fff;border-radius:9px;font-size:16px;cursor:pointer;margin-top:6px;transition:.3s;font-weight:bold}
.modal-btn.salvar{background:#4CAF50}
.modal-btn.salvar:hover{background:#45a049;transform:scale(1.01)}
.modal-btn.cancelar{background:#F23535}
.modal-btn.cancelar:hover{background:#c91f2c;transform:scale(1.01)}
/* TOAST */
.toast{position:fixed;bottom:25px;right:25px;background:#4CAF50;color:#fff;padding:14px 22px;border-radius:12px;display:none;font-weight:bold;z-index:9999;box-shadow:0 5px 20px rgba(0,0,0,.4)}
@keyframes fadeIn{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:768px){body{padding:15px}.container{padding:18px}th,td{padding:9px;font-size:12px}}
</style>
</head>
<body>
<div class="container">
    <h1>👥 Lista de Clientes</h1>
    <div class="subtitulo">Gerencie todos os clientes cadastrados no sistema</div>
    <div class="admin-badge"><span>👤 Área Administrativa</span></div>

    <form class="busca-container" method="GET">
        <input class="busca-input" type="text" name="busca"
               placeholder="Pesquisar por nome..." value="<?= htmlspecialchars($busca) ?>">
        <button class="busca-btn" type="submit">🔍 Buscar</button>
        <?php if ($busca): ?>
            <a class="limpar-btn" href="ListarCliente.php">✖ Limpar</a>
        <?php endif; ?>
    </form>

    <div class="total-bar">Total de clientes: <span><?= $total ?></span></div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>ID</th><th>Nome</th><th>CPF</th><th>Telefone</th><th>Endereço</th><th style="text-align:center">Ações</th></tr>
            </thead>
            <tbody>
            <?php if ($clientes): foreach ($clientes as $r): ?>
                <tr>
                    <td><?= $r['cliid'] ?></td>
                    <td><?= htmlspecialchars($r['clinome']) ?></td>
                    <td><?= htmlspecialchars($r['clicpf']) ?></td>
                    <td><?= htmlspecialchars($r['clitel']) ?></td>
                    <td><?= htmlspecialchars($r['cliendereco']) ?></td>
                    <td style="text-align:center">
                        <a class="btn-editar" style="background:#F23535;text-decoration:none" href="GerenciarServico.php?cliid=<?= (int)$r['cliid'] ?>">🧾 Serviços</a>
                        <button class="btn-editar" onclick="abrirEditar(<?= htmlspecialchars(json_encode($r)) ?>)">✏️ Editar</button>
                        <button class="btn-deletar" onclick="deletar(<?= $r['cliid'] ?>, '<?= htmlspecialchars($r['clinome']) ?>')">🗑️ Excluir</button>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="6" style="text-align:center;padding:30px;color:#aaa">
                    <?= $busca ? "Nenhum resultado para \"$busca\"" : 'Nenhum cliente cadastrado.' ?>
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer-botoes">
        <a class="btn-voltar" href="../../telas/ADM/principalFUN.html">🏠 Voltar ao Menu</a>
        <a class="btn-novo"   href="../../telas/Cliente/CadastroCliente.html">➕ Novo Cliente</a>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal" id="modalEditar">
    <div class="modal-content">
        <h2>✏️ Editar Cliente</h2>
        <input type="hidden" id="edit_id">
        <input type="text"     id="edit_nome"     placeholder="NOME COMPLETO" required>
        <input type="password" id="edit_senha"    placeholder="Nova senha (deixe em branco para manter)">
        <p class="aviso-senha">⚠️ Deixe em branco para não alterar a senha.</p>
        <input type="text"     id="edit_cpf"      placeholder="CPF">
        <input type="text"     id="edit_telefone" placeholder="TELEFONE">
        <input type="text"     id="edit_endereco" placeholder="ENDEREÇO">
        <button class="modal-btn salvar"  onclick="salvarEdicao()">💾 Salvar</button>
        <button class="modal-btn cancelar" onclick="fecharModal()">❌ Cancelar</button>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
function showToast(msg, ok = true) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = ok ? '#4CAF50' : '#F23535';
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
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
    if (data.status === 'sucesso') setTimeout(() => location.reload(), 1200);
}

async function deletar(id, nome) {
    if (!confirm(`Excluir o cliente "${nome}"?`)) return;
    const fd = new FormData();
    fd.append('acao', 'deletar');
    fd.append('id', id);
    const res  = await fetch('ListarCliente.php', { 
        method:'POST', 
        body:fd 
    });
    const data = await res.json();
    showToast(data.mensagem, data.status === 'sucesso');
    if (data.status === 'sucesso') setTimeout(() => location.reload(), 1200);
}

// Fecha modal ao clicar fora
document.getElementById('modalEditar').addEventListener('click', function(e){
    if (e.target === this) fecharModal();
});
</script>
</body>
</html>