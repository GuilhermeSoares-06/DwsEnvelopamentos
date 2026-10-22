<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="../../js/guard-admin.js"></script>
<title>Painel Admin - DWS</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Space Grotesk',sans-serif;background:#050505;color:#fff;min-height:100vh;
  background-image:radial-gradient(ellipse at top left,rgba(242,53,53,.08),transparent 50%),radial-gradient(ellipse at bottom right,rgba(242,53,53,.05),transparent 50%);background-attachment:fixed}
a{text-decoration:none;color:inherit}
.sidebar{position:fixed;inset:0 auto 0 0;width:260px;background:linear-gradient(180deg,#141414,#0a0a0a);border-right:1px solid rgba(255,255,255,.06);padding:25px 18px;z-index:100;display:flex;flex-direction:column;transition:transform .3s}
.sidebar .brand{font-family:'Orbitron',sans-serif;font-weight:900;font-size:1.1rem;margin-bottom:6px}
.sidebar .brand i{color:#f23535;margin-right:8px}
.sidebar .who{color:#888;font-size:.8rem;margin-bottom:25px}
.sidebar .who span{color:#fff;font-weight:600}
.sidebar nav{display:flex;flex-direction:column;gap:6px;flex:1}
.sidebar nav a{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;color:#aaa;font-size:.9rem;font-weight:500;transition:.25s}
.sidebar nav a i{width:18px;text-align:center}
.sidebar nav a:hover,.sidebar nav a.active{background:rgba(242,53,53,.1);color:#f23535}
.sidebar .sair{margin-top:10px;border:1px solid rgba(242,53,53,.3);color:#f23535;justify-content:center}
.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:90}
.overlay.active{display:block}
.main{margin-left:260px;padding:30px 28px;max-width:1300px}
.top{display:flex;align-items:center;gap:14px;margin-bottom:28px}
.menu-toggle{display:none;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#fff;width:42px;height:42px;border-radius:12px;font-size:1.1rem;cursor:pointer}
.top h1{font-family:'Orbitron',sans-serif;font-size:1.5rem;font-weight:900}
.top h1 span{background:linear-gradient(135deg,#fff,#f23535);-webkit-background-clip:text;background-clip:text;color:transparent}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin-bottom:28px}
.card{background:linear-gradient(145deg,rgba(25,25,25,.9),rgba(12,12,12,.95));border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:20px}
.card .lbl{color:#888;font-size:.75rem;text-transform:uppercase;letter-spacing:1px;display:flex;align-items:center;gap:8px;margin-bottom:10px}
.card .lbl i{color:#f23535}
.card .val{font-family:'Orbitron',sans-serif;font-size:1.5rem;font-weight:700}
.card .sub{color:#666;font-size:.75rem;margin-top:6px}
.up{color:#4CAF50}.down{color:#f23535}
.grid2{display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:18px}
.box{background:linear-gradient(145deg,rgba(25,25,25,.9),rgba(12,12,12,.95));border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:22px;overflow:hidden}
.box h2{font-size:1rem;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.box h2 i{color:#f23535}
.tw{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:340px}
th{text-align:left;color:#888;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;padding:8px 10px;border-bottom:1px solid rgba(242,53,53,.15)}
td{padding:11px 10px;color:#ccc;font-size:.85rem;border-bottom:1px solid rgba(255,255,255,.04)}
.badge{display:inline-block;padding:3px 10px;border-radius:50px;font-size:.7rem;font-weight:600;border:1px solid}
.b-ok{color:#4CAF50;border-color:rgba(76,175,80,.4);background:rgba(76,175,80,.1)}
.b-wait{color:#FF9800;border-color:rgba(255,152,0,.4);background:rgba(255,152,0,.1)}
.b-off{color:#888;border-color:rgba(255,255,255,.15)}
.vazio{color:#666;text-align:center;padding:25px 0;font-size:.85rem}
@media(max-width:900px){
  .sidebar{transform:translateX(-100%)}.sidebar.open{transform:none}
  .main{margin-left:0;padding:20px 14px}.menu-toggle{display:block}
  .grid2{grid-template-columns:1fr}
}
</style>
</head>
<body>

<div class="overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="brand"><i class="fas fa-crown"></i>DWS ADMIN</div>
    <div class="who">Logado como <span id="sidebarName">...</span></div>
    <nav>
        <a href="principalFUN.php" class="active"><i class="fas fa-chart-line"></i> Painel</a>
        <a href="../../PHP/ADM/ListarPedidos.php"><i class="fas fa-clipboard-list"></i> Todos os Pedidos</a>
        <a href="../../PHP/Clientes/ListarCliente.php"><i class="fas fa-users"></i> Todos os Clientes</a>
        <a href="../Cliente/CadastroCliente.html?origem=adm"><i class="fas fa-user-plus"></i> Novo cliente</a>
        <a href="../../PHP/ADM/Faturamento.php"><i class="fas fa-dollar-sign"></i> Faturamento</a>
        <a href="../../PHP/ADM/GerenciarPrecos.php"><i class="fas fa-tags"></i> Preços</a>
        <a href="../../PHP/ADM/GerenciarGaleria.php"><i class="fas fa-images"></i> Galeria</a>
        <a href="../Cliente/principal.html"><i class="fas fa-globe"></i> Ver o site</a>
    </nav>
    <a href="../../PHP/ADM/logoutadm.php" class="sair" id="btnSairAdm" style="display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:12px;font-weight:600;font-size:.9rem">
        <i class="fas fa-sign-out-alt"></i> Sair
    </a>
</aside>

<main class="main">
    <div class="top">
        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu" type="button"><i class="fas fa-bars"></i></button>
        <h1>Olá, <span id="dashboardName">Administrador</span></h1>
    </div>

    <section class="cards">
        <div class="card"><div class="lbl"><i class="fas fa-users"></i> Clientes</div><div class="val" id="stClientes">-</div></div>
        <div class="card"><div class="lbl"><i class="fas fa-car-side"></i> Serviços</div><div class="val" id="stServicos">-</div>
            <div class="sub"><span id="stFinal">-</span> finalizados · <span id="stPend">-</span> pendentes</div></div>
        <div class="card"><div class="lbl"><i class="fas fa-calendar-day"></i> Faturamento do mês</div><div class="val" id="stMes">-</div>
            <div class="sub" id="stVar">&nbsp;</div></div>
        <div class="card"><div class="lbl"><i class="fas fa-check-circle"></i> Aprovado</div><div class="val" id="stAprov">-</div>
            <div class="sub">Pago online ou no local</div></div>
        <div class="card"><div class="lbl"><i class="fas fa-receipt"></i> Ticket médio</div><div class="val" id="stTicket">-</div></div>
    </section>

    <section class="grid2">
        <div class="box">
            <h2><i class="fas fa-clock-rotate-left"></i> Últimos serviços</h2>
            <div class="tw"><table><thead><tr><th>Cliente</th><th>Serviço</th><th>Valor</th><th>Status</th></tr></thead>
            <tbody id="tbServicos"><tr><td colspan="4" class="vazio">Carregando...</td></tr></tbody></table></div>
        </div>
        <div class="box">
            <h2><i class="fas fa-user-clock"></i> Últimos clientes</h2>
            <div class="tw"><table><thead><tr><th>Nome</th><th>Telefone</th><th>Endereço</th></tr></thead>
            <tbody id="tbClientes"><tr><td colspan="3" class="vazio">Carregando...</td></tr></tbody></table></div>
        </div>
    </section>
</main>

<script src="../../js/main.js"></script>
<script>
const $ = id => document.getElementById(id);
const brl = n => Number(n || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
const esc = t => { const d = document.createElement('div'); d.textContent = t ?? ''; return d.innerHTML; };

function valorBR(v) {
    if (typeof v === 'number') return v;
    let s = String(v ?? '').replace(/[R$\s]/g, '');
    if (s.includes(',') && s.includes('.')) s = s.lastIndexOf(',') > s.lastIndexOf('.') ? s.replace(/\./g, '').replace(',', '.') : s.replace(/,/g, '');
    else if (s.includes(',')) s = s.replace(',', '.');
    return parseFloat(s) || 0;
}
function badge(status) {
    const s = String(status || '').toLowerCase();
    if (s === 'finalizado') return '<span class="badge b-ok">Finalizado</span>';
    if (s === 'cancelado')  return '<span class="badge b-off">Cancelado</span>';
    return '<span class="badge b-wait">' + esc(status || 'Pendente') + '</span>';
}
async function chamar(url) {
    const r = await fetch(url, { credentials: 'include', cache: 'no-store' });
    if (r.status === 401) { window.location.replace('login.html'); throw new Error('401'); }
    return r.json();
}

async function carregarStats() {
    try {
        const d = await chamar('../../PHP/ADM/dashboard_stats.php');
        if (!d.success) return;
        $('stClientes').textContent = d.total_clientes;
        $('stServicos').textContent = d.total_servicos;
        $('stFinal').textContent = d.servicos_finalizados;
        $('stPend').textContent = d.servicos_pendentes;
        $('stMes').textContent = brl(d.faturamento_mes);
        $('stAprov').textContent = brl(d.faturamento_aprovado);
        $('stTicket').textContent = brl(d.ticket_medio);

        const ant = Number(d.faturamento_anterior || 0), atual = Number(d.faturamento_mes || 0);
        if (ant > 0) {
            const p = ((atual - ant) / ant) * 100;
            $('stVar').innerHTML = '<span class="' + (p >= 0 ? 'up' : 'down') + '">' + (p >= 0 ? '▲ ' : '▼ ') + Math.abs(p).toFixed(1) + '%</span> vs mês anterior';
        } else {
            $('stVar').textContent = 'Mês anterior: ' + brl(ant);
        }
    } catch (e) { console.error(e); }
}

async function carregarListas() {
    try {
        const d = await chamar('../../PHP/ADM/DadosPFun.php');
        if (!d.success) throw new Error(d.erro);

        $('tbServicos').innerHTML = d.servicos.length ? d.servicos.map(s =>
            '<tr><td>' + esc(s.clinome) + '</td><td>' + esc(s.tipo_servico) + '</td><td>' + brl(valorBR(s.servalor)) +
            '</td><td>' + badge(s.serstatus_servico) + '</td></tr>').join('')
            : '<tr><td colspan="4" class="vazio">Nenhum serviço ainda.</td></tr>';

        $('tbClientes').innerHTML = d.clientes.length ? d.clientes.map(c =>
            '<tr><td>' + esc(c.clinome) + '</td><td>' + esc(c.clitel) + '</td><td>' + esc(c.cliendereco) + '</td></tr>').join('')
            : '<tr><td colspan="3" class="vazio">Nenhum cliente ainda.</td></tr>';
    } catch (e) {
        console.error(e);
        $('tbServicos').innerHTML = $('tbClientes').innerHTML = '<tr><td colspan="4" class="vazio">Erro ao carregar.</td></tr>';
    }
}

$('btnSairAdm').addEventListener('click', e => {
    if (!confirm('Deseja realmente sair?')) e.preventDefault();
});

carregarStats();
carregarListas();
</script>
</body>
</html>