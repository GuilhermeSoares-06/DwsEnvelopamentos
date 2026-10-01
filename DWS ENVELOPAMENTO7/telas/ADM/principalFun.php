<?php
// =============================================
// principalFUN.php - Painel Admin protegido
// =============================================
require_once __DIR__ . '/../../PHP/Banco/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// 🚫 Anti-cache — impede o "Voltar" mostrar o painel
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// 🚫 Sem sessão → volta pro login
if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_logado'])) {
    header('Location: login.html?erro=restrito&t=' . time());
    exit;
}

// ⏱️ Timeout de 2h
if (isset($_SESSION['admin_login_time']) && time() - $_SESSION['admin_login_time'] > 7200) {
    session_destroy();
    header('Location: login.html?erro=timeout');
    exit;
}

// 🔍 Confirma no banco que o admin ainda existe
try {
    $stmt = $pdo->prepare("
        SELECT cliid, clinome FROM clientes
        WHERE cliid = :id AND tipocliente = 'funcionario'
        LIMIT 1
    ");
    $stmt->execute([':id' => (int)$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        session_destroy();
        header('Location: login.html?erro=excluido');
        exit;
    }
} catch (PDOException $e) {
    error_log("Erro principalFUN: " . $e->getMessage());
    header('Location: login.html?erro=2');
    exit;
}

$nomeAdmin = htmlspecialchars($admin['clinome'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DWS — Painel Admin</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Space Grotesk', sans-serif;
    background: #050505;
    color: #fff;
    min-height: 100vh;
    overflow-x: hidden;
}

.bg-animated {
    position: fixed; inset: 0; z-index: -2;
    background:
        radial-gradient(ellipse at 15% 15%, rgba(242, 53, 53, 0.10) 0%, transparent 45%),
        radial-gradient(ellipse at 85% 85%, rgba(242, 53, 53, 0.08) 0%, transparent 45%),
        radial-gradient(ellipse at 50% 50%, #0a0a0a 0%, #000 100%);
}
.bg-grid {
    position: fixed; inset: 0; z-index: -1;
    background-image:
        linear-gradient(rgba(242, 53, 53, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(242, 53, 53, 0.03) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse at center, #000 0%, transparent 70%);
    -webkit-mask-image: radial-gradient(ellipse at center, #000 0%, transparent 70%);
}

.topbar {
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(20px);
    border-bottom: 2px solid rgba(242, 53, 53, 0.3);
    padding: 15px 35px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 1000;
    flex-wrap: wrap; gap: 15px;
}
.topbar .brand { display: flex; align-items: center; gap: 15px; }
.topbar .brand img { height: 45px; filter: drop-shadow(0 0 8px rgba(242,53,53,0.4)); }
.topbar .brand h1 {
    font-family: 'Orbitron', sans-serif; font-size: 1.2rem;
    font-weight: 800; margin: 0; text-transform: uppercase;
}
.topbar .brand h1 span { color: #f23535; }

.badge-admin {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(242,53,53,0.15); color: #f23535;
    padding: 5px 14px; border-radius: 50px;
    font-family: 'Orbitron', sans-serif; font-size: 0.6rem;
    font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
    border: 1px solid rgba(242,53,53,0.3);
}

.topbar .user-area { display: flex; align-items: center; gap: 18px; }
.user-greeting { text-align: right; font-size: 0.85rem; color: #aaa; line-height: 1.3; }
.user-greeting strong { display: block; color: #fff; font-weight: 700; }
.user-greeting .status { color: #4CAF50; font-size: 0.7rem; font-weight: 600; }

.user-avatar {
    width: 45px; height: 45px; border-radius: 50%;
    border: 2px solid #f23535; object-fit: cover;
    transition: 0.3s; cursor: pointer;
    box-shadow: 0 0 15px rgba(242,53,53,0.3);
}
.user-avatar:hover { transform: scale(1.08); box-shadow: 0 0 25px rgba(242,53,53,0.5); }

.menu-toggle {
    display: none; background: none; border: none; color: #fff;
    font-size: 1.5rem; cursor: pointer; padding: 8px 12px;
    border-radius: 10px; transition: 0.3s;
}
.menu-toggle:hover { background: rgba(242,53,53,0.15); color: #f23535; }

.sidebar-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);
    z-index: 1050;
}
.sidebar-overlay.active { display: block; }

.sidebar {
    position: fixed; top: 0; left: 0;
    width: 300px; height: 100vh;
    background: linear-gradient(180deg, #0a0a0a, #111);
    border-right: 1px solid rgba(242,53,53,0.2);
    z-index: 1060;
    transition: left 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    display: flex; flex-direction: column;
    box-shadow: 8px 0 60px rgba(0,0,0,0.8);
}
.sidebar.open { left: 0; }

.sidebar-header {
    padding: 30px 25px 25px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex; align-items: center; gap: 15px;
}
.sidebar-header img { height: 45px; border-radius: 8px; }
.sidebar-header h5 {
    font-family: 'Orbitron', sans-serif; color: #fff;
    font-weight: 800; font-size: 1rem; margin: 0; text-transform: uppercase;
}
.sidebar-header h5 span { color: #f23535; }

.close-sidebar {
    margin-left: auto; background: none; border: none; color: #666;
    font-size: 1.3rem; cursor: pointer; padding: 6px 10px;
    border-radius: 8px; transition: 0.3s; display: none;
}
.close-sidebar:hover { color: #fff; background: rgba(255,255,255,0.05); }

.sidebar-nav { flex: 1; padding: 20px 15px; overflow-y: auto; }
.nav-label {
    font-family: 'Orbitron', sans-serif; font-size: 0.6rem;
    text-transform: uppercase; letter-spacing: 3px;
    color: #555; padding: 15px 15px 8px; font-weight: 700;
}
.sidebar-nav a {
    display: flex; align-items: center; gap: 16px;
    padding: 14px 18px; color: #aaa; text-decoration: none;
    font-size: 0.9rem; font-weight: 500;
    border-radius: 12px; transition: all 0.3s ease;
    margin-bottom: 4px; position: relative;
}
.sidebar-nav a i {
    width: 22px; text-align: center; font-size: 1.1rem;
    color: #666; transition: 0.3s;
}
.sidebar-nav a:hover {
    background: rgba(242,53,53,0.08); color: #fff; transform: translateX(5px);
}
.sidebar-nav a:hover i { color: #f23535; }
.sidebar-nav a.active {
    background: linear-gradient(135deg, rgba(242,53,53,0.15), rgba(242,53,53,0.05));
    color: #fff; box-shadow: 0 8px 25px rgba(242,53,53,0.15);
}
.sidebar-nav a.active i { color: #f23535; }
.sidebar-nav a.active::before {
    content: ''; position: absolute; left: 0; top: 15%; bottom: 15%;
    width: 3px; background: #f23535; border-radius: 0 4px 4px 0;
}
.nav-badge {
    margin-left: auto; background: #f23535; color: #fff;
    font-family: 'Orbitron', sans-serif; font-size: 0.6rem;
    font-weight: 800; padding: 3px 10px; border-radius: 50px;
    min-width: 28px; text-align: center;
}

.sidebar-footer {
    padding: 20px 22px 30px;
    border-top: 1px solid rgba(255,255,255,0.05);
}
.user-mini {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px; padding: 0 5px;
}
.user-mini img {
    width: 42px; height: 42px; border-radius: 50%;
    border: 2px solid #f23535; object-fit: cover;
}
.user-mini .info .name { color: #fff; font-weight: 700; font-size: 0.9rem; }
.user-mini .info .role {
    color: #f23535; font-size: 0.7rem;
    font-family: 'Orbitron', sans-serif; letter-spacing: 1px; text-transform: uppercase;
}
.btn-logout {
    display: flex; align-items: center; justify-content: center; gap: 12px;
    width: 100%; padding: 14px;
    background: rgba(242,53,53,0.08);
    border: 1px solid rgba(242,53,53,0.25);
    color: #f23535; border-radius: 12px;
    font-family: 'Orbitron', sans-serif; font-weight: 700;
    font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase;
    cursor: pointer; transition: 0.3s;
}
.btn-logout:hover { background: rgba(242,53,53,0.2); transform: translateY(-2px); }

main {
    margin-left: 300px;
    padding: 40px 35px;
    max-width: 1500px;
    transition: margin-left 0.35s ease;
}

.welcome { margin-bottom: 50px; }
.welcome h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 900; margin-bottom: 10px;
    text-transform: uppercase; letter-spacing: -1px;
}
.welcome h1 span {
    background: linear-gradient(135deg, #f23535, #ff6b6b);
    -webkit-background-clip: text; background-clip: text; color: transparent;
}
.welcome p { color: #888; font-size: 1rem; max-width: 600px; }

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 25px; margin-bottom: 50px;
}
.stat-card {
    position: relative;
    background: linear-gradient(145deg, rgba(20,20,20,0.9), rgba(10,10,10,0.95));
    border-radius: 22px; padding: 35px 28px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.stat-card::before {
    content: ''; position: absolute; inset: -2px;
    border-radius: 22px; padding: 2px;
    background: linear-gradient(var(--angle, 0deg), transparent, #f23535, #ff6b6b, #f23535, transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor; mask-composite: exclude;
    animation: ledRotate 4s linear infinite;
    opacity: 0; transition: opacity 0.4s;
}
.stat-card:hover::before { opacity: 1; }
.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(242,53,53,0.2);
}
@keyframes ledRotate { 0% { --angle: 0deg; } 100% { --angle: 360deg; } }

.stat-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px;
}
.stat-icon {
    width: 55px; height: 55px; border-radius: 16px;
    background: linear-gradient(135deg, rgba(242,53,53,0.15), rgba(242,53,53,0.03));
    border: 1px solid rgba(242,53,53,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: #f23535; transition: 0.4s;
}
.stat-card:hover .stat-icon {
    background: linear-gradient(135deg, #f23535, #c91f2c);
    color: #fff; transform: rotate(-8deg) scale(1.1);
    box-shadow: 0 12px 30px rgba(242,53,53,0.4);
}
.stat-trend {
    font-family: 'Orbitron', sans-serif; font-size: 0.7rem;
    font-weight: 700; padding: 4px 10px; border-radius: 50px;
}
.stat-trend.up { background: rgba(76,175,80,0.15); color: #4CAF50; }
.stat-trend.neutral { background: rgba(255,255,255,0.05); color: #888; }

.stat-value {
    font-family: 'Orbitron', sans-serif; font-size: 2.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, #fff, #f23535);
    -webkit-background-clip: text; background-clip: text; color: transparent;
    line-height: 1; margin-bottom: 8px; letter-spacing: -2px;
}
.stat-label {
    font-size: 0.85rem; color: #888; font-weight: 500;
    text-transform: uppercase; letter-spacing: 1px;
    font-family: 'Orbitron', sans-serif;
}

.section-card {
    background: linear-gradient(145deg, rgba(20,20,20,0.8), rgba(10,10,10,0.9));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 22px; padding: 32px;
    margin-bottom: 30px; backdrop-filter: blur(20px);
}
.section-title {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 25px; padding-bottom: 18px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
    flex-wrap: wrap; gap: 12px;
}
.section-title h2 {
    font-family: 'Orbitron', sans-serif; font-size: 1rem;
    font-weight: 800; margin: 0; text-transform: uppercase;
    letter-spacing: 1px; color: #fff;
}
.section-title h2 i { color: #f23535; margin-right: 10px; }
.section-title a {
    color: #f23535; text-decoration: none; font-size: 0.8rem;
    font-weight: 600; transition: 0.3s;
    display: inline-flex; align-items: center; gap: 6px;
}
.section-title a:hover { color: #ff6b6b; gap: 10px; }

.table-responsive { overflow-x: auto; border-radius: 14px; }
table { width: 100%; border-collapse: collapse; min-width: 700px; }
thead { background: rgba(242,53,53,0.08); }
th {
    text-align: left; padding: 14px 18px; color: #aaa;
    font-family: 'Orbitron', sans-serif; font-size: 0.7rem;
    font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.5px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
}
td {
    padding: 14px 18px; color: #ccc;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    font-size: 0.9rem;
}
tbody tr { transition: 0.2s; }
tbody tr:hover td { background: rgba(242,53,53,0.04); }

.status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 14px; border-radius: 50px;
    font-family: 'Orbitron', sans-serif; font-size: 0.6rem;
    font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
}
.status-badge.pendente { background: rgba(255,152,0,0.15); color: #FF9800; border: 1px solid rgba(255,152,0,0.3); }
.status-badge.aprovado { background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3); }
.status-badge.cancelado,
.status-badge.rejeitado { background: rgba(244,67,54,0.15); color: #f44336; border: 1px solid rgba(244,67,54,0.3); }
.status-badge.pagar_no_local { background: rgba(37,211,102,0.15); color: #25d366; border: 1px solid rgba(37,211,102,0.3); }

.loading { text-align: center; padding: 40px; color: #555; }
.loading .spinner {
    display: inline-block; width: 30px; height: 30px;
    border: 3px solid rgba(242,53,53,0.15);
    border-top-color: #f23535; border-radius: 50%;
    animation: spin 0.8s linear infinite; margin-bottom: 12px;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 1024px) {
    .sidebar { left: -320px; }
    .sidebar.open { left: 0; }
    .close-sidebar { display: block; }
    main { margin-left: 0; padding: 30px 20px; }
    .menu-toggle { display: block; }
    .topbar { padding: 12px 20px; }
    .user-greeting { display: none; }
    .topbar .brand h1 { font-size: 1rem; }
    .badge-admin { display: none; }
}

@media (max-width: 500px) {
    .stat-value { font-size: 2rem; }
    .stat-card { padding: 25px 20px; }
    .section-card { padding: 22px 18px; }
    .welcome h1 { font-size: 1.6rem; }
}
</style>
</head>
<body>

<div class="bg-animated"></div>
<div class="bg-grid"></div>

<header class="topbar">
    <div style="display: flex; align-items: center; gap: 12px;">
        <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
        <div class="brand">
            <img src="../../img/logoLOJA.png" alt="DWS">
            <h1>DWS <span>Admin</span></h1>
            <span class="badge-admin"><i class="fas fa-shield-alt"></i> ADMIN</span>
        </div>
    </div>

    <div class="user-area">
        <div class="user-greeting">
            <strong><?= $nomeAdmin ?></strong>
            <span class="status"><i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i>Online</span>
        </div>
        <img src="../../img/icon.user2.png" class="user-avatar" id="userIcon" alt="User">
    </div>
</header>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="../../img/logoLOJA.png" alt="DWS">
        <h5>DWS <span>Admin</span></h5>
        <button class="close-sidebar" id="closeSidebar" aria-label="Fechar menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Navegação</div>

        <a href="principalFUN.php" class="active">
            <i class="fas fa-home"></i>
            <span>Início</span>
        </a>

        <a href="../../PHP/Servico/GerenciarServico.php">
            <i class="fas fa-clipboard-list"></i>
            <span>Pedidos</span>
            <span class="nav-badge" id="servicosBadge">0</span>
        </a>

        <a href="../../PHP/Clientes/ListarCliente.php">
            <i class="fas fa-users"></i>
            <span>Clientes</span>
            <span class="nav-badge" id="clientesBadge">0</span>
        </a>

        <a href="../../PHP/ADM/GerenciarGaleria.php">
            <i class="fas fa-images"></i>
            <span>Galeria</span>
        </a>

        <div class="nav-label" style="margin-top: 12px;">Gestão</div>

        <a href="../../PHP/ADM/GerenciarPrecos.php">
            <i class="fas fa-dollar-sign"></i>
            <span>Preços</span>
        </a>

        <a href="../../PHP/Servico/GerenciarServico.php">
            <i class="fas fa-calendar-check"></i>
            <span>Agendamentos</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-mini">
            <img src="../../img/icon.user2.png" alt="Avatar" id="sidebarUserImg">
            <div class="info">
                <div class="name"><?= $nomeAdmin ?></div>
                <div class="role"><i class="fas fa-crown" style="font-size: 0.6rem; margin-right: 4px;"></i>Super Admin</div>
            </div>
        </div>
        <button class="btn-logout" id="sidebarLogout">
            <i class="fas fa-sign-out-alt"></i> Sair do painel
        </button>
    </div>
</aside>

<main>
    <div class="welcome">
        <h1>BEM-VINDO, <span><?= strtoupper($nomeAdmin) ?></span></h1>
        <p>Gerencie serviços, clientes e agendamentos com total controle.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fas fa-car-side"></i></div>
                <div class="stat-trend up" id="trendMes">+0%</div>
            </div>
            <div class="stat-value" id="statServicos">0</div>
            <div class="stat-label">Total de Serviços</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-trend neutral">ATIVOS</div>
            </div>
            <div class="stat-value" id="statClientes">0</div>
            <div class="stat-label">Clientes Cadastrados</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-trend neutral">MÊS</div>
            </div>
            <div class="stat-value" id="statServicosMes">0</div>
            <div class="stat-label">Serviços no Mês</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-trend up">R$</div>
            </div>
            <div class="stat-value" id="statFaturamento">0</div>
            <div class="stat-label">Faturamento Total</div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-title">
            <h2><i class="fas fa-clock"></i>Últimos Serviços</h2>
            <a href="../../PHP/Servico/GerenciarServico.php">
                Ver todos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Serviço</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="servicesBody">
                    <tr><td colspan="5"><div class="loading"><div class="spinner"></div><p>Carregando...</p></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-card">
        <div class="section-title">
            <h2><i class="fas fa-user-plus"></i>Clientes Recentes</h2>
            <a href="../../PHP/Clientes/ListarCliente.php">
                Ver todos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                    </tr>
                </thead>
                <tbody id="clientsBody">
                    <tr><td colspan="3"><div class="loading"><div class="spinner"></div><p>Carregando...</p></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
// ============================================================
// 🔥 PROTEÇÃO CONTRA O BOTÃO "VOLTAR"
// Se o navegador mostrar esta página do cache, força reload
// ============================================================
window.addEventListener('pageshow', function(e) {
    if (e.persisted) {
        window.location.reload();
    }
});

// Também verifica periodicamente se a sessão caiu
async function checarSessao() {
    try {
        const res = await fetch('../../PHP/ADM/verificar_sessao.php?t=' + Date.now(), {
            credentials: 'include', cache: 'no-store'
        });
        const data = await res.json();
        if (!data.logado) {
            window.location.replace('login.html?erro=timeout&t=' + Date.now());
        }
    } catch (e) {}
}
setInterval(checarSessao, 30000);
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) checarSessao();
});

// ============================================================
// SIDEBAR
// ============================================================
const DOM = {
    menuToggle: document.getElementById('menuToggle'),
    closeSidebar: document.getElementById('closeSidebar'),
    sidebar: document.getElementById('sidebar'),
    overlay: document.getElementById('sidebarOverlay'),
    logoutBtn: document.getElementById('sidebarLogout')
};

function openSidebar() {
    DOM.sidebar.classList.add('open');
    DOM.overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    DOM.sidebar.classList.remove('open');
    DOM.overlay.classList.remove('active');
    document.body.style.overflow = '';
}
function toggleSidebar(e) {
    e.stopPropagation();
    DOM.sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
}

if (DOM.menuToggle) DOM.menuToggle.addEventListener('click', toggleSidebar);
if (DOM.closeSidebar) DOM.closeSidebar.addEventListener('click', closeSidebar);
if (DOM.overlay) DOM.overlay.addEventListener('click', closeSidebar);

DOM.sidebar.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 1024) closeSidebar();
    });
});

// ============================================================
// LOGOUT
// ============================================================
if (DOM.logoutBtn) {
    DOM.logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Deseja realmente encerrar a sessão administrativa?')) {
            window.location.replace('../../PHP/ADM/logoutadm.php');
        }
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && DOM.sidebar.classList.contains('open')) closeSidebar();
});

// ============================================================
// ESTATÍSTICAS
// ============================================================
async function carregarEstatisticas() {
    try {
        const res = await fetch('../../PHP/ADM/dashboard_stats.php', {
            credentials: 'include', cache: 'no-store'
        });

        if (res.status === 401) {
            window.location.replace('login.html?erro=restrito');
            return;
        }

        const data = await res.json();
        if (data.success) {
            document.getElementById('statServicos').textContent = data.total_servicos || 0;
            document.getElementById('statClientes').textContent = data.total_clientes || 0;
            document.getElementById('statServicosMes').textContent = data.servicos_mes || 0;

            const fat = Number(data.faturamento_total || 0);
            document.getElementById('statFaturamento').textContent =
                fat.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const atual = data.servicos_mes || 0;
            const anterior = data.servicos_mes_anterior || 0;
            const trendEl = document.getElementById('trendMes');
            if (trendEl) {
                if (anterior === 0 && atual === 0) {
                    trendEl.textContent = '0%';
                    trendEl.className = 'stat-trend neutral';
                } else if (anterior === 0) {
                    trendEl.textContent = '+100%';
                    trendEl.className = 'stat-trend up';
                } else {
                    const diff = Math.round(((atual - anterior) / anterior) * 100);
                    trendEl.textContent = (diff >= 0 ? '+' : '') + diff + '%';
                    trendEl.className = 'stat-trend ' + (diff >= 0 ? 'up' : 'neutral');
                }
            }
        }
    } catch (e) {
        console.error('Erro stats:', e);
    }
}

// ============================================================
// DASHBOARD (serviços + clientes)
// ============================================================
function mostrarErro(tbodyId, colspan, mensagem) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    tbody.innerHTML = `
        <tr>
            <td colspan="${colspan}" style="text-align:center;color:#555;padding:30px 0;">
                <i class="fas fa-triangle-exclamation" style="font-size:1.5rem;display:block;margin-bottom:8px;color:#333;"></i>
                ${mensagem}
            </td>
        </tr>`;
}

function renderizarServicos(servicos) {
    const tbody = document.getElementById('servicesBody');
    if (!tbody) return;

    if (servicos && servicos.length > 0) {
        tbody.innerHTML = servicos.map(s => {
            const status = (s.serstatus_pagamento || 'pendente').toLowerCase();
            const label = status === 'pagar_no_local' ? 'Pagar no Local'
                        : status.charAt(0).toUpperCase() + status.slice(1);

            let valorExibir = s.servalor;
            if (typeof valorExibir === 'string' && valorExibir.includes(',')) {
                valorExibir = valorExibir;
            } else {
                valorExibir = parseFloat(valorExibir || 0).toFixed(2).replace('.', ',');
            }

            return `
                <tr>
                    <td>${s.clinome || '—'}</td>
                    <td>${(s.tipo_servico || '').charAt(0).toUpperCase() + (s.tipo_servico || '').slice(1)}</td>
                    <td>R$ ${valorExibir}</td>
                    <td>${s.serdata_servico ? new Date(s.serdata_servico.replace(' ', 'T')).toLocaleDateString('pt-BR') : '—'}</td>
                    <td><span class="status-badge ${status}">${label}</span></td>
                </tr>`;
        }).join('');
    } else {
        mostrarErro('servicesBody', 5, 'Nenhum serviço encontrado');
    }

    const badge = document.getElementById('servicosBadge');
    if (badge) badge.textContent = servicos ? servicos.length : 0;
}

function renderizarClientes(clientes) {
    const tbody = document.getElementById('clientsBody');
    if (!tbody) return;

    if (clientes && clientes.length > 0) {
        tbody.innerHTML = clientes.map(c => `
            <tr>
                <td>${c.clinome || '—'}</td>
                <td>${c.clitel || '—'}</td>
                <td>${c.cliendereco || '—'}</td>
            </tr>`).join('');
    } else {
        mostrarErro('clientsBody', 3, 'Nenhum cliente encontrado');
    }

    const badge = document.getElementById('clientesBadge');
    if (badge) badge.textContent = clientes ? clientes.length : 0;
}

async function carregarDashboard() {
    try {
        const resposta = await fetch('../../PHP/ADM/DadosPFun.php', {
            credentials: 'include',
            headers: { 'Accept': 'application/json' },
            cache: 'no-store'
        });

        if (resposta.status === 401) {
            window.location.replace('login.html?erro=restrito');
            return;
        }

        if (!resposta.ok) throw new Error('HTTP ' + resposta.status);

        const dados = await resposta.json();
        renderizarServicos(dados.servicos || []);
        renderizarClientes(dados.clientes || []);
    } catch (erro) {
        console.error('Erro dashboard:', erro);
        mostrarErro('servicesBody', 5, 'Erro ao carregar serviços');
        mostrarErro('clientsBody', 3, 'Erro ao carregar clientes');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    Promise.all([carregarEstatisticas(), carregarDashboard()]);
});
</script>

</body>
</html>