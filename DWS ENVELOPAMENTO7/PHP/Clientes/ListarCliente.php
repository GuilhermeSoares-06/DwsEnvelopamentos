<?php
// =============================================
// ListarCliente.php - Admin vê TODOS os clientes (com paginação)
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

    if ($acao === 'excluir' && $id > 0) {
        try {
            $pdo->prepare("DELETE FROM clientes WHERE cliid = :id AND tipocliente <> 'funcionario'")
                ->execute([':id' => $id]);
            $mensagem = 'Cliente removido!';
            $tipoMsg = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao remover cliente.';
            $tipoMsg = 'erro';
        }
    }
}

$busca = trim($_GET['busca'] ?? '');

$sql = "
    SELECT cliid, clinome, clicpf, clitel, cliemail, cliendereco, tipocliente
    FROM clientes
    WHERE (tipocliente = 'cliente' OR tipocliente IS NULL)
";
$params = [];
if ($busca !== '') {
    $sql .= " AND (clinome LIKE :busca OR clicpf LIKE :busca OR clitel LIKE :busca OR cliemail LIKE :busca)";
    $params[':busca'] = '%' . $busca . '%';
}
$sql .= " ORDER BY cliid DESC LIMIT 500";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ListarCliente: " . $e->getMessage());
    $clientes = [];
}

$totalClientes = count($clientes);
$porPagina = 10;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Todos os Clientes - DWS Admin</title>
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

.btn-novo {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 22px; background: linear-gradient(135deg, #f23535, #c91f2c);
    color: #fff; text-decoration: none; border-radius: 12px;
    font-size: 0.85rem; transition: 0.3s; font-weight: 700;
}
.btn-novo:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(242,53,53,0.4); color: #fff; }

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
    display: grid; grid-template-columns: 1fr auto auto;
    gap: 15px; align-items: end;
}
.filtro-group { display: flex; flex-direction: column; gap: 6px; }
.filtro-group label {
    color: #aaa; font-size: 0.75rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 1px;
}
.filtro-group input {
    padding: 12px 16px; background: #1a1a1a;
    border: 2px solid #333; border-radius: 10px;
    color: #fff; font-size: 0.9rem; outline: none; font-family: inherit;
}
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

table { width: 100%; border-collapse: collapse; min-width: 800px; }
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

.avatar-cliente {
    width: 42px; height: 42px; border-radius: 50%;
    background: linear-gradient(135deg, #f23535, #c91f2c);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 1rem;
    text-transform: uppercase;
}
.nome-cell { font-weight: 600; color: #fff; }
.tel-cell { color: #f23535; font-weight: 600; }

.acoes { display: flex; gap: 6px; flex-wrap: wrap; }
.btn-acao {
    padding: 6px 12px; border-radius: 8px; border: none;
    cursor: pointer; font-size: 0.75rem; font-weight: 600;
    transition: 0.2s; font-family: inherit; text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
}
.btn-excluir { background: rgba(242,53,53,0.15); color: #f23535; border: 1px solid rgba(242,53,53,0.3); }
.btn-excluir:hover { background: rgba(242,53,53,0.3); color: #f23535; }

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
        <h1><i class="fas fa-users"></i> Todos os Clientes</h1>
        <div style="display:flex; gap:10px;">
            <a href="../../telas/ADM/principalFUN.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
            <a href="../../telas/Cliente/CadastroCliente.html?origem=adm" class="btn-novo">
                <i class="fas fa-user-plus"></i> Novo Cliente
            </a>
        </div>
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
                <label>Buscar Cliente</label>
                <input type="text" name="busca" placeholder="Nome, CPF, telefone ou email..." value="<?= htmlspecialchars($busca) ?>">
            </div>
            <button type="submit" class="btn-filtrar"><i class="fas fa-search"></i> Filtrar</button>
            <a href="ListarCliente.php" class="btn-limpar"><i class="fas fa-times"></i> Limpar</a>
        </div>
    </form>

    <div class="resumo">
        <div class="resumo-item">Total: <strong><?= $totalClientes ?></strong> clientes</div>
        <div class="resumo-item">Mostrando <strong><?= $porPagina ?></strong> por página</div>
    </div>

    <!-- ABAS DE PAGINAÇÃO (geradas automaticamente pelo JS) -->
    <div class="abas-container" id="abasClientes"></div>

    <!-- TABELA -->
    <div class="tabela-box">
        <div class="tabela-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>#ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Endereço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tbodyClientes">
                    <?php if (empty($clientes)): ?>
                        <tr><td colspan="8" class="vazio"><i class="fas fa-user-slash"></i> Nenhum cliente encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $i => $c): ?>
                        <tr class="linha-cliente" data-index="<?= $i ?>">
                            <td>
                                <div class="avatar-cliente">
                                    <?= htmlspecialchars(mb_substr($c['clinome'] ?? '?', 0, 1)) ?>
                                </div>
                            </td>
                            <td>#<?= (int)$c['cliid'] ?></td>
                            <td class="nome-cell"><?= htmlspecialchars($c['clinome'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($c['clicpf'] ?? '—') ?></td>
                            <td class="tel-cell"><?= htmlspecialchars($c['clitel'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($c['cliemail'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($c['cliendereco'] ?? '—') ?></td>
                            <td>
                                <div class="acoes">
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir este cliente?');">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?= (int)$c['cliid'] ?>">
                                        <button type="submit" class="btn-acao btn-excluir">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
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
    const linhas = document.querySelectorAll('.linha-cliente');
    const containerAbas = document.getElementById('abasClientes');

    if (linhas.length === 0) {
        containerAbas.style.display = 'none';
        return;
    }

    const totalPaginas = Math.ceil(linhas.length / POR_PAGINA);

    // Se só tem 1 página, esconde as abas
    if (totalPaginas <= 1) {
        containerAbas.style.display = 'none';
        return;
    }

    // Cria os botões das abas
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

    // Função que mostra apenas as linhas da página escolhida
    function mostrarPagina(pagina) {
        // Atualiza o botão ativo
        document.querySelectorAll('.aba-btn').forEach(b => {
            b.classList.toggle('active', parseInt(b.dataset.pagina) === pagina);
        });

        // Mostra apenas as linhas da página
        const inicio = pagina * POR_PAGINA;
        const fim = inicio + POR_PAGINA;

        linhas.forEach((linha, i) => {
            linha.style.display = (i >= inicio && i < fim) ? '' : 'none';
        });
    }

    // Mostra a primeira página ao carregar
    mostrarPagina(0);
})();
</script>

</body>
</html>