<?php
// =============================================
// Faturamento.php - Painel de faturamento
// =============================================
require_once __DIR__ . '/../Banco/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../telas/ADM/login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faturamento — DWS Admin</title>
<link rel="icon" type="image/png" href="../../img/logoabas.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Space Grotesk', sans-serif;
    background: #050505;
    color: #fff;
    min-height: 100vh;
    padding: 30px 20px;
    background-image:
        radial-gradient(ellipse at 15% 15%, rgba(242, 53, 53, 0.08) 0%, transparent 45%),
        radial-gradient(ellipse at 85% 85%, rgba(242, 53, 53, 0.05) 0%, transparent 45%);
    background-attachment: fixed;
}

.container { max-width: 1400px; margin: 0 auto; }

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -0.5px;
    margin: 0;
}
.page-header h1 i { color: #f23535; margin-right: 12px; }
.page-header h1 span {
    background: linear-gradient(135deg, #fff, #f23535);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.btn-voltar {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: rgba(255,255,255,0.05);
    color: #aaa;
    text-decoration: none;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: 0.3s;
    border: 1px solid rgba(255,255,255,0.05);
}
.btn-voltar:hover {
    background: rgba(242,53,53,0.1);
    color: #f23535;
    transform: translateX(-5px);
}

/* CARDS RESUMO */
.resumo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 35px;
}

.resumo-card {
    background: linear-gradient(145deg, rgba(20,20,20,0.9), rgba(10,10,10,0.95));
    border-radius: 20px;
    padding: 25px;
    border: 1px solid rgba(255,255,255,0.06);
    transition: 0.3s;
    position: relative;
    overflow: hidden;
}
.resumo-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, #f23535, transparent);
    transform: scaleX(0);
    transition: transform 0.4s;
}
.resumo-card:hover::before { transform: scaleX(1); }
.resumo-card:hover {
    transform: translateY(-5px);
    border-color: rgba(242,53,53,0.3);
    box-shadow: 0 20px 40px rgba(242,53,53,0.1);
}

.resumo-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}
.resumo-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(242,53,53,0.15), rgba(242,53,53,0.03));
    border: 1px solid rgba(242,53,53,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f23535;
    font-size: 1.2rem;
}
.resumo-label {
    color: #888;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 600;
    margin-bottom: 8px;
}
.resumo-valor {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.8rem;
    font-weight: 900;
    background: linear-gradient(135deg, #fff, #f23535);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: -1px;
}

/* GRÁFICO */
.chart-card {
    background: linear-gradient(145deg, rgba(20,20,20,0.9), rgba(10,10,10,0.95));
    border-radius: 20px;
    padding: 30px;
    border: 1px solid rgba(255,255,255,0.06);
    margin-bottom: 35px;
}
.chart-card h3 {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
}
.chart-card h3 i { color: #f23535; }
.chart-container {
    position: relative;
    height: 320px;
}

/* TABELA */
.table-card {
    background: linear-gradient(145deg, rgba(20,20,20,0.9), rgba(10,10,10,0.95));
    border-radius: 20px;
    padding: 30px;
    border: 1px solid rgba(255,255,255,0.06);
}
.table-card h3 {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
}
.table-card h3 i { color: #f23535; }

.table-responsive { overflow-x: auto; border-radius: 12px; }

table { width: 100%; border-collapse: collapse; min-width: 700px; }
thead { background: rgba(242,53,53,0.08); }
th {
    text-align: left;
    padding: 14px 18px;
    color: #aaa;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border-bottom: 1px solid rgba(242,53,53,0.15);
}
td {
    padding: 14px 18px;
    color: #ccc;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    font-size: 0.9rem;
}
tbody tr { transition: 0.2s; }
tbody tr:hover td { background: rgba(242,53,53,0.04); }

.mes-cell {
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    color: #fff;
}
.faturamento-cell { color: #4CAF50; font-weight: 700; }
.aprovado-cell { color: #25d366; font-weight: 600; }
.data-cell { color: #666; font-size: 0.85rem; }

.loading {
    text-align: center;
    padding: 60px;
    color: #555;
}
.loading i {
    font-size: 2rem;
    color: #f23535;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
    display: block;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* RESPONSIVO */
@media (max-width: 768px) {
    body { padding: 20px 12px; }
    .page-header h1 { font-size: 1.4rem; }
    .resumo-valor { font-size: 1.4rem; }
    .chart-card, .table-card { padding: 20px; }
    .chart-container { height: 250px; }
}
</style>
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1><i class="fas fa-chart-line"></i> <span>Faturamento</span></h1>
        <a href="../../telas/ADM/principalFUN.html" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- RESUMO -->
    <div class="resumo-grid">
        <div class="resumo-card">
            <div class="resumo-header">
                <div class="resumo-icon"><i class="fas fa-calendar-day"></i></div>
            </div>
            <div class="resumo-label">Faturamento do Mês Atual</div>
            <div class="resumo-valor" id="resumoMesAtual">R$ 0,00</div>
        </div>

        <div class="resumo-card">
            <div class="resumo-header">
                <div class="resumo-icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
            <div class="resumo-label">Mês Anterior</div>
            <div class="resumo-valor" id="resumoMesAnterior">R$ 0,00</div>
        </div>

        <div class="resumo-card">
            <div class="resumo-header">
                <div class="resumo-icon"><i class="fas fa-coins"></i></div>
            </div>
            <div class="resumo-label">Total Histórico</div>
            <div class="resumo-valor" id="resumoTotal">R$ 0,00</div>
        </div>

        <div class="resumo-card">
            <div class="resumo-header">
                <div class="resumo-icon"><i class="fas fa-chart-bar"></i></div>
            </div>
            <div class="resumo-label">Média Mensal</div>
            <div class="resumo-valor" id="resumoMedia">R$ 0,00</div>
        </div>
    </div>

    <!-- GRÁFICO -->
    <div class="chart-card">
        <h3><i class="fas fa-chart-area"></i> Evolução Mensal (últimos 12 meses)</h3>
        <div class="chart-container">
            <canvas id="graficoFaturamento"></canvas>
        </div>
    </div>

    <!-- TABELA -->
    <div class="table-card">
        <h3><i class="fas fa-list"></i> Histórico Completo</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Mês / Ano</th>
                        <th>Total Serviços</th>
                        <th>Faturamento</th>
                        <th>Aprovado</th>
                        <th>Fechado em</th>
                    </tr>
                </thead>
                <tbody id="historicoBody">
                    <tr>
                        <td colspan="5">
                            <div class="loading">
                                <i class="fas fa-spinner"></i>
                                Carregando histórico...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
// =============================================
// CARREGA DADOS
// =============================================
async function carregarDados() {
    try {
        // Histórico
        const resHist = await fetch('historico_faturamento.php', {
            credentials: 'include', cache: 'no-store'
        });
        const dataHist = await resHist.json();

        // Estatísticas atuais
        const resStats = await fetch('dashboard_stats.php', {
            credentials: 'include', cache: 'no-store'
        });
        const dataStats = await resStats.json();

        if (dataHist.status === 'sucesso') {
            renderizarHistorico(dataHist.historico);
            renderizarResumo(dataHist.historico, dataStats);
            renderizarGrafico(dataHist.historico);
        }
    } catch (e) {
        console.error('Erro:', e);
        document.getElementById('historicoBody').innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center;color:#f44336;padding:40px;">
                    <i class="fas fa-exclamation-triangle" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
                    Erro ao carregar dados
                </td>
            </tr>
        `;
    }
}

// =============================================
// RENDERIZA RESUMO
// =============================================
function renderizarResumo(historico, stats) {
    const fmt = v => 'R$ ' + parseFloat(v || 0).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    document.getElementById('resumoMesAtual').textContent = fmt(stats.faturamento_total || 0);
    document.getElementById('resumoMesAnterior').textContent = fmt(stats.faturamento_anterior || 0);

    let total = 0;
    historico.forEach(h => {
        total += parseFloat(h.faturamento_total || 0);
    });
    document.getElementById('resumoTotal').textContent = fmt(total);

    const media = historico.length > 0 ? total / historico.length : 0;
    document.getElementById('resumoMedia').textContent = fmt(media);
}

// =============================================
// RENDERIZA HISTÓRICO
// =============================================
function renderizarHistorico(historico) {
    const tbody = document.getElementById('historicoBody');

    if (!historico || historico.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center;color:#555;padding:60px;">
                    <i class="fas fa-info-circle" style="font-size:2rem;display:block;margin-bottom:12px;opacity:0.4;"></i>
                    Nenhum mês fechado ainda.<br>
                    <small>Os registros aparecem automaticamente quando o mês virar.</small>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = historico.map(h => `
        <tr>
            <td class="mes-cell">${h.mes_ano}</td>
            <td>${h.total_servicos}</td>
            <td class="faturamento-cell">R$ ${parseFloat(h.faturamento_total).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</td>
            <td class="aprovado-cell">R$ ${parseFloat(h.faturamento_aprovado).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</td>
            <td class="data-cell">${new Date(h.fechado_em.replace(' ', 'T')).toLocaleDateString('pt-BR')}</td>
        </tr>
    `).join('');
}

// =============================================
// RENDERIZA GRÁFICO
// =============================================
function renderizarGrafico(historico) {
    if (!historico || historico.length === 0) return;

    // Pega últimos 12 meses (na ordem cronológica)
    const ultimos12 = historico.slice(0, 12).reverse();

    const labels = ultimos12.map(h => h.mes_ano);
    const dados = ultimos12.map(h => parseFloat(h.faturamento_total || 0));
    const dadosAprovados = ultimos12.map(h => parseFloat(h.faturamento_aprovado || 0));

    const ctx = document.getElementById('graficoFaturamento').getContext('2d');

    // Gradiente
    const gradiente = ctx.createLinearGradient(0, 0, 0, 320);
    gradiente.addColorStop(0, 'rgba(242, 53, 53, 0.4)');
    gradiente.addColorStop(1, 'rgba(242, 53, 53, 0.02)');

    const gradienteVerde = ctx.createLinearGradient(0, 0, 0, 320);
    gradienteVerde.addColorStop(0, 'rgba(37, 211, 102, 0.3)');
    gradienteVerde.addColorStop(1, 'rgba(37, 211, 102, 0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Faturamento Total',
                    data: dados,
                    borderColor: '#f23535',
                    backgroundColor: gradiente,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f23535',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                },
                {
                    label: 'Aprovado',
                    data: dadosAprovados,
                    borderColor: '#25d366',
                    backgroundColor: gradienteVerde,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#25d366',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#aaa',
                        font: { family: 'Space Grotesk', size: 12, weight: '600' },
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(10, 10, 10, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#ccc',
                    borderColor: 'rgba(242, 53, 53, 0.5)',
                    borderWidth: 1,
                    padding: 15,
                    titleFont: { family: 'Orbitron', size: 13, weight: '700' },
                    bodyFont: { family: 'Space Grotesk', size: 12 },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: {
                        color: '#666',
                        font: { family: 'Space Grotesk', size: 11 },
                        callback: function(v) {
                            return 'R$ ' + v.toLocaleString('pt-BR', { maximumFractionDigits: 0 });
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#888',
                        font: { family: 'Orbitron', size: 10, weight: '600' }
                    }
                }
            }
        }
    });
}

// INICIALIZA
carregarDados();
</script>

</body>
</html>