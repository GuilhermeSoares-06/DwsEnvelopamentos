<?php
// =============================================
// GerenciarGaleria.php - Admin posta itens
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

// =============================================
// PROCESSAR AÇÕES
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // DELETAR
    if ($acao === 'deletar') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("SELECT imagem FROM galeria WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item) {
                $caminhoArquivo = __DIR__ . '/../../' . $item['imagem'];
                if (file_exists($caminhoArquivo)) unlink($caminhoArquivo);

                $pdo->prepare("DELETE FROM galeria WHERE id = :id")->execute([':id' => $id]);
                $mensagem = 'Item removido com sucesso!';
                $tipoMsg = 'sucesso';
            }
        } catch (PDOException $e) {
            $mensagem = 'Erro ao remover item.';
            $tipoMsg = 'erro';
        }
    }

    // ADICIONAR
    if ($acao === 'adicionar') {
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');

        if (empty($titulo) || empty($tipo)) {
            $mensagem = 'Preencha todos os campos obrigatórios.';
            $tipoMsg = 'erro';
        } elseif (empty($_FILES['imagem']['name'])) {
            $mensagem = 'Selecione uma imagem.';
            $tipoMsg = 'erro';
        } else {
            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $extensoesPermitidas)) {
                $mensagem = 'Formato de imagem inválido. Use JPG, PNG, WEBP ou GIF.';
                $tipoMsg = 'erro';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $mensagem = 'A imagem deve ter no máximo 5MB.';
                $tipoMsg = 'erro';
            } else {
                // 🔥 AGORA SALVA EM img/galeria/
                $pastaDestino = __DIR__ . '/../../img/galeria/';
                if (!is_dir($pastaDestino)) mkdir($pastaDestino, 0755, true);

                $nomeArquivo = 'gal_' . uniqid() . '.' . $ext;
                $caminhoCompleto = $pastaDestino . $nomeArquivo;

                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
                    // 🔥 CAMINHO SALVO NO BANCO
                    $caminhoBanco = 'img/galeria/' . $nomeArquivo;

                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO galeria (titulo, descricao, tipo, imagem)
                            VALUES (:t, :d, :ti, :i)
                        ");
                        $stmt->execute([
                            ':t' => $titulo,
                            ':d' => $descricao,
                            ':ti' => $tipo,
                            ':i' => $caminhoBanco
                        ]);
                        $mensagem = 'Item adicionado à galeria!';
                        $tipoMsg = 'sucesso';
                    } catch (PDOException $e) {
                        $mensagem = 'Erro ao salvar no banco: ' . $e->getMessage();
                        $tipoMsg = 'erro';
                    }
                } else {
                    $mensagem = 'Erro ao fazer upload da imagem.';
                    $tipoMsg = 'erro';
                }
            }
        }
    }
}

// Buscar itens da galeria
$items = $pdo->query("SELECT * FROM galeria ORDER BY criado_em DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar Galeria - DWS Admin</title>
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

.container { max-width: 1300px; margin: 0 auto; }

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.8rem;
    background: linear-gradient(135deg, #fff, #f23535);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-transform: uppercase;
    letter-spacing: -0.5px;
}

.page-header h1 i {
    -webkit-text-fill-color: #f23535;
    margin-right: 12px;
}

.btn-voltar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 22px;
    background: rgba(255, 255, 255, 0.05);
    color: #aaa;
    text-decoration: none;
    border-radius: 12px;
    font-size: 0.85rem;
    transition: 0.3s;
    font-weight: 500;
}

.btn-voltar:hover {
    background: rgba(242, 53, 53, 0.1);
    color: #f23535;
}

.mensagem {
    padding: 15px 25px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 12px;
}

.mensagem.sucesso {
    background: rgba(76, 175, 80, 0.15);
    color: #4CAF50;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.mensagem.erro {
    background: rgba(242, 53, 53, 0.15);
    color: #f23535;
    border: 1px solid rgba(242, 53, 53, 0.3);
}

/* FORMULÁRIO */
.card-form {
    background: linear-gradient(145deg, rgba(30,30,30,0.8), rgba(20,20,20,0.9));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 24px;
    padding: 35px;
    margin-bottom: 40px;
    backdrop-filter: blur(20px);
}

.card-form h2 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.1rem;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.card-form h2 i { color: #f23535; }

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
}

.form-group { display: flex; flex-direction: column; gap: 8px; }

.form-group label {
    color: #aaa;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 14px 18px;
    background: #1a1a1a;
    border: 2px solid #333;
    border-radius: 12px;
    color: #fff;
    font-size: 0.95rem;
    outline: none;
    transition: 0.3s;
    font-family: inherit;
    resize: vertical;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #f23535;
    background: #222;
    box-shadow: 0 0 0 4px rgba(242,53,53,0.1);
}

.form-group textarea { min-height: 90px; }

.form-group input[type="file"] {
    padding: 12px;
    cursor: pointer;
}

.form-group input[type="file"]::file-selector-button {
    background: #f23535;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    margin-right: 12px;
}

.btn-adicionar {
    width: 100%;
    padding: 16px;
    margin-top: 25px;
    background: linear-gradient(135deg, #f23535, #c91f2c);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    font-size: 0.9rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-adicionar:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(242,53,53,0.4);
}

/* GRID DE ITENS */
.galeria-admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 22px;
}

.galeria-admin-card {
    position: relative;
    background: #1a1a1a;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.06);
    transition: 0.3s;
}

.galeria-admin-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(242,53,53,0.15);
    border-color: rgba(242,53,53,0.3);
}

.galeria-admin-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}

.galeria-admin-info { padding: 18px; }

.galeria-admin-tipo {
    display: inline-block;
    background: rgba(242,53,53,0.15);
    color: #f23535;
    padding: 3px 12px;
    border-radius: 20px;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.galeria-admin-titulo {
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.galeria-admin-desc {
    color: #888;
    font-size: 0.8rem;
    line-height: 1.5;
}

.btn-deletar-galeria {
    width: 100%;
    padding: 10px;
    background: rgba(242,53,53,0.1);
    border: 1px solid rgba(242,53,53,0.3);
    color: #f23535;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 14px;
    font-family: inherit;
}

.btn-deletar-galeria:hover {
    background: rgba(242,53,53,0.25);
}

.empty-admin {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    color: #555;
}

.empty-admin i {
    font-size: 4rem;
    color: rgba(242,53,53,0.2);
    margin-bottom: 20px;
    display: block;
}

@media (max-width: 600px) {
    .page-header h1 { font-size: 1.3rem; }
    .card-form { padding: 25px 20px; }
    .galeria-admin-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-images"></i> Gerenciar Galeria</h1>
        <a href="../../telas/ADM/principalFUN.html" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if ($mensagem): ?>
    <div class="mensagem <?= $tipoMsg ?>">
        <i class="fas fa-<?= $tipoMsg === 'sucesso' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($mensagem) ?>
    </div>
    <?php endif; ?>

    <!-- FORMULÁRIO -->
    <div class="card-form">
        <h2><i class="fas fa-plus-circle"></i> Adicionar Novo Trabalho</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="adicionar">

            <div class="form-grid">
                <div class="form-group">
                    <label>Título *</label>
                    <input type="text" name="titulo" placeholder="Ex: Envelopamento Honda Civic" required maxlength="150">
                </div>

                <div class="form-group">
                    <label>Tipo *</label>
                    <select name="tipo" required>
                        <option value="">Selecione...</option>
                        <option value="carro">🚗 Carro</option>
                        <option value="moto">🏍️ Moto</option>
                        <option value="caminhao">🚛 Caminhão</option>
                        <option value="aquatico">⛵ Aquático</option>
                        <option value="mobilia">🪑 Mobília</option>
                    </select>
                </div>
            </div>

            <div class="form-grid" style="margin-top: 20px;">
                <div class="form-group">
                    <label>Imagem * (máx 5MB)</label>
                    <input type="file" name="imagem" accept="image/*" required>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label>Descrição</label>
                <textarea name="descricao" placeholder="Descreva o trabalho realizado..." maxlength="500"></textarea>
            </div>

            <button type="submit" class="btn-adicionar">
                <i class="fas fa-upload"></i> PUBLICAR NA GALERIA
            </button>
        </form>
    </div>

    <!-- LISTA -->
    <div class="card-form">
        <h2><i class="fas fa-list"></i> Trabalhos Publicados (<?= count($items) ?>)</h2>

        <div class="galeria-admin-grid">
            <?php if (empty($items)): ?>
                <div class="empty-admin">
                    <i class="fas fa-images"></i>
                    <p>Nenhum trabalho publicado ainda.</p>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                <div class="galeria-admin-card">
                    <img src="../../<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['titulo']) ?>">
                    <div class="galeria-admin-info">
                        <span class="galeria-admin-tipo"><?= htmlspecialchars($item['tipo']) ?></span>
                        <div class="galeria-admin-titulo"><?= htmlspecialchars($item['titulo']) ?></div>
                        <?php if ($item['descricao']): ?>
                            <div class="galeria-admin-desc"><?= htmlspecialchars(mb_strimwidth($item['descricao'], 0, 80, '…')) ?></div>
                        <?php endif; ?>

                        <form method="POST" onsubmit="return confirm('Remover este item?');">
                            <input type="hidden" name="acao" value="deletar">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn-deletar-galeria">
                                <i class="fas fa-trash"></i> REMOVER
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>