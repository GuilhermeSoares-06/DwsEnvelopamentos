// sobre.html — scripts da página (extraídos do HTML)
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
// EFEITO 3D NOS CARDS
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
// EFEITO PARALLAX NAS PARTÍCULAS
// =============================================
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    document.querySelectorAll('.particle').forEach((particle, index) => {
        const speed = 0.1 + (index * 0.05);
        particle.style.transform = `translateY(${scrolled * speed}px)`;
    });
});
