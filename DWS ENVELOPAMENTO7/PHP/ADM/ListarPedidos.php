<?php
// =============================================
// ListarPedidos.php - Admin vê TODOS os pedidos (com paginação)
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../telas/ADM/login.html');
    exit;
}

$mensagem = '';
$tipoMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($acao === 'atualizar_status' && $id > 0) {
        $novoStatus = trim($_POST['status'] ?? 'pendente');
        try {
            $stmt = $pdo->prepare("UPDATE servicos SET serstatus_servico = :s WHERE serid = :id");
            $stmt->execute([':s' => $novoStatus, ':id' => $id]);
            $mensagem = 'Status atualizado com sucesso!';
            $tipoMsg = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao atualizar status.';
            $tipoMsg = 'erro';
        }
    }

    if ($acao === 'excluir' && $id > 0) {
        try {
            $pdo->prepare("DELETE FROM servicos WHERE serid = :id")->execute([':id' => $id]);
            $mensagem = 'Pedido excluído!';
            $tipoMsg = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao excluir pedido.';
            $tipoMsg = 'erro';
        }
    }
}

$filtroStatus = $_GET['status'] ?? 'todos';
$filtroTipo   = $_GET['tipo']   ?? 'todos';
$busca        = trim($_GET['busca'] ?? '');

$where = [];
$params = [];

if ($filtroStatus !== 'todos') {
    $where[] = "s.serstatus_servico = :status";
    $params[':status'] = $filtroStatus;
}
if ($filtroTipo !== 'todos') {
    $where[] = "s.tipo_servico = :tipo";
    $params[':tipo'] = $filtroTipo;
}
if ($busca !== '') {
    $where[] = "(c.clinome LIKE :busca OR s.serdescricao LIKE :busca)";
    $params[':busca'] = '%' . $busca . '%';
}

$sql = "
    SELECT
        s.serid,
        s.tipo_servico,
        s.serdescricao,
        s.servalor,
        s.serdata_servico,
        s.serstatus_servico,
        s.serstatus_pagamento,
        s.sermp_metodo_pagamento,
        c.clinome,
        c.clitel,
        c.cliemail
    FROM servicos s
    LEFT JOIN clientes c ON c.cliid = s.cliid
";
if (!empty($where)) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY s.serdata_servico DESC LIMIT 500";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ListarPedidos: " . $e->getMessage());
    $pedidos = [];
}

$totalPedidos = count($pedidos);
$porPagina = 10;

function extrairVeiculo($desc) {
    if (preg_match('/Ve[íi]culo:\s*([^|]+)/i', $desc, $m)) return trim($m[1]);
    if (preg_match('/Embarca[çc][ãa]o:\s*([^|]+)/i', $desc, $m)) return trim($m[1]);
    if (preg_match('/M[óo]vel:\s*([^|]+)/i', $desc, $m)) return trim($m[1]);
    return '—';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Todos os Pedidos - DWS Admin</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Space Grotesk', sans-serif;
    background: #0a0a0a;
    background-image:
        radial-gradient(ellipse at top left, rgba(242, 53, 53, 0.08) 0%, transparent 50%),
        radial-gradient(ellipse at bottom right, rgba(242, 53, 53, 0.05) 0%, transparent 50%);
    background-attachment: fixed;
    min-height: 100vh;
    padding: 30px 20px;
    color: #fff;
}

.container { max-width: 1400px; margin: 0 auto; }

.page-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 30px; flex-wrap: wrap; gap: 15px;
}
.page-header h1 {
    font-family: 'Orbitron', sans-serif; font-size: 1.8rem;
    background: linear-gradient(135deg, #fff, #f23535);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text; text-transform: uppercase;
}
.page-header h1 i { -webkit-text-fill-color: #f23535; margin-right: 12px; }

.btn-voltar {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 22px; background: rgba(255,255,255,0.05);
    color: #aaa; text-decoration: none; border-radius: 12px;
    font-size: 0.85rem; transition: 0.3s; font-weight: 500;
}
.btn-voltar:hover { background: rgba(242,53,53,0.1); color: #f23535; }

.mensagem {
    padding: 15px 25px; border-radius: 12px; margin-bottom: 25px;
    font-weight: 500; display: flex; align-items: center; gap: 12px;
}
.mensagem.sucesso { background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3); }
.mensagem.erro { background: rgba(242,53,53,0.15); color: #f23535; border: 1px solid rgba(242,53,53,0.3); }

.filtros-box {
    background: linear-gradient(145deg, rgba(30,30,30,0.8), rgba(20,20,20,0.9));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px; padding: 22px; margin-bottom: 25px;
}
.filtros-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px; align-items: end;
}
.filtro-group { display: flex; flex-direction: column; gap: 6px; }
.filtro-group label {
    color: #aaa; font-size: 0.75rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 1px;
}
.filtro-group select,
.filtro-group input {
    padding: 12px 16px; background: #1a1a1a;
    border: 2px solid #333; border-radius: 10px;
    color: #fff; font-size: 0.9rem; outline: none; font-family: inherit;
}
.filtro-group select:focus,
.filtro-group input:focus { border-color: #f23535; }

.btn-filtrar {
    padding: 12px 24px; background: linear-gradient(135deg, #f23535, #c91f2c);
    color: #fff; border: none; border-radius: 10px;
    font-weight: 700; cursor: pointer; transition: 0.3s;
    font-family: inherit; font-size: 0.9rem;
}
.btn-filtrar:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(242,53,53,0.4); }

.btn-limpar {
    padding: 12px 24px; background: rgba(255,255,255,0.05);
    color: #aaa; border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px; font-weight: 600; cursor: pointer;
    transition: 0.3s; text-decoration: none; display: inline-flex;
    align-items: center; gap: 8px; justify-content: center;
}
.btn-limpar:hover { background: rgba(242,53,53,0.1); color: #f23535; }

.resumo {
    display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;
}
.resumo-item {
    background: rgba(242,53,53,0.08); border: 1px solid rgba(242,53,53,0.2);
    padding: 10px 18px; border-radius: 12px; font-size: 0.85rem;
}
.resumo-item strong { color: #f23535; font-family: 'Orbitron', sans-serif; }

/* ============================================
   ABAS DE PAGINAÇÃO
   ============================================ */
.abas-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
    padding: 12px;
    background: linear-gradient(145deg, rgba(30,30,30,0.6), rgba(20,20,20,0.8));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    justify-content: center;
}

.aba-btn {
    padding: 10px 20px;
    background: rgba(255,255,255,0.03);
    border: 2px solid rgba(255,255,255,0.08);
    color: #aaa;
    border-radius: 10px;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    min-width: 44px;
    text-align: center;
}

.aba-btn:hover {
    border-color: #f23535;
    color: #f23535;
    background: rgba(242,53,53,0.08);
    transform: translateY(-2px);
}

.aba-btn.active {
    background: linear-gradient(135deg, #f23535, #c91f2c);
    border-color: #f23535;
    color: #fff;
    box-shadow: 0 8px 20px rgba(242,53,53,0.4);
    transform: translateY(-2px);
}

.aba-btn .aba-range {
    display: block;
    font-size: 0.6rem;
    color: #888;
    font-weight: 500;
    margin-top: 2px;
    letter-spacing: 0;
}

.aba-btn.active .aba-range {
    color: rgba(255,255,255,0.8);
}

/* ============================================
   TABELA
   ============================================ */
.tabela-box {
    background: linear-gradient(145deg, rgba(30,30,30,0.8), rgba(20,20,20,0.9));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px; padding: 25px; overflow: hidden;
}
.tabela-wrapper { overflow-x: auto; border-radius: 12px; }

table { width: 100%; border-collapse: collapse; min-width: 1000px; }
thead { background: rgba(242,53,53,0.08); }
th {
    text-align: left; padding: 14px 16px; color: #aaa;
    font-family: 'Orbitron', sans-serif; font-size: 0.7rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
}
td {
    padding: 14px 16px; color: #ccc; font-size: 0.85rem;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
tbody tr:hover td { background: rgba(242,53,53,0.04); }

.cliente-cell { font-weight: 600; color: #fff; }
.servico-cell { color: #f23535; font-weight: 600; }
.valor-cell { color: #4CAF50; font-weight: 700; font-family: 'Orbitron', sans-serif; }

.badge {
    display: inline-block; padding: 4px 12px; border-radius: 50px;
    font-size: 0.7rem; font-weight: 600; border: 1px solid;
}
.b-ok { color: #4CAF50; border-color: rgba(76,175,80,0.4); background: rgba(76,175,80,0.1); }
.b-wait { color: #FF9800; border-color: rgba(255,152,0,0.4); background: rgba(255,152,0,0.1); }
.b-off { color: #888; border-color: rgba(255,255,255,0.15); background: rgba(255,255,255,0.03); }
.b-pago { color: #25d366; border-color: rgba(37,211,102,0.4); background: rgba(37,211,102,0.1); }

.acoes { display: flex; gap: 6px; flex-wrap: wrap; }
.btn-acao {
    padding: 6px 10px; border-radius: 8px; border: none;
    cursor: pointer; font-size: 0.75rem; font-weight: 600;
    transition: 0.2s; font-family: inherit;
    display: inline-flex; align-items: center; gap: 5px;
}
.btn-excluir { background: rgba(242,53,53,0.15); color: #f23535; border: 1px solid rgba(242,53,53,0.3); }
.btn-excluir:hover { background: rgba(242,53,53,0.3); }

select.status-select {
    background: #1a1a1a; border: 1px solid #333; color: #fff;
    padding: 6px 10px; border-radius: 8px; font-size: 0.8rem;
    outline: none; cursor: pointer;
}

.vazio {
    text-align: center; padding: 60px 20px; color: #666;
}
.vazio i { font-size: 3rem; color: rgba(242,53,53,0.2); display: block; margin-bottom: 15px; }

@media (max-width: 600px) {
    .page-header h1 { font-size: 1.3rem; }
    .filtros-grid { grid-template-columns: 1fr; }
    .tabela-box { padding: 15px; }
    .aba-btn { padding: 8px 14px; font-size: 0.7rem; }
}
</style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-clipboard-list"></i> Todos os Pedidos</h1>
        <a href="../../telas/ADM/principalFUN.php" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel
        </a>
    </div>

    <?php if ($mensagem): ?>
    <div class="mensagem <?= $tipoMsg ?>">
        <i class="fas fa-<?= $tipoMsg === 'sucesso' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($mensagem) ?>
    </div>
    <?php endif; ?>

    <form method="GET" class="filtros-box">
        <div class="filtros-grid">
            <div class="filtro-group">
                <label>Status</label>
                <select name="status">
                    <option value="todos" <?= $filtroStatus === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="pendente" <?= $filtroStatus === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="em_andamento" <?= $filtroStatus === 'em_andamento' ? 'selected' : '' ?>>Em andamento</option>
                    <option value="finalizado" <?= $filtroStatus === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                    <option value="cancelado" <?= $filtroStatus === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <div class="filtro-group">
                <label>Tipo de Serviço</label>
                <select name="tipo">
                    <option value="todos" <?= $filtroTipo === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="carro" <?= $filtroTipo === 'carro' ? 'selected' : '' ?>>Carro</option>
                    <option value="moto" <?= $filtroTipo === 'moto' ? 'selected' : '' ?>>Moto</option>
                    <option value="caminhao" <?= $filtroTipo === 'caminhao' ? 'selected' : '' ?>>Caminhão</option>
                    <option value="aquatico" <?= $filtroTipo === 'aquatico' ? 'selected' : '' ?>>Aquático</option>
                    <option value="mobilia" <?= $filtroTipo === 'mobilia' ? 'selected' : '' ?>>Mobília</option>
                </select>
            </div>
            <div class="filtro-group">
                <label>Buscar</label>
                <input type="text" name="busca" placeholder="Cliente ou descrição..." value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="filtro-group" style="flex-direction:row; gap:8px;">
                <button type="submit" class="btn-filtrar"><i class="fas fa-search"></i> Filtrar</button>
                <a href="ListarPedidos.php" class="btn-limpar"><i class="fas fa-times"></i> Limpar</a>
            </div>
        </div>
    </form>

    <div class="resumo">
        <div class="resumo-item">Total: <strong><?= $totalPedidos ?></strong> pedidos</div>
        <div class="resumo-item">Mostrando <strong><?= $porPagina ?></strong> por página</div>
    </div>

    <!-- ABAS DE PAGINAÇÃO (geradas automaticamente pelo JS) -->
    <div class="abas-container" id="abasPedidos"></div>

    <!-- TABELA -->
    <div class="tabela-box">
        <div class="tabela-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Cliente</th>
                        <th>Telefone</th>
                        <th>Serviço</th>
                        <th>Veículo/Móvel</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Pagamento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tbodyPedidos">
                    <?php if (empty($pedidos)): ?>
                        <tr><td colspan="11" class="vazio"><i class="fas fa-inbox"></i> Nenhum pedido encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pedidos as $i => $p): ?>
                        <tr class="linha-pedido" data-index="<?= $i ?>">
                            <td>#<?= (int)$p['serid'] ?></td>
                            <td class="cliente-cell"><?= htmlspecialchars($p['clinome'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($p['clitel'] ?? '—') ?></td>
                            <td class="servico-cell"><?= htmlspecialchars(ucfirst($p['tipo_servico'] ?? '—')) ?></td>
                            <td><?= htmlspecialchars(extrairVeiculo($p['serdescricao'] ?? '')) ?></td>
                            <td><?= !empty($p['serdata_servico']) ? date('d/m/Y', strtotime($p['serdata_servico'])) : '—' ?></td>
                            <td><?= !empty($p['serdata_servico']) ? date('H:i', strtotime($p['serdata_servico'])) : '—' ?></td>
                            <td class="valor-cell">R$ <?= number_format((float)$p['servalor'], 2, ',', '.') ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="atualizar_status">
                                    <input type="hidden" name="id" value="<?= (int)$p['serid'] ?>">
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="pendente" <?= $p['serstatus_servico'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="em_andamento" <?= $p['serstatus_servico'] === 'em_andamento' ? 'selected' : '' ?>>Em andamento</option>
                                        <option value="finalizado" <?= $p['serstatus_servico'] === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                        <option value="cancelado" <?= $p['serstatus_servico'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <?php
                                    $pag = strtolower($p['serstatus_pagamento'] ?? '');
                                    if (in_array($pag, ['aprovado', 'pagar_no_local'])) {
                                        echo '<span class="badge b-pago">Pago</span>';
                                    } else {
                                        echo '<span class="badge b-wait">Pendente</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir este pedido?');">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= (int)$p['serid'] ?>">
                                    <button type="submit" class="btn-acao btn-excluir">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// =============================================
// PAGINAÇÃO COM ABAS
// =============================================
(function() {
    const POR_PAGINA = 10;
    const linhas = document.querySelectorAll('.linha-pedido');
    const containerAbas = document.getElementById('abasPedidos');

    if (linhas.length === 0) {
        containerAbas.style.display = 'none';
        return;
    }

    const totalPaginas = Math.ceil(linhas.length / POR_PAGINA);

    if (totalPaginas <= 1) {
        containerAbas.style.display = 'none';
        return;
    }

    for (let i = 0; i < totalPaginas; i++) {
        const inicio = i * POR_PAGINA + 1;
        const fim = Math.min((i + 1) * POR_PAGINA, linhas.length);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'aba-btn' + (i === 0 ? ' active' : '');
        btn.dataset.pagina = i;
        btn.innerHTML = `Página ${i + 1}<span class="aba-range">${inicio}–${fim}</span>`;
        btn.addEventListener('click', () => mostrarPagina(i));

        containerAbas.appendChild(btn);
    }

    function mostrarPagina(pagina) {
        document.querySelectorAll('.aba-btn').forEach(b => {
            b.classList.toggle('active', parseInt(b.dataset.pagina) === pagina);
        });

        const inicio = pagina * POR_PAGINA;
        const fim = inicio + POR_PAGINA;

        linhas.forEach((linha, i) => {
            linha.style.display = (i >= inicio && i < fim) ? '' : 'none';
        });

        // Rola para o topo da tabela
        document.querySelector('.tabela-box')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    mostrarPagina(0);
})();
</script>

</body>
</html>