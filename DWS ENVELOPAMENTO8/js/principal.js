// principal.html — scripts da página (extraídos do HTML)
// =============================================
// SEGURANÇA: Remove overlay travado do page-transition
// =============================================
(function() {
    function limparOverlay() {
        const overlay = document.querySelector('.dws-pt-overlay');
        if (overlay) {
            overlay.classList.remove('dws-pt-saindo', 'dws-pt-entrando');
            overlay.style.visibility = 'hidden';
            overlay.style.pointerEvents = 'none';
            overlay.style.opacity = '0';
        }
    }
    limparOverlay();
    setTimeout(limparOverlay, 1000);
    setTimeout(limparOverlay, 2000);
})();

// =============================================
// SCROLL SUAVE PARA ÂNCORAS
// =============================================
document.querySelectorAll('a[href^="#"]').forEach(function(link){
    link.addEventListener('click', function(e){
        var id = this.getAttribute('href');
        if (id === '#' || id.length < 2) return;
        var alvo = document.querySelector(id);
        if (!alvo) return;
        e.preventDefault();
        var offsetHeader = 90;
        var topo = alvo.getBoundingClientRect().top + window.pageYOffset - offsetHeader;
        window.scrollTo({ top: topo, behavior: 'smooth' });
    });
});

// =============================================
// REVEAL ANIMATIONS
// =============================================
const revealElements = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
}, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });
revealElements.forEach(el => revealObserver.observe(el));

// =============================================
// ESTATÍSTICAS
// =============================================
async function carregarEstatisticas() {
    try {
        const res = await fetch('../../PHP/Clientes/estatisticas.php', { credentials: 'include', cache: 'no-store' });
        const data = await res.json();
        if (data.status === 'sucesso' && data.estatisticas) {
            const stats = data.estatisticas;
            const statVeiculos = document.getElementById('statVeiculos');
            const statAnos = document.getElementById('statAnos');
            const statClientes = document.getElementById('statClientes');
            const statSatisfacao = document.getElementById('statSatisfacao');
            if (statVeiculos) statVeiculos.dataset.target = stats.veiculos_transformados;
            if (statAnos) statAnos.dataset.target = stats.anos_experiencia;
            if (statClientes) statClientes.dataset.target = stats.clientes_satisfeitos;
            if (statSatisfacao) statSatisfacao.dataset.target = stats.percentual_satisfacao;
            iniciarContadores();
        } else { usarValoresPadrao(); }
    } catch (e) { usarValoresPadrao(); }
}
function usarValoresPadrao() {
    const statVeiculos = document.getElementById('statVeiculos');
    const statAnos = document.getElementById('statAnos');
    const statClientes = document.getElementById('statClientes');
    const statSatisfacao = document.getElementById('statSatisfacao');
    if (statVeiculos) statVeiculos.dataset.target = 0;
    if (statAnos) statAnos.dataset.target = 5;
    if (statClientes) statClientes.dataset.target = 0;
    if (statSatisfacao) statSatisfacao.dataset.target = 98;
    iniciarContadores();
}
function iniciarContadores() {
    document.querySelectorAll('.stat-number[data-target]').forEach(counter => {
        const target = parseInt(counter.dataset.target) || 0;
        counter.dataset.animated = '';
        counter.textContent = '0';
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.dataset.animated) {
                    entry.target.dataset.animated = 'true';
                    const duration = 2000;
                    const startTime = performance.now();
                    function updateCounter(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        const easeOut = 1 - Math.pow(1 - progress, 3);
                        const current = Math.floor(easeOut * target);
                        entry.target.textContent = current.toLocaleString('pt-BR');
                        if (progress < 1) requestAnimationFrame(updateCounter);
                        else entry.target.textContent = target.toLocaleString('pt-BR');
                    }
                    requestAnimationFrame(updateCounter);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        observer.observe(counter);
    });
}
document.addEventListener('DOMContentLoaded', carregarEstatisticas);

// =============================================
// EFEITO 3D NOS CARDS DE SERVIÇO
// =============================================
document.querySelectorAll('.service-card-3d').forEach(card => {
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const rotateX = (y - rect.height / 2) / 20;
        const rotateY = (rect.width / 2 - x) / 20;
        card.style.transform = `translateY(-15px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; });
});

// =============================================
// PARALLAX NAS PARTÍCULAS
// =============================================
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    document.querySelectorAll('.particle').forEach((particle, index) => {
        const speed = 0.1 + (index * 0.05);
        particle.style.transform = `translateY(${scrolled * speed}px)`;
    });
});

// =============================================
// GALERIA — carrega e renderiza
// =============================================
let galeriaCompleta = [];
let filtroAtual = 'todos';

async function carregarGaleria() {
    const grid = document.getElementById('galeriaGrid');
    if (!grid) return;
    try {
        const res = await fetch('../../PHP/Clientes/galeria.php', { credentials: 'include', cache: 'no-store' });
        const data = await res.json();
        if (data.status === 'sucesso' && data.items && data.items.length > 0) {
            galeriaCompleta = data.items;
            renderizarGaleria();
        } else {
            grid.innerHTML = `<div class="empty-galeria"><i class="fas fa-images"></i><h3>Nenhum trabalho ainda</h3><p>Em breve adicionaremos nossos trabalhos mais recentes.</p></div>`;
        }
    } catch (e) {
        grid.innerHTML = `<div class="empty-galeria"><i class="fas fa-exclamation-triangle"></i><h3>Erro ao carregar</h3><p>Verifique sua conexão e tente novamente.</p></div>`;
    }
}

function renderizarGaleria() {
    const grid = document.getElementById('galeriaGrid');
    if (!grid) return;
    let items = galeriaCompleta;
    if (filtroAtual !== 'todos') items = items.filter(i => i.tipo === filtroAtual);
    if (items.length === 0) {
        grid.innerHTML = `<div class="empty-galeria"><i class="fas fa-search"></i><h3>Nenhum item nesta categoria</h3><p>Tente selecionar outra categoria.</p></div>`;
        return;
    }
    grid.innerHTML = items.map((item, index) => `
        <div class="galeria-card reveal" style="transition-delay: ${index * 0.05}s">
            <div class="galeria-card-img" style="background-image: url('../../${item.imagem}');"></div>
            <div class="galeria-card-overlay"></div>
            <div class="galeria-card-content">
                <span class="galeria-card-tipo">${traduzirTipo(item.tipo)}</span>
                <h3 class="galeria-card-titulo">${escapeHtml(item.titulo)}</h3>
                ${item.descricao ? `<p class="galeria-card-desc">${escapeHtml(item.descricao)}</p>` : ''}
                <div class="galeria-card-info">
                    <i class="fas fa-calendar-day"></i>
                    <span>${formatarData(item.criado_em)}</span>
                </div>
            </div>
        </div>
    `).join('');
    grid.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
    grid.querySelectorAll('.galeria-card').forEach((el, index) => {
        let angle = 0;
        const speed = 1.2 + (index % 5) * 0.15;
        function animate() {
            angle = (angle + speed) % 360;
            el.style.setProperty('--angle', angle + 'deg');
            requestAnimationFrame(animate);
        }
        animate();
    });
}

function traduzirTipo(tipo) {
    const map = { 'carro': 'Carro', 'moto': 'Moto', 'caminhao': 'Caminhão', 'aquatico': 'Aquático', 'mobilia': 'Mobília' };
    return map[tipo] || tipo;
}
function formatarData(dataStr) {
    if (!dataStr) return '—';
    try {
        const d = new Date(dataStr.replace(' ', 'T'));
        return d.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
    } catch (e) { return dataStr; }
}
function escapeHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

document.querySelectorAll('.filtro-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        filtroAtual = this.dataset.tipo;
        renderizarGaleria();
    });
});

const foto = localStorage.getItem('fotoPerfilDWS');
if (foto) {
    const userIcon = document.getElementById('userIcon');
    if (userIcon) userIcon.src = foto;
}

document.addEventListener('DOMContentLoaded', carregarGaleria);
