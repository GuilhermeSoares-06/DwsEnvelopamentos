<?php
// =============================================
// GerenciarVeiculos.php — Admin cadastra os modelos (carros, motos,
// caminhões, aquáticos e mobília) que o cliente escolhe no agendamento
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca();
exigirLoginAdmin();

$TIPOS = [
    'carro'    => ['nome' => 'Carro',    'plural' => 'Carros',    'icone' => 'fa-car'],
    'moto'     => ['nome' => 'Moto',     'plural' => 'Motos',     'icone' => 'fa-motorcycle'],
    'caminhao' => ['nome' => 'Caminhão', 'plural' => 'Caminhões', 'icone' => 'fa-truck'],
    'aquatico' => ['nome' => 'Aquático', 'plural' => 'Aquáticos', 'icone' => 'fa-ship'],
    'mobilia'  => ['nome' => 'Mobília',  'plural' => 'Mobília',   'icone' => 'fa-couch'],
];
const POR_PAGINA = 30;

// Filtros da listagem (mantidos depois de salvar/remover)
$filtroTipo = isset($TIPOS[$_GET['tipo'] ?? '']) ? $_GET['tipo'] : '';
$busca      = trim(mb_substr($_GET['q'] ?? '', 0, 60));
$pagina     = max(1, (int) ($_GET['p'] ?? 1));

function urlLista(array $mudar = []): string {
    global $filtroTipo, $busca, $pagina;
    $q = array_merge(['tipo' => $filtroTipo, 'q' => $busca, 'p' => $pagina], $mudar);
    if ((int) $q['p'] <= 1) unset($q['p']);
    $q = array_filter($q, fn($v) => $v !== '' && $v !== null);
    return 'GerenciarVeiculos.php' . ($q ? '?' . http_build_query($q) : '');
}

function voltarComMensagem(string $msg, string $tipo, array $mudar = []): void {
    $_SESSION['flash_veiculos'] = ['msg' => $msg, 'tipo' => $tipo];
    header('Location: ' . urlLista($mudar));
    exit;
}

// Já existe outro com o mesmo tipo + marca + modelo?
function veiculoDuplicado(PDO $pdo, string $tipo, ?string $marca, string $modelo, int $ignorarId = 0): bool {
    $stmt = $pdo->prepare("
        SELECT 1 FROM veiculos
        WHERE vectipo = :t AND vecmarca <=> :m AND vecmodelo = :mo AND vecid <> :id
        LIMIT 1
    ");
    $stmt->execute([':t' => $tipo, ':m' => $marca, ':mo' => $modelo, ':id' => $ignorarId]);
    return (bool) $stmt->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCSRF();
    $acao = $_POST['acao'] ?? '';
    $id   = (int) ($_POST['id'] ?? 0);

    try {
        if ($acao === 'salvar') {
            $tipo   = $_POST['vectipo'] ?? '';
            $marca  = trim($_POST['vecmarca'] ?? '');
            $modelo = trim($_POST['vecmodelo'] ?? '');
            $marca  = ($marca === '' || $tipo === 'mobilia') ? null : $marca;

            if (!isset($TIPOS[$tipo]) || $modelo === '') {
                voltarComMensagem('Escolha o tipo e informe o modelo.', 'erro');
            }
            if (mb_strlen($modelo) > 120 || mb_strlen($marca ?? '') > 60) {
                voltarComMensagem('Marca (máx. 60) ou modelo (máx. 120) muito longos.', 'erro');
            }
            if (veiculoDuplicado($pdo, $tipo, $marca, $modelo, $id)) {
                voltarComMensagem('Esse modelo já está cadastrado.', 'erro');
            }

            if ($id > 0) {
                $pdo->prepare("UPDATE veiculos SET vectipo = :t, vecmarca = :m, vecmodelo = :mo WHERE vecid = :id")
                    ->execute([':t' => $tipo, ':m' => $marca, ':mo' => $modelo, ':id' => $id]);
                logAuditoria('veiculo_editado', ['vecid' => $id]);
                voltarComMensagem('Modelo atualizado!', 'sucesso');
            }

            $pdo->prepare("INSERT INTO veiculos (vectipo, vecmarca, vecmodelo) VALUES (:t, :m, :mo)")
                ->execute([':t' => $tipo, ':m' => $marca, ':mo' => $modelo]);
            logAuditoria('veiculo_criado', ['vecid' => (int) $pdo->lastInsertId()]);
            // Mostra a lista do tipo recém-cadastrado, já filtrada pelo modelo
            voltarComMensagem('Modelo cadastrado!', 'sucesso', ['tipo' => $tipo, 'q' => $modelo, 'p' => 1]);
        }

        if ($acao === 'alternar') {
            $pdo->prepare("UPDATE veiculos SET vecativo = 1 - vecativo WHERE vecid = :id")->execute([':id' => $id]);
            voltarComMensagem('Visibilidade atualizada.', 'sucesso');
        }

        if ($acao === 'deletar') {
            // Agendamentos/vendas que usavam o modelo ficam com vecid NULL (a descrição continua)
            $pdo->prepare("DELETE FROM veiculos WHERE vecid = :id")->execute([':id' => $id]);
            logAuditoria('veiculo_removido', ['vecid' => $id]);
            voltarComMensagem('Modelo removido.', 'sucesso');
        }
    } catch (PDOException $e) {
        error_log('[GerenciarVeiculos] ' . $e->getMessage());
        voltarComMensagem('Erro ao salvar no banco.', 'erro');
    }
}

$flash = $_SESSION['flash_veiculos'] ?? null;
unset($_SESSION['flash_veiculos']);

// Modelo em edição (?editar=ID)
$editando = null;
if (!empty($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM veiculos WHERE vecid = :id");
    $stmt->execute([':id' => (int) $_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Contagem por tipo (chips de filtro)
$contagem = $pdo->query("SELECT vectipo, COUNT(*) FROM veiculos GROUP BY vectipo")->fetchAll(PDO::FETCH_KEY_PAIR);
$totalGeral = array_sum($contagem);

// Listagem filtrada e paginada
$where  = [];
$params = [];
if ($filtroTipo !== '') {
    $where[] = 'vectipo = :tipo';
    $params[':tipo'] = $filtroTipo;
}
if ($busca !== '') {
    $where[] = "CONCAT_WS(' ', vecmarca, vecmodelo) LIKE :q";
    $params[':q'] = '%' . $busca . '%';
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM veiculos $sqlWhere");
$stmt->execute($params);
$totalFiltrado = (int) $stmt->fetchColumn();
$totalPaginas  = max(1, (int) ceil($totalFiltrado / POR_PAGINA));
$pagina        = min($pagina, $totalPaginas);
$offset        = ($pagina - 1) * POR_PAGINA;

$stmt = $pdo->prepare("
    SELECT * FROM veiculos $sqlWhere
    ORDER BY FIELD(vectipo, 'carro', 'moto', 'caminhao', 'aquatico', 'mobilia'), vecmarca, vecmodelo
    LIMIT " . POR_PAGINA . " OFFSET $offset
");
$stmt->execute($params);
$veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$csrf = htmlspecialchars(gerarTokenCSRF());
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar Veículos - DWS Admin</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Space Grotesk', sans-serif; background: #0a0a0a; background-image: radial-gradient(ellipse at top left, rgba(242,53,53,0.08) 0%, transparent 50%), radial-gradient(ellipse at bottom right, rgba(242,53,53,0.05) 0%, transparent 50%); background-attachment: fixed; min-height: 100vh; padding: 30px 20px; color: #fff; }
.container { max-width: 1300px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 15px; }
.page-header h1 { font-family: 'Orbitron', sans-serif; font-size: 1.8rem; background: linear-gradient(135deg, #fff, #f23535); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-transform: uppercase; letter-spacing: -0.5px; }
.page-header h1 i { -webkit-text-fill-color: #f23535; margin-right: 12px; }
.btn-voltar { display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; background: rgba(255,255,255,0.05); color: #aaa; text-decoration: none; border-radius: 12px; font-size: 0.85rem; transition: 0.3s; font-weight: 500; }
.btn-voltar:hover { background: rgba(242,53,53,0.1); color: #f23535; }
.mensagem { padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
.mensagem.sucesso { background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3); }
.mensagem.erro { background: rgba(242,53,53,0.15); color: #f23535; border: 1px solid rgba(242,53,53,0.3); }
.card-form { background: linear-gradient(145deg, rgba(30,30,30,0.8), rgba(20,20,20,0.9)); border: 1px solid rgba(255,255,255,0.06); border-radius: 24px; padding: 35px; margin-bottom: 40px; backdrop-filter: blur(20px); }
.card-form h2 { font-family: 'Orbitron', sans-serif; font-size: 1.1rem; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(242,53,53,0.15); display: flex; align-items: center; gap: 12px; text-transform: uppercase; letter-spacing: 1px; }
.card-form h2 i { color: #f23535; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group label { color: #aaa; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
.form-group input, .form-group select { padding: 14px 18px; background: #1a1a1a; border: 2px solid #333; border-radius: 12px; color: #fff; font-size: 0.95rem; outline: none; transition: 0.3s; font-family: inherit; }
.form-group input:focus, .form-group select:focus { border-color: #f23535; background: #222; box-shadow: 0 0 0 4px rgba(242,53,53,0.1); }
.form-group input:disabled { opacity: 0.4; cursor: not-allowed; }
.form-acoes { display: flex; gap: 12px; margin-top: 25px; flex-wrap: wrap; }
.btn-adicionar { flex: 1; min-width: 200px; padding: 16px; background: linear-gradient(135deg, #f23535, #c91f2c); color: #fff; border: none; border-radius: 12px; font-family: 'Orbitron', sans-serif; font-weight: 700; font-size: 0.9rem; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
.btn-adicionar:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(242,53,53,0.4); }
.btn-cancelar { padding: 16px 26px; background: rgba(255,255,255,0.05); color: #aaa; border-radius: 12px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.btn-cancelar:hover { color: #fff; }

.filtros { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
.chip { display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 50px; border: 2px solid rgba(255,255,255,0.1); color: #ccc; text-decoration: none; font-family: 'Orbitron', sans-serif; font-size: 0.68rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; transition: 0.3s; }
.chip:hover { border-color: #f23535; color: #fff; }
.chip.active { background: linear-gradient(135deg, #f23535, #c91f2c); border-color: #f23535; color: #fff; }
.chip small { opacity: 0.7; font-family: 'Space Grotesk', sans-serif; }
.busca { display: flex; gap: 10px; margin-bottom: 25px; }
.busca input { flex: 1; padding: 12px 18px; background: #1a1a1a; border: 2px solid #333; border-radius: 12px; color: #fff; font-family: inherit; font-size: 0.95rem; outline: none; }
.busca input:focus { border-color: #f23535; }
.busca button { padding: 12px 20px; background: rgba(242,53,53,0.15); border: 1px solid rgba(242,53,53,0.4); color: #f23535; border-radius: 12px; cursor: pointer; font-weight: 600; font-family: inherit; }

.tabela-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { text-align: left; color: #888; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px; padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.08); }
td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 0.92rem; vertical-align: middle; }
tr.inativo td { opacity: 0.45; }
.tag-tipo { display: inline-flex; align-items: center; gap: 6px; background: rgba(242,53,53,0.15); color: #f23535; padding: 3px 12px; border-radius: 20px; font-family: 'Orbitron', sans-serif; font-size: 0.6rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; white-space: nowrap; }
.marca { color: #888; }
.acoes { display: flex; gap: 6px; justify-content: flex-end; }
.acoes form { display: inline; }
.btn-acao { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04); color: #aaa; cursor: pointer; text-decoration: none; transition: 0.2s; font-size: 0.85rem; }
.btn-acao:hover { color: #fff; border-color: #f23535; }
.btn-acao.perigo:hover { background: rgba(242,53,53,0.2); color: #f23535; }
.empty-admin { text-align: center; padding: 60px 20px; color: #555; }
.empty-admin i { font-size: 3.5rem; color: rgba(242,53,53,0.2); margin-bottom: 18px; display: block; }
.paginacao { display: flex; justify-content: center; align-items: center; gap: 12px; margin-top: 25px; color: #888; font-size: 0.85rem; }
.paginacao a { padding: 8px 16px; border-radius: 10px; background: rgba(255,255,255,0.05); color: #ccc; text-decoration: none; }
.paginacao a:hover { color: #f23535; }
@media (max-width: 600px) {
    .page-header h1 { font-size: 1.3rem; }
    .card-form { padding: 25px 18px; }
    th.col-status, td.col-status { display: none; }
}
</style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-car-side"></i> Gerenciar Veículos</h1>
        <a href="../../telas/ADM/principalFun.php" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if ($flash): ?>
    <div class="mensagem <?= $flash['tipo'] === 'sucesso' ? 'sucesso' : 'erro' ?>">
        <i class="fas fa-<?= $flash['tipo'] === 'sucesso' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <div class="card-form" id="formVeiculo">
        <h2>
            <i class="fas fa-<?= $editando ? 'pen' : 'plus-circle' ?>"></i>
            <?= $editando ? 'Editar Modelo' : 'Cadastrar Novo Modelo' ?>
        </h2>

        <form method="POST" action="<?= htmlspecialchars(urlLista()) ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="acao" value="salvar">
            <input type="hidden" name="id" value="<?= (int) ($editando['vecid'] ?? 0) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Tipo *</label>
                    <select name="vectipo" id="selTipo" required>
                        <option value="">Selecione...</option>
                        <?php $tipoForm = $editando['vectipo'] ?? $filtroTipo; ?>
                        <?php foreach ($TIPOS as $valor => $t): ?>
                            <option value="<?= $valor ?>" <?= $tipoForm === $valor ? 'selected' : '' ?>><?= $t['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label id="lblMarca">Marca</label>
                    <input type="text" name="vecmarca" id="inpMarca" maxlength="60"
                           placeholder="Ex: Honda" value="<?= htmlspecialchars($editando['vecmarca'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label id="lblModelo">Modelo *</label>
                    <input type="text" name="vecmodelo" id="inpModelo" maxlength="120" required
                           placeholder="Ex: Civic" value="<?= htmlspecialchars($editando['vecmodelo'] ?? '') ?>">
                </div>
            </div>

            <div class="form-acoes">
                <button type="submit" class="btn-adicionar">
                    <i class="fas fa-<?= $editando ? 'save' : 'plus' ?>"></i>
                    <?= $editando ? 'SALVAR ALTERAÇÕES' : 'CADASTRAR' ?>
                </button>
                <?php if ($editando): ?>
                    <a href="<?= htmlspecialchars(urlLista()) ?>" class="btn-cancelar"><i class="fas fa-times"></i> Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card-form">
        <h2><i class="fas fa-list"></i> Modelos Cadastrados (<?= $totalFiltrado ?>)</h2>

        <div class="filtros">
            <a href="<?= htmlspecialchars(urlLista(['tipo' => '', 'p' => 1])) ?>" class="chip <?= $filtroTipo === '' ? 'active' : '' ?>">
                <i class="fas fa-th"></i> Todos <small><?= $totalGeral ?></small>
            </a>
            <?php foreach ($TIPOS as $valor => $t): ?>
            <a href="<?= htmlspecialchars(urlLista(['tipo' => $valor, 'p' => 1])) ?>" class="chip <?= $filtroTipo === $valor ? 'active' : '' ?>">
                <i class="fas <?= $t['icone'] ?>"></i> <?= $t['plural'] ?> <small><?= (int) ($contagem[$valor] ?? 0) ?></small>
            </a>
            <?php endforeach; ?>
        </div>

        <form method="GET" class="busca">
            <?php if ($filtroTipo): ?><input type="hidden" name="tipo" value="<?= $filtroTipo ?>"><?php endif; ?>
            <input type="search" name="q" placeholder="Buscar por marca ou modelo..." value="<?= htmlspecialchars($busca) ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <?php if (empty($veiculos)): ?>
            <div class="empty-admin">
                <i class="fas fa-car-side"></i>
                <p>Nenhum modelo encontrado.</p>
            </div>
        <?php else: ?>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>Tipo</th><th>Modelo</th><th class="col-status">Status</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($veiculos as $v): $t = $TIPOS[$v['vectipo']]; ?>
                    <tr class="<?= $v['vecativo'] ? '' : 'inativo' ?>">
                        <td><span class="tag-tipo"><i class="fas <?= $t['icone'] ?>"></i> <?= $t['nome'] ?></span></td>
                        <td>
                            <?php if ($v['vecmarca']): ?><span class="marca"><?= htmlspecialchars($v['vecmarca']) ?></span><?php endif; ?>
                            <?= htmlspecialchars($v['vecmodelo']) ?>
                        </td>
                        <td class="col-status"><?= $v['vecativo'] ? 'Visível' : 'Oculto' ?></td>
                        <td>
                            <div class="acoes">
                                <a href="<?= htmlspecialchars(urlLista(['editar' => $v['vecid']])) ?>#formVeiculo" class="btn-acao" title="Editar">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" action="<?= htmlspecialchars(urlLista()) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="acao" value="alternar">
                                    <input type="hidden" name="id" value="<?= (int) $v['vecid'] ?>">
                                    <button type="submit" class="btn-acao" title="<?= $v['vecativo'] ? 'Ocultar do agendamento' : 'Mostrar no agendamento' ?>">
                                        <i class="fas fa-<?= $v['vecativo'] ? 'eye' : 'eye-slash' ?>"></i>
                                    </button>
                                </form>
                                <form method="POST" action="<?= htmlspecialchars(urlLista()) ?>" onsubmit="return confirm('Remover este modelo?');">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="acao" value="deletar">
                                    <input type="hidden" name="id" value="<?= (int) $v['vecid'] ?>">
                                    <button type="submit" class="btn-acao perigo" title="Remover">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPaginas > 1): ?>
        <div class="paginacao">
            <?php if ($pagina > 1): ?><a href="<?= htmlspecialchars(urlLista(['p' => $pagina - 1])) ?>"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
            <span>Página <?= $pagina ?> de <?= $totalPaginas ?></span>
            <?php if ($pagina < $totalPaginas): ?><a href="<?= htmlspecialchars(urlLista(['p' => $pagina + 1])) ?>"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Mobília não tem marca: o campo vira "medida/descrição" no modelo
const selTipo = document.getElementById('selTipo');
const inpMarca = document.getElementById('inpMarca');
const inpModelo = document.getElementById('inpModelo');
function ajustarCampos() {
    const mobilia = selTipo.value === 'mobilia';
    inpMarca.disabled = mobilia;
    if (mobilia) inpMarca.value = '';
    document.getElementById('lblModelo').textContent = mobilia ? 'Móvel e medida *' : 'Modelo *';
    inpModelo.placeholder = mobilia ? 'Ex: Mesa 1,80m x 0,90m' : 'Ex: Civic';
}
selTipo.addEventListener('change', ajustarCampos);
ajustarCampos();
</script>

</body>
</html>
