<?php
// =============================================
// GerenciarPrecos.php - Admin edita preços
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se é admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../telas/ADM/login.html');
    exit;
}

// Processa POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'salvar') {
        $chave = trim($_POST['chave'] ?? '');
        $valor = trim($_POST['valor'] ?? '');
        
        if (empty($chave)) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'Chave inválida']);
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("
                UPDATE configuracoes SET valor = :v WHERE chave = :c
            ");
            $stmt->execute([':v' => $valor, ':c' => $chave]);
            
            echo json_encode(['status' => 'sucesso', 'mensagem' => 'Preço atualizado!']);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar preço: " . $e->getMessage());
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar']);
        }
        exit;
    }
}

// Busca todas as configurações
$configs = $pdo->query("SELECT * FROM configuracoes ORDER BY chave")->fetchAll(PDO::FETCH_ASSOC);

// Organiza em array associativo
$cfg = [];
foreach ($configs as $c) {
    $cfg[$c['chave']] = $c;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar Preços - DWS Admin</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Inter', Arial, sans-serif;
    background: #0a0a0a;
    background-image:
        radial-gradient(ellipse at top left, rgba(242, 53, 53, 0.08) 0%, transparent 50%),
        radial-gradient(ellipse at bottom right, rgba(242, 53, 53, 0.05) 0%, transparent 50%),
        linear-gradient(135deg, #0a0a0a 0%, #111111 25%, #000000 100%);
    background-attachment: fixed;
    min-height: 100vh;
    padding: 30px 20px;
    color: #fff;
}
.container {
    max-width: 900px;
    margin: 0 auto;
}
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}
.page-header h1 {
    font-size: 1.8rem;
    background: linear-gradient(135deg, #fff, #f23535);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.page-header h1 i {
    -webkit-text-fill-color: #f23535;
    margin-right: 10px;
}
.btn-voltar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.05);
    color: #aaa;
    text-decoration: none;
    border-radius: 10px;
    font-size: 14px;
    transition: 0.3s;
}
.btn-voltar:hover {
    background: rgba(242, 53, 53, 0.1);
    color: #f23535;
}
.card {
    background: linear-gradient(145deg, rgba(30,30,30,0.8), rgba(20,20,20,0.9));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 20px;
    backdrop-filter: blur(20px);
}
.card h2 {
    font-size: 1.1rem;
    color: #fff;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
    display: flex;
    align-items: center;
    gap: 10px;
}
.card h2 i {
    color: #f23535;
}
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.form-group label {
    color: #aaa;
    font-size: 0.85rem;
    font-weight: 500;
}
.form-group input {
    padding: 12px 16px;
    background: #1a1a1a;
    border: 2px solid #333;
    border-radius: 10px;
    color: #fff;
    font-size: 1rem;
    outline: none;
    transition: 0.3s;
}
.form-group input:focus {
    border-color: #f23535;
    background: #222;
    box-shadow: 0 0 0 4px rgba(242,53,53,0.1);
}
.form-group .hint {
    color: #666;
    font-size: 0.75rem;
}
.btn-salvar {
    width: 100%;
    padding: 14px;
    margin-top: 25px;
    background: linear-gradient(135deg, #f23535, #c91f2c);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.btn-salvar:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(242,53,53,0.4);
}
.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    padding: 16px 24px;
    background: #4CAF50;
    color: #fff;
    border-radius: 12px;
    font-weight: 600;
    z-index: 9999;
    display: none;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}
.toast.error { background: #f23535; }
@media (max-width: 600px) {
    .page-header h1 { font-size: 1.4rem; }
    .card { padding: 20px 15px; }
}
</style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-dollar-sign"></i> Gerenciar Preços</h1>
        <a href="../../telas/ADM/principalFUN.html" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form id="formPrecos" class="card">
        <h2><i class="fas fa-car"></i> Preços Base dos Serviços</h2>
        <div class="form-grid">
            <div class="form-group">
                <label>🚗 Carro (R$)</label>
                <input type="number" name="preco_carro" step="0.01" min="0" 
                       value="<?= htmlspecialchars($cfg['preco_carro']['valor'] ?? '800') ?>">
                <span class="hint">Preço base para envelopamento de carro</span>
            </div>

            <div class="form-group">
                <label>🏍️ Moto (R$)</label>
                <input type="number" name="preco_moto" step="0.01" min="0" 
                       value="<?= htmlspecialchars($cfg['preco_moto']['valor'] ?? '500') ?>">
                <span class="hint">Preço base para envelopamento de moto</span>
            </div>

            <div class="form-group">
                <label>🚛 Caminhão (R$)</label>
                <input type="number" name="preco_caminhao" step="0.01" min="0" 
                       value="<?= htmlspecialchars($cfg['preco_caminhao']['valor'] ?? '2500') ?>">
                <span class="hint">Preço base para envelopamento de caminhão</span>
            </div>

            <div class="form-group">
                <label>⛵ Aquático (R$)</label>
                <input type="number" name="preco_aquatico" step="0.01" min="0" 
                       value="<?= htmlspecialchars($cfg['preco_aquatico']['valor'] ?? '1800') ?>">
                <span class="hint">Preço base para envelopamento aquático</span>
            </div>

            <div class="form-group">
                <label>🪑 Mobília (R$)</label>
                <input type="number" name="preco_mobilia" step="0.01" min="0" 
                       value="<?= htmlspecialchars($cfg['preco_mobilia']['valor'] ?? '300') ?>">
                <span class="hint">Preço base para envelopamento de mobília</span>
            </div>
        </div>

        <h2 style="margin-top: 40px;"><i class="fas fa-palette"></i> Fatores de Acabamento</h2>
        <div class="form-grid">
            <div class="form-group">
                <label>🎨 Brilhante (fator)</label>
                <input type="number" name="acabamento_brilhante" step="0.01" min="1" 
                       value="<?= htmlspecialchars($cfg['acabamento_brilhante']['valor'] ?? '1.15') ?>">
                <span class="hint">Ex: 1.15 = +15% no valor</span>
            </div>

            <div class="form-group">
                <label>🎨 Perolizado (fator)</label>
                <input type="number" name="acabamento_perolizado" step="0.01" min="1" 
                       value="<?= htmlspecialchars($cfg['acabamento_perolizado']['valor'] ?? '1.30') ?>">
                <span class="hint">Ex: 1.30 = +30% no valor</span>
            </div>

            <div class="form-group">
                <label>🎨 Texturizado (fator)</label>
                <input type="number" name="acabamento_texturizado" step="0.01" min="1" 
                       value="<?= htmlspecialchars($cfg['acabamento_texturizado']['valor'] ?? '1.40') ?>">
                <span class="hint">Ex: 1.40 = +40% no valor</span>
            </div>
        </div>

        <button type="submit" class="btn-salvar">
            <i class="fas fa-save"></i> SALVAR ALTERAÇÕES
        </button>
    </form>
</div>

<div class="toast" id="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastText">Salvo!</span>
</div>

<script>
document.getElementById('formPrecos').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const btn = e.target.querySelector('.btn-salvar');
    const originalHTML = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    btn.disabled = true;

    try {
        // Salva cada campo
        const promises = [];
        for (const [chave, valor] of formData.entries()) {
            const fd = new FormData();
            fd.append('acao', 'salvar');
            fd.append('chave', chave);
            fd.append('valor', valor);
            
            promises.push(fetch('GerenciarPrecos.php', {
                method: 'POST',
                body: fd
            }).then(r => r.json()));
        }

        const resultados = await Promise.all(promises);
        const todosOk = resultados.every(r => r.status === 'sucesso');

        if (todosOk) {
            mostrarToast('✅ Preços atualizados com sucesso!');
        } else {
            mostrarToast('⚠️ Alguns preços não foram salvos', 'error');
        }
    } catch (erro) {
        console.error(erro);
        mostrarToast('❌ Erro de conexão', 'error');
    } finally {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }
});

function mostrarToast(msg, tipo = 'success') {
    const toast = document.getElementById('toast');
    document.getElementById('toastText').textContent = msg;
    toast.className = 'toast ' + (tipo === 'error' ? 'error' : '');
    toast.style.display = 'flex';
    
    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}
</script>

</body>
</html>