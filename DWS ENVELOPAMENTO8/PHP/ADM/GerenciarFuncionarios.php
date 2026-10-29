<?php
// =============================================
// GerenciarFuncionarios.php - Apenas Dariel
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

// Cadastrar Funcionário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'cadastrar') {
    exigirCSRF();
    $nome = trim($_POST['nome'] ?? '');
    $usuario = trim($_POST['usuario'] ?? ''); // LOGIN
    $senha = $_POST['senha'] ?? '';           // SENHA
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $salario = (float)str_replace(',', '.', $_POST['salario'] ?? '0');
    $cargo_id = (int)($_POST['cargo_id'] ?? 0) ?: null;

    if ($nome === '' || $usuario === '' || $senha === '') {
        $mensagem = 'Nome, Usuário de Login e Senha são obrigatórios.';
        $tipoMsg = 'erro';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM funcionarios WHERE usuario = :u LIMIT 1");
            $stmt->execute([':u' => $usuario]);
            if ($stmt->fetch()) {
                $mensagem = 'Este nome de usuário já existe.';
                $tipoMsg = 'erro';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO funcionarios (nome, usuario, senha, cpf, telefone, salario, cargo_id) 
                    VALUES (:n, :u, :s, :c, :t, :sal, :cargo)
                ");
                $stmt->execute([
                    ':n' => $nome, ':u' => $usuario, ':s' => $hash, 
                    ':c' => $cpf, ':t' => $telefone, ':sal' => $salario, ':cargo' => $cargo_id
                ]);
                $mensagem = 'Funcionário cadastrado!';
                $tipoMsg = 'sucesso';
            }
        } catch (PDOException $e) {
            $mensagem = 'Erro ao cadastrar.';
            $tipoMsg = 'erro';
        }
    }
}

// Editar Funcionário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'editar') {
    exigirCSRF();
    $id = (int)$_POST['id'];
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $salario = (float)str_replace(',', '.', $_POST['salario'] ?? '0');
    $cargo_id = (int)($_POST['cargo_id'] ?? 0) ?: null;
    $nova_senha = $_POST['senha'] ?? '';

    try {
        if ($nova_senha !== '') {
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE funcionarios SET nome=:n, cpf=:c, telefone=:t, salario=:sal, cargo_id=:cargo, senha=:s WHERE id=:id");
            $stmt->execute([':n'=>$nome, ':c'=>$cpf, ':t'=>$telefone, ':sal'=>$salario, ':cargo'=>$cargo_id, ':s'=>$hash, ':id'=>$id]);
        } else {
            $stmt = $pdo->prepare("UPDATE funcionarios SET nome=:n, cpf=:c, telefone=:t, salario=:sal, cargo_id=:cargo WHERE id=:id");
            $stmt->execute([':n'=>$nome, ':c'=>$cpf, ':t'=>$telefone, ':sal'=>$salario, ':cargo'=>$cargo_id, ':id'=>$id]);
        }
        $mensagem = 'Funcionário atualizado!';
        $tipoMsg = 'sucesso';
    } catch (PDOException $e) {
        $mensagem = 'Erro ao atualizar.';
        $tipoMsg = 'erro';
    }
}

// Excluir Funcionário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'excluir') {
    exigirCSRF();
    $id = (int)$_POST['id'];
    if ($id !== $SUPER_ADMIN_ID) {
        $pdo->prepare("DELETE FROM funcionarios WHERE id = :id")->execute([':id' => $id]);
        $mensagem = 'Funcionário excluído!';
        $tipoMsg = 'sucesso';
    }
}

$funcionarios = $pdo->query("
    SELECT f.*, c.nome as cargo_nome 
    FROM funcionarios f 
    LEFT JOIN cargos c ON c.id = f.cargo_id 
    ORDER BY f.nome ASC
")->fetchAll(PDO::FETCH_ASSOC);

$cargos = $pdo->query("SELECT * FROM cargos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar Funcionários - DWS</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Space Grotesk', sans-serif; background: #0a0a0a; color: #fff; padding: 30px 20px; background-image: radial-gradient(ellipse at top left, rgba(242, 53, 53, 0.08) 0%, transparent 50%); }
.container { max-width: 1200px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
.page-header h1 { font-family: 'Orbitron', sans-serif; font-size: 1.8rem; color: #fff; }
.page-header h1 i { color: #f23535; margin-right: 10px; }
.header-actions { display: flex; gap: 10px; }
.btn-voltar { color: #aaa; text-decoration: none; padding: 10px 20px; background: rgba(255,255,255,0.05); border-radius: 10px; transition: 0.3s; }
.btn-voltar:hover { background: rgba(242,53,53,0.1); color: #f23535; }
.btn-cargos { color: #fff; text-decoration: none; padding: 10px 20px; background: rgba(242,53,53,0.2); border: 1px solid #f23535; border-radius: 10px; transition: 0.3s; }
.btn-cargos:hover { background: #f23535; }
.mensagem { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
.mensagem.sucesso { background: rgba(76,175,80,0.15); color: #4CAF50; }
.mensagem.erro { background: rgba(242,53,53,0.15); color: #f23535; }
.card { background: #1a1a1a; border-radius: 20px; padding: 25px; margin-bottom: 25px; border: 1px solid #333; }
.card h2 { font-size: 1.1rem; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group label { font-size: 0.8rem; color: #888; }
.form-group input, .form-group select { padding: 12px; background: #0a0a0a; border: 1px solid #444; border-radius: 10px; color: #fff; outline: none; }
.form-group input:focus, .form-group select:focus { border-color: #f23535; }
.btn-salvar { padding: 12px 25px; background: #f23535; border: none; border-radius: 10px; color: #fff; font-weight: bold; cursor: pointer; width: 100%; }
table { width: 100%; border-collapse: collapse; min-width: 900px; }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; font-size: 0.9rem; }
th { color: #888; font-size: 0.8rem; text-transform: uppercase; }
.btn-acao { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; margin-right: 5px; }
.btn-edit { background: rgba(255,193,7,0.2); color: #ffc107; }
.btn-delete { background: rgba(242,53,53,0.2); color: #f23535; }
.modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
.modal.open { display: flex; }
.modal-content { background: #1a1a1a; border: 1px solid #333; border-radius: 20px; padding: 30px; width: 100%; max-width: 600px; }
.modal-content h3 { margin-bottom: 20px; color: #f23535; }
.btn-fechar { background: #333; color: #fff; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer; }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-users-cog"></i> Gerenciar Funcionários</h1>
        <div class="header-actions">
            <a href="GerenciarCargos.php" class="btn-cargos"><i class="fas fa-briefcase"></i> Gerenciar Cargos</a>
            <a href="../../telas/ADM/principalFun.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem <?= $tipoMsg ?>"><?= $mensagem ?></div>
    <?php endif; ?>

    <!-- Formulário de Cadastro -->
    <div class="card">
        <h2>Cadastrar Novo Funcionário</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCSRF()) ?>">
            <input type="hidden" name="acao" value="cadastrar">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group">
                    <label>Usuário de Acesso (Login)</label>
                    <input type="text" name="usuario" required placeholder="ex: joao123">
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required>
                </div>
                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" placeholder="000.000.000-00">
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" placeholder="(14) 99999-9999">
                </div>
                <div class="form-group">
                    <label>Salário (R$)</label>
                    <input type="text" name="salario" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Cargo</label>
                    <select name="cargo_id">
                        <option value="">Selecione...</option>
                        <?php foreach ($cargos as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-salvar">Cadastrar Funcionário</button>
        </form>
    </div>

    <!-- Tabela de Funcionários -->
    <div class="card">
        <h2>Funcionários Cadastrados</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nome Completo</th>
                        <th>Login / Senha</th>
                        <th>Cargo</th>
                        <th>CPF / Telefone</th>
                        <th>Salário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($funcionarios as $f): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($f['nome']) ?></strong></td>
                        <td>
                            <span style="color: #4CAF50; font-weight: bold;">@<?= htmlspecialchars($f['usuario']) ?></span><br>
                            <small style="color:#666;">Senha oculta (hash)</small>
                        </td>
                        <td><?= htmlspecialchars($f['cargo_nome'] ?? '—') ?></td>
                        <td>
                            <?= htmlspecialchars($f['cpf'] ?? '—') ?><br>
                            <small style="color:#888;"><?= htmlspecialchars($f['telefone'] ?? '—') ?></small>
                        </td>
                        <td>R$ <?= number_format($f['salario'], 2, ',', '.') ?></td>
                        <td>
                            <?php if ($f['id'] == $SUPER_ADMIN_ID): ?>
                                <span style="color: #4CAF50; font-weight: bold; font-size: 0.8rem;">👑 Super Admin</span>
                            <?php else: ?>
                                <button type="button" class="btn-acao btn-edit" onclick='abrirModalEdicao(<?= json_encode($f, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'><i class="fas fa-edit"></i> Editar</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir funcionário?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCSRF()) ?>">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                    <button type="submit" class="btn-acao btn-delete"><i class="fas fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal" id="modalEdicao">
    <div class="modal-content">
        <h3>Editar Funcionário</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarTokenCSRF()) ?>">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" id="edit_nome" required>
                </div>
                <div class="form-group">
                    <label>Nova Senha (deixe em branco p/ manter)</label>
                    <input type="password" name="senha" id="edit_senha">
                </div>
                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" id="edit_cpf">
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" id="edit_telefone">
                </div>
                <div class="form-group">
                    <label>Salário (R$)</label>
                    <input type="text" name="salario" id="edit_salario">
                </div>
                <div class="form-group">
                    <label>Cargo</label>
                    <select name="cargo_id" id="edit_cargo_id">
                        <option value="">Selecione...</option>
                        <?php foreach ($cargos as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-salvar" style="flex: 1;">Salvar Alterações</button>
                <button type="button" class="btn-fechar" onclick="document.getElementById('modalEdicao').classList.remove('open')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalEdicao(func) {
    document.getElementById('edit_id').value = func.id;
    document.getElementById('edit_nome').value = func.nome;
    document.getElementById('edit_senha').value = '';
    document.getElementById('edit_cpf').value = func.cpf || '';
    document.getElementById('edit_telefone').value = func.telefone || '';
    document.getElementById('edit_salario').value = func.salario;
    document.getElementById('edit_cargo_id').value = func.cargo_id || '';
    document.getElementById('modalEdicao').classList.add('open');
}
</script>
</body>
</html>