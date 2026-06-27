<?php
// =============================================
// GerenciarServico.php  (área ADM)
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

// ---- Ações POST (JSON) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'editar') {
        $id         = (int)($_POST['id']          ?? 0);
        $valor      = trim($_POST['valor']         ?? '');
        $valor_antigo = trim($_POST['valor_antigo'] ?? '');

        $stmt = $pdo->prepare("UPDATE servicos SET servalor = :v WHERE serid = :id");
        $ok   = $stmt->execute([':v' => $valor, ':id' => $id]);
        echo json_encode($ok
            ? ['status'=>'sucesso','mensagem'=>"Valor atualizado: R$ $valor",'valor_antigo'=>$valor_antigo,'valor_novo'=>$valor]
            : ['status'=>'erro',   'mensagem'=>'Erro ao atualizar valor.']
        );
        exit;
    }

    if ($acao === 'deletar') {
        $id   = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM servicos WHERE serid = :id");
        $ok   = $stmt->execute([':id' => $id]);
        echo json_encode($ok
            ? ['status'=>'sucesso','mensagem'=>'Serviço excluído.']
            : ['status'=>'erro',   'mensagem'=>'Erro ao excluir.']
        );
        exit;
    }

    echo json_encode(['status'=>'erro','mensagem'=>'Ação desconhecida.']);
    exit;
}

// ---- Busca ----
$busca = trim($_GET['busca'] ?? '');
if ($busca) {
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
$rowTotal = $pdo->query(
    "SELECT COUNT(*) as qtd,
            COALESCE(SUM(CAST(REPLACE(REPLACE(servalor,'.',''),',','.') AS DECIMAL(10,2))),0) AS total
     FROM servicos"
)->fetch();
$total_geral  = number_format((float)$rowTotal['total'], 2, ',', '.');
$qtd_total    = (int)$rowTotal['qtd'];
$ticket_medio = $qtd_total > 0
    ? number_format((float)$rowTotal['total'] / $qtd_total, 2, ',', '.')
    : '0,00';

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
<title>Lista de Pedidos - DWS</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:linear-gradient(135deg,#1e1e1e,#3b3b3b);font-family:'Segoe UI',Arial,sans-serif;padding:35px;min-height:100vh}
.dashboard{display:flex;gap:22px;max-width:1500px;margin:0 auto;align-items:flex-start}
.main-content{flex:1;background:#403E3F;padding:28px;border-radius:22px;border:1px solid #F23535;box-shadow:0 10px 30px rgba(0,0,0,.3)}
.sidebar-total{width:300px;background:#403E3F;border-radius:22px;border:1px solid #F23535;padding:22px;position:sticky;top:18px;box-shadow:0 10px 30px rgba(0,0,0,.3)}
h1{color:#fff;text-align:center;margin-bottom:25px;font-size:26px}
.admin-badge{text-align:center;margin-bottom:18px}
.admin-badge span{background:#F23535;color:#fff;padding:4px 14px;border-radius:18px;font-size:12px}
.busca-container{display:flex;gap:8px;justify-content:center;margin-bottom:22px;flex-wrap:wrap}
.busca-input{flex:1;max-width:360px;padding:11px 18px;border:2px solid transparent;border-radius:22px;background:#2b2b2b;color:#fff;font-size:14px;outline:none}
.busca-input:focus{border-color:#F23535}
.busca-btn{padding:11px 20px;background:#F23535;color:#fff;border:none;border-radius:22px;cursor:pointer;font-weight:bold;transition:.3s}
.busca-btn:hover{background:#c91f2c;transform:scale(1.02)}
.limpar-btn{padding:11px 20px;background:#4CAF50;color:#fff;text-decoration:none;border-radius:22px;font-weight:bold;transition:.3s;display:inline-flex;align-items:center}
.limpar-btn:hover{background:#45a049;transform:scale(1.02)}
.resultado-info{text-align:center;color:#aaa;margin-bottom:15px;font-size:13px}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;color:#fff;min-width:700px}
th{background:#F23535;padding:11px;text-align:left;font-size:13px}
td{padding:9px 11px;border-bottom:1px solid #555;font-size:13px}
tr:hover td{background:#4a4a4a}
.btn-editar{background:#4CAF50;color:#fff;padding:6px 12px;border:none;border-radius:7px;cursor:pointer;transition:.3s;font-size:12px}
.btn-editar:hover{transform:scale(1.05);background:#45a049}
.btn-deletar{background:#F23535;color:#fff;padding:6px 12px;border:none;border-radius:7px;cursor:pointer;transition:.3s;font-size:12px;margin-left:4px}
.btn-deletar:hover{transform:scale(1.05);background:#c91f2c}
.btn-voltar{display:inline-block;background:#F23535;color:#fff;padding:10px 20px;text-decoration:none;border-radius:10px;margin-top:18px;transition:.3s;font-weight:bold}
.btn-voltar:hover{transform:scale(1.05);background:#c91f2c}
.status{color:#ff9800;font-weight:bold}
/* SIDEBAR */
.total-header{text-align:center;margin-bottom:20px;padding-bottom:13px;border-bottom:2px solid #F23535}
.total-header h3{color:#F23535;font-size:18px}
.total-header .icone{font-size:36px;margin-bottom:7px}
.total-valor{text-align:center;margin-bottom:22px}
.total-valor .label{color:#A4A5A6;font-size:13px;margin-bottom:7px}
.total-valor .valor{font-size:40px;font-weight:bold;color:#4CAF50}
.total-valor .moeda{font-size:20px;color:#4CAF50}
.total-detalhes{background:#2b2b2b;border-radius:12px;padding:13px}
.total-detalhes p{color:#A4A5A6;margin:7px 0;font-size:13px}
.total-detalhes strong{color:#fff}
.servicos-list{margin-top:17px;max-height:260px;overflow-y:auto}
.servicos-list h4{color:#fff;margin-bottom:8px;font-size:14px}
.servico-item{background:#2b2b2b;padding:7px 11px;border-radius:7px;margin-bottom:6px;font-size:12px;overflow:hidden}
.servico-item .nome{color:#F23535}
.servico-item .valor-item{color:#4CAF50;float:right}
/* MODAL */
.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.9);justify-content:center;align-items:center;z-index:1000}
.modal.ativo{display:flex}
.modal-content{background:#403E3F;padding:36px;border-radius:22px;border:2px solid #F23535;width:480px;max-width:93%;box-shadow:0 10px 30px rgba(0,0,0,.5);animation:fadeIn .3s ease}
.modal-content h2{color:#fff;text-align:center;margin-bottom:17px}
.info-servico{background:#2b2b2b;padding:13px;border-radius:9px;margin-bottom:17px}
.info-servico p{color:#A4A5A6;margin:4px 0;font-size:13px}
.info-servico strong{color:#F23535}
.valor-atual{text-align:center;color:#4CAF50;font-size:18px;margin-bottom:13px}
.modal-content input{width:100%;padding:13px;margin-bottom:13px;border:none;border-radius:9px;background:#2b2b2b;color:#fff;font-size:16px;box-sizing:border-box;outline:none;transition:.3s}
.modal-content input:focus{border:2px solid #F23535}
.modal-btn{width:100%;padding:13px;border:none;color:#fff;border-radius:9px;font-size:16px;cursor:pointer;margin-top:6px;transition:.3s;font-weight:bold}
.modal-btn.salvar{background:#4CAF50}
.modal-btn.salvar:hover{background:#45a049}
.modal-btn.cancelar{background:#F23535}
.modal-btn.cancelar:hover{background:#c91f2c}
/* TOAST */
.toast{position:fixed;bottom:25px;right:25px;background:#4CAF50;color:#fff;padding:13px 20px;border-radius:11px;display:none;font-weight:bold;z-index:9999;box-shadow:0 5px 20px rgba(0,0,0,.4)}
@keyframes fadeIn{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:1000px){.dashboard{flex-direction:column}.sidebar-total{width:100%;position:relative}body{padding:18px}.main-content{padding:18px}}
@media(max-width:768px){th,td{font-size:11px;padding:7px}.btn-editar,.btn-deletar{padding:5px 9px;font-size:11px}}
</style>
</head>
<body>
<div class="dashboard">
    <div class="main-content">
        <h1>📋 Lista de Pedidos</h1>
        <div class="admin-badge"><span>👤 Área Administrativa</span></div>

        <form class="busca-container" method="GET">
            <input class="busca-input" type="text" name="busca"
                   placeholder="Pesquisar por nome do cliente..."
                   value="<?= htmlspecialchars($busca) ?>">
            <button class="busca-btn" type="submit">🔍 Buscar</button>
            <?php if ($busca): ?>
                <a class="limpar-btn" href="GerenciarServico.php">✖ Limpar</a>
            <?php endif; ?>
        </form>

        <?php if ($busca): ?>
        <div class="resultado-info">
            Resultado para: <strong>"<?= htmlspecialchars($busca) ?>"</strong>
            — <?= count($servicos) ?> pedido(s) encontrado(s)
        </div>
        <?php endif; ?>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th><th>Telefone</th><th>Serviço</th>
                        <th>Descrição</th><th>Valor</th><th>Data</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($servicos): foreach ($servicos as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['clinome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['clitel']  ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['tipo_servico']) ?></td>
                        <td><?= htmlspecialchars(mb_strimwidth($r['serdescricao'], 0, 60, '…')) ?></td>
                        <td class="status">R$ <?= $r['servalor'] ? htmlspecialchars($r['servalor']) : '0,00' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($r['serdata_servico'])) ?></td>
                        <td>
                            <button class="btn-editar" onclick="abrirEditar(<?= htmlspecialchars(json_encode($r)) ?>)">✏️ Editar</button>
                            <button class="btn-deletar" onclick="deletar(<?= $r['serid'] ?>)">🗑️</button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="7" style="text-align:center;padding:28px;color:#aaa">
                        <?= $busca ? "Nenhum pedido para \"$busca\"" : 'Nenhum pedido cadastrado.' ?>
                    </td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a class="btn-voltar" href="../../telas/ADM/principalFUN.html">🏠 Voltar ao Menu</a>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar-total">
        <div class="total-header">
            <div class="icone">💰</div>
            <h3>Faturamento Total</h3>
        </div>
        <div class="total-valor">
            <div class="label">VALOR ACUMULADO</div>
            <div class="valor"><span class="moeda">R$</span> <?= $total_geral ?></div>
        </div>
        <div class="total-detalhes">
            <p><strong>Resumo Financeiro</strong></p>
            <p>Total de pedidos: <strong><?= $qtd_total ?></strong></p>
            <p>Ticket médio: <strong style="color:#ff9800">R$ <?= $ticket_medio ?></strong></p>
        </div>
        <div class="servicos-list">
            <h4>Últimos serviços</h4>
            <?php foreach ($ultimos as $u): ?>
            <div class="servico-item">
                <span class="nome"><?= htmlspecialchars(mb_strimwidth($u['clinome'] ?? '—', 0, 20, '…')) ?></span>
                <span class="valor-item">R$ <?= $u['servalor'] ? htmlspecialchars($u['servalor']) : '0,00' ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- MODAL EDITAR VALOR -->
<div class="modal" id="modalEditar">
    <div class="modal-content">
        <h2>✏️ Editar Valor do Serviço</h2>
        <div class="info-servico" id="infoServico"></div>
        <div class="valor-atual" id="valorAtual"></div>
        <input type="hidden" id="edit_id">
        <input type="hidden" id="edit_valor_antigo">
        <input type="text" id="edit_valor" placeholder="Novo valor (ex: 350,00)" autofocus>
        <button class="modal-btn salvar"  onclick="salvarValor()">💾 Salvar Valor</button>
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

function abrirEditar(s) {
    document.getElementById('edit_id').value           = s.serid;
    document.getElementById('edit_valor_antigo').value = s.servalor || '0';
    document.getElementById('edit_valor').value        = '';
    document.getElementById('infoServico').innerHTML   =
        `<p><strong>Cliente:</strong> ${s.clinome || '—'}</p>
         <p><strong>Serviço:</strong> ${s.tipo_servico}</p>
         <p><strong>Descrição:</strong> ${s.serdescricao.substring(0,80)}</p>`;
    document.getElementById('valorAtual').textContent =
        'Valor atual: R$ ' + (s.servalor || '0,00');
    document.getElementById('modalEditar').classList.add('ativo');
    setTimeout(() => document.getElementById('edit_valor').focus(), 150);
}

function fecharModal() {
    document.getElementById('modalEditar').classList.remove('ativo');
}

async function salvarValor() {
    const fd = new FormData();
    fd.append('acao',        'editar');
    fd.append('id',          document.getElementById('edit_id').value);
    fd.append('valor',       document.getElementById('edit_valor').value);
    fd.append('valor_antigo',document.getElementById('edit_valor_antigo').value);

    const res  = await fetch('GerenciarServico.php', {
        method:'POST', 
        body:fd
    });
    const data = await res.json();
    showToast(data.mensagem, data.status === 'sucesso');
    if (data.status === 'sucesso') {
        fecharModal();
        setTimeout(() => location.reload(), 1400);
    }
}

async function deletar(id) {
    if (!confirm('Excluir este pedido?')) return;
    const fd = new FormData();
    fd.append('acao', 'deletar');
    fd.append('id',   id);
    const res  = await fetch('GerenciarServico.php', {
        method:'POST', 
        body:fd
    });
    const data = await res.json();
    showToast(data.mensagem, data.status === 'sucesso');
    if (data.status === 'sucesso') setTimeout(() => location.reload(), 1200);
}

document.getElementById('modalEditar').addEventListener('click', function(e){
    if (e.target === this) fecharModal();
});
</script>
</body>
</html>