<?php
// =============================================
// GerenciarCargos.php - Apenas Dariel
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca();
exigirLoginAdmin();

$SUPER_ADMIN_ID = (int) ((array) config('admin.super_admin_ids', [1]))[0];
if (!isSuperAdmin()) {
    header('Location: ../../telas/ADM/principalFun.php');
    exit;
}

$mensagem = '';
$tipoMsg = '';

// Cadastrar / Editar Cargo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'salvar_cargo') {
    exigirCSRF();
    $nome = trim($_POST['nome'] ?? '');
    $id = (int)($_POST['id'] ?? 0);

    if ($nome === '') {
        $mensagem = 'O nome do cargo é obrigatório.';
        $tipoMsg = 'erro';
    } else {
        try {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE cargos SET nome = :n WHERE id = :id");
                $stmt->execute([':n' => $nome, ':id' => $id]);
                $mensagem = 'Cargo atualizado!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO cargos (nome) VALUES (:n)");
                $stmt->execute([':n' => $nome]);
                $mensagem = 'Cargo adicionado!';
            }
            $tipoMsg = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao salvar cargo.';
            $tipoMsg = 'erro';
        }
    }
}

// Excluir Cargo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'excluir_cargo') {
    exigirCSRF();
    $id = (int)($_POST['id'] ?? 0);
    try {
        $pdo->prepare("DELETE FROM cargos WHERE id = :id")->execute([':id' => $id]);
        $mensagem = 'Cargo removido!';
        $tipoMsg = 'sucesso';
    } catch (PDOException $e) {
        $mensagem = 'Não é possível excluir. Existem funcionários usando este cargo.';
        $tipoMsg = 'erro';
    }
}

$cargos = $pdo->query("SELECT * FROM cargos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar Cargos - DWS</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Space Grotesk', sans-serif; background: #0a0a0a; color: #fff; padding: 30px 20px; background-image: radial-gradient(ellipse at top left, rgba(242, 53, 53, 0.08) 0%, transparent 50%); }
.container { max-width: 800px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.page-header h1 { font-family: 'Orbitron', sans-serif; font-size: 1.5rem; color: #fff; }
.page-header h1 i { color: #f23535; margin-right: 10px; }
.btn-voltar { color: #aaa; text-decoration: none; padding: 10px 20px; background: rgba(255,255,255,0.05); border-radius: 10px; transition: 0.3s; }
.btn-voltar:hover { background: rgba(242,53,53,0.1); color: #f23535; }
.mensagem { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
.mensagem.sucesso { background: rgba(76,175,80,0.15); color: #4CAF50; }
.mensagem.erro { background: rgba(242,53,53,0.15); color: #f23535; }
.card { background: #1a1a1a; border-radius: 20px; padding: 25px; margin-bottom: 25px; border: 1px solid #333; }
.card h2 { font-size: 1.1rem; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; }
.form-inline { display: flex; gap: 15px; }
.form-inline input { flex: 1; padding: 12px; background: #0a0a0a; border: 1px solid #444; border-radius: 10px; color: #fff; outline: none; }
.form-inline input:focus { border-color: #f23535; }
.btn-salvar { padding: 12px 25px; background: #f23535; border: none; border-radius: 10px; color: #fff; font-weight: bold; cursor: pointer; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
th { color: #888; font-size: 0.8rem; text-transform: uppercase; }
.btn-acao { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; margin-right: 5px; }
.btn-edit { background: rgba(255,193,7,0.2); color: #ffc107; }
.btn-delete { background: rgba(242,53,53,0.2); color: #f23535; }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-briefcase"></i> Gerenciar Cargos</h1>
        <a href="GerenciarFuncionarios.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar aos Funcionários</a>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem <?= $tipoMsg ?>"><?= $mensagem ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>Adicionar Novo Cargo</h2>
        <form method="POST" class="form-inline">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCSRF()) ?>">
            <input type="hidden" name="acao" value="salvar_cargo">
            <input type="text" name="nome" placeholder="Ex: Gerente, Envelopador, Atendente..." required>
            <button type="submit" class="btn-salvar">Adicionar</button>
        </form>
    </div>

    <div class="card">
        <h2>Cargos Cadastrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Cargo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cargos as $c): ?>
                <tr>
                    <td>#<?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['nome']) ?></td>
                    <td>
                        <!-- Botão Editar (Abre um prompt simples no JS para não criar outro arquivo) -->
                        <button type="button" class="btn-acao btn-edit" onclick="editarCargo(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['nome'])) ?>')"><i class="fas fa-edit"></i> Editar</button>
                        
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir cargo?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCSRF()) ?>">
                            <input type="hidden" name="acao" value="excluir_cargo">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn-acao btn-delete"><i class="fas fa-trash"></i> Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Formulário escondido para edição -->
<form id="formEditar" method="POST" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCSRF()) ?>">
    <input type="hidden" name="acao" value="salvar_cargo">
    <input type="hidden" name="id" id="editIdCargo">
    <input type="hidden" name="nome" id="editNomeCargo">
</form>

<script>
function editarCargo(id, nomeAtual) {
    let novoNome = prompt("Editar nome do cargo:", nomeAtual);
    if (novoNome !== null && novoNome.trim() !== '') {
        document.getElementById('editIdCargo').value = id;
        document.getElementById('editNomeCargo').value = novoNome.trim();
        document.getElementById('formEditar').submit();
    }
}
</script>
</body>
</html>