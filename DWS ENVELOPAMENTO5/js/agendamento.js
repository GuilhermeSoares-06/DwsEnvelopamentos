/**
 * agendamento.js - Página de agendamento (contato.html)
 * Depende de: auth.js, FullCalendar
 */

let calendario;
let dataSelecionada    = null;
let horarioSelecionado = null;
let servicoSelecionado = null;
let precoBaseAtual     = 0;

const SERVICOS = {
    carro:    { nome: 'Carro',     precoBase: 800 },
    moto:     { nome: 'Moto',      precoBase: 500 },
    caminhao: { nome: 'Caminhão',  precoBase: 2500 },
    aquatico: { nome: 'Aquático',  precoBase: 1800 },
    mobilia:  { nome: 'Mobília',   precoBase: 300 },
};

const HORARIOS = ['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00','17:00'];

// Horários ocupados carregados do BD
let horariosOcupados = [];  // [{ data:'YYYY-MM-DD', horario:'HH:MM' }, ...]

// ---- Carrega horários ocupados do servidor ----
async function carregarHorariosOcupados() {
    try {
        const res  = await fetch('../../PHP/Servico/horarios_ocupados.php');
        const data = await res.json();
        if (data.status === 'sucesso') horariosOcupados = data.horarios;
    } catch (_) {
        // fallback silencioso — sem conexão, permite agendar (validação no servidor)
    }
}

function isHorarioOcupado(data, horario) {
    return horariosOcupados.some(h => h.data === data && h.horario === horario);
}

// ---- Calendário ----
function inicializarCalendario() {
    const el = document.getElementById('calendario');
    if (!el) return;

    calendario = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth',
        },
        validRange: nowDate => ({ start: nowDate }),

        dateClick(info) {
            const sel  = new Date(info.dateStr);
            const hoje = new Date(); hoje.setHours(0,0,0,0);

            if (sel < hoje) {
                showToast('⚠️ Não é possível agendar em datas passadas!', true);
                return;
            }

            limparDestaques();
            setTimeout(() => destacarDia(info.dateStr), 80);

            dataSelecionada    = info.dateStr;
            horarioSelecionado = null;
            document.getElementById('horarioSelecionado').innerText = 'Nenhum';
            atualizarHorarios();
            atualizarResumo();
            showToast(`📅 Data: ${formatarData(dataSelecionada)}`);
        },

        datesSet() { limparDestaques(); },
    });

    calendario.render();
}

function destacarDia(dateStr) {
    const [, , d] = dateStr.split('-').map(Number);
    document.querySelectorAll('.fc-daygrid-day').forEach(el => {
        if (el.classList.contains('fc-day-other')) return;
        const n = el.querySelector('.fc-daygrid-day-number');
        if (n && parseInt(n.innerText.trim()) === d) {
            el.style.cssText += 'background:rgba(242,53,53,.22);border:2px solid #f23535;border-radius:8px;box-shadow:0 0 18px rgba(242,53,53,.28)';
            n.style.cssText  += 'color:#f23535;font-weight:800;font-size:1.15rem';
        }
    });
}

function limparDestaques() {
    document.querySelectorAll('.fc-daygrid-day').forEach(el => {
        el.style.background = '';
        el.style.border     = '';
        el.style.boxShadow  = '';
    });
    document.querySelectorAll('.fc-daygrid-day-number').forEach(el => {
        el.style.color      = '';
        el.style.fontWeight = '';
        el.style.fontSize   = '';
    });
}

// ---- Horários ----
function atualizarHorarios() {
    const container = document.getElementById('horariosList');
    if (!dataSelecionada) {
        container.innerHTML = '<div class="text-center text-white p-4">📅 Selecione uma data para ver os horários</div>';
        return;
    }
    container.innerHTML = HORARIOS.map(h => {
        const ocupado   = isHorarioOcupado(dataSelecionada, h);
        const sel       = horarioSelecionado === h;
        const cls       = ocupado ? 'ocupado' : sel ? 'selecionado' : '';
        const click     = !ocupado ? `onclick="selecionarHorario('${h}')"` : '';
        return `<div class="horario-btn ${cls}" ${click}>${h}${ocupado ? ' 🔴' : ''}</div>`;
    }).join('');
}

function selecionarHorario(h) {
    if (isHorarioOcupado(dataSelecionada, h)) {
        showToast('❌ Horário já ocupado! Escolha outro.', true);
        return;
    }
    horarioSelecionado = h;
    document.getElementById('horarioSelecionado').innerText = h;
    atualizarHorarios();
    showToast(`✅ Horário: ${h}`);
}

// ---- Orçamento ----
function calcularOrcamento() {
    if (!servicoSelecionado) {
        ['valorBase','valorAcabamento','valorTotal'].forEach(id => {
            document.getElementById(id).innerText = id === 'valorAcabamento' ? '+0%' : 'R$ 0,00';
        });
        return;
    }
    const fator   = parseFloat(document.getElementById('acabamento').value);
    const perc    = Math.round((fator - 1) * 100);
    const final   = precoBaseAtual * fator;
    document.getElementById('valorBase').innerText      = `R$ ${precoBaseAtual.toFixed(2)}`;
    document.getElementById('valorAcabamento').innerText = `+${perc}%`;
    document.getElementById('valorTotal').innerText      = `R$ ${final.toFixed(2)}`;
}

function atualizarResumo() {
    document.getElementById('dataSelecionada').innerText =
        dataSelecionada ? formatarData(dataSelecionada) : 'Nenhuma';
    document.getElementById('horarioSelecionado').innerText = horarioSelecionado || 'Nenhum';
    document.getElementById('servicoResumo').innerText =
        servicoSelecionado ? SERVICOS[servicoSelecionado].nome : 'Nenhum';
    calcularOrcamento();
}

// ---- Confirmar agendamento ----
async function confirmarAgendamento() {
    const nome      = document.getElementById('nome').value.trim();
    const telefone  = document.getElementById('telefone').value.trim();
    const email     = document.getElementById('email').value.trim();
    const veiculo   = document.getElementById('veiculo').value.trim();
    const descricao = document.getElementById('descricao').value.trim();
    const acabamento= document.getElementById('acabamento').value;
    const valorBase = document.getElementById('valorBase').innerText.replace('R$ ','').replace(',','.');
    const valorTotal= document.getElementById('valorTotal').innerText.replace('R$ ','').replace(',','.');

    const checks = [
        [!dataSelecionada,    '📅 Selecione uma data!'],
        [!horarioSelecionado, '⏰ Selecione um horário!'],
        [!servicoSelecionado, '🔧 Selecione um tipo de serviço!'],
        [!nome,               '👤 Informe seu nome!'],
        [!telefone,           '📞 Informe seu telefone!'],
        [!veiculo,            '🚗 Informe o veículo/mobília!'],
        [!descricao,          '📝 Descreva o serviço!'],
    ];
    for (const [cond, msg] of checks) {
        if (cond) { showToast(msg, true); return; }
    }

    if (isHorarioOcupado(dataSelecionada, horarioSelecionado)) {
        showToast('⚠️ Este horário acaba de ser ocupado! Escolha outro.', true);
        horarioSelecionado = null;
        atualizarHorarios();
        atualizarResumo();
        return;
    }

    const btn = document.getElementById('btnAgendar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    const fd = new FormData();
    fd.append('nome',                nome);
    fd.append('telefone',            telefone);
    fd.append('email',               email);
    fd.append('veiculo',             veiculo);
    fd.append('descricao',           descricao);
    fd.append('tipo_servico',        servicoSelecionado);
    fd.append('acabamento',          acabamento);
    fd.append('data_agendamento',    dataSelecionada);
    fd.append('horario_agendamento', horarioSelecionado);
    fd.append('valor_total',         valorTotal);
    fd.append('valor_base',          valorBase);

    // Envia token se logado
    const headers = typeof DWSAuth !== 'undefined' ? DWSAuth.authHeaders() : {};

    try {
        const res  = await fetch('../../PHP/Servico/agendar.php', {
            method: 'POST', body: fd, headers
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        if (data.status === 'sucesso') {
            showToast('✅ ' + data.mensagem);
            if (data.whatsapp_url) window.open(data.whatsapp_url, '_blank');
            // Marca horário como ocupado localmente
            horariosOcupados.push({ data: dataSelecionada, horario: horarioSelecionado });
            horarioSelecionado = null;
            atualizarHorarios();
            atualizarResumo();
        } else {
            showToast('❌ ' + data.mensagem, true);
            // Recarrega horários ocupados
            await carregarHorariosOcupados();
            atualizarHorarios();
        }
    } catch (err) {
        showToast('❌ Erro de conexão. Tente novamente.', true);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Agendamento';
    }
}

// ---- Toast ----
function showToast(msg, isError = false) {
    const t  = document.getElementById('toastMsg');
    const tx = document.getElementById('toastText');
    if (!t || !tx) return;
    tx.innerText = msg;
    t.style.background = isError ? '#dc3545' : '#28a745';
    t.style.display    = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3500);
}

// ---- Cards de serviço ----
function inicializarCardsServico() {
    document.querySelectorAll('.servico-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.servico-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            servicoSelecionado = card.dataset.tipo;
            precoBaseAtual     = parseFloat(card.dataset.precoBase);
            atualizarResumo();
            showToast(`🔧 Serviço: ${SERVICOS[servicoSelecionado].nome}`);
        });
    });
}

// ---- Máscara telefone ----
function mascaraTelefone() {
    const el = document.getElementById('telefone');
    if (!el) return;
    el.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g,'').slice(0,11);
        if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})/,'($1) $2-$3');
        else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4,5})(\d{0,4})/,'($1) $2-$3');
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/,'($1) $2');
        e.target.value = v;
    });
}

// ---- Helper data ----
function formatarData(str) {
    const [a,m,d] = str.split('-');
    return `${d}/${m}/${a}`;
}

// ---- Init ----
document.addEventListener('DOMContentLoaded', async () => {
    await carregarHorariosOcupados();
    inicializarCalendario();
    inicializarCardsServico();
    mascaraTelefone();

    // Select de acabamento
    const sel = document.getElementById('acabamento');
    if (sel) sel.addEventListener('change', calcularOrcamento);

    // Preenche nome/telefone se usuário logado
    if (typeof DWSAuth !== 'undefined' && DWSAuth.isLoggedIn()) {
        const user = DWSAuth.getUser();
        if (user) {
            const nomeEl = document.getElementById('nome');
            if (nomeEl && !nomeEl.value) nomeEl.value = user.nome;
        }
    }

    const btnAgendar = document.getElementById('btnAgendar');
    if (btnAgendar) btnAgendar.addEventListener('click', confirmarAgendamento);
});
