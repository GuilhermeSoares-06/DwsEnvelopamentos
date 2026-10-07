// =============================================
// page-transition.js — Animação de envelopamento
// VERSÃO CORRIGIDA: garante que o overlay NUNCA fica preso
// =============================================
(function () {
    'use strict';

    // Cria o overlay no body
    function criarOverlay() {
        // Se já existe, remove primeiro
        const existente = document.querySelector('.dws-pt-overlay');
        if (existente) existente.remove();

        const overlay = document.createElement('div');
        overlay.className = 'dws-pt-overlay';
        overlay.innerHTML = `
            <div class="dws-pt-faixa"></div>
            <div class="dws-pt-linha-corte"></div>
            <div class="dws-pt-grid"></div>
            <div class="dws-pt-carro">
                <div class="dws-pt-rastro"></div>
                <i class="fas fa-car-side"></i>
            </div>
            <div class="dws-pt-logo">
                <img src="../../img/logoLOJA.png" alt="DWS">
                <div class="dws-pt-texto">
                    CARREGANDO
                    <div class="dws-pt-pontos">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        return overlay;
    }

    // Remove o overlay com segurança
    function removerOverlay() {
        const overlay = document.querySelector('.dws-pt-overlay');
        if (overlay) {
            overlay.classList.remove('dws-pt-saindo', 'dws-pt-entrando');
            overlay.style.visibility = 'hidden';
            overlay.style.pointerEvents = 'none';
            overlay.style.opacity = '0';
            setTimeout(() => {
                if (overlay.parentNode) overlay.remove();
            }, 100);
        }
    }

    // Intercepta cliques em links internos para fazer a transição
    function interceptarLinks() {
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');

            // Ignora links externos, âncoras, target=_blank, mailto, tel, etc.
            if (!href) return;
            if (href.startsWith('http') && !href.includes(window.location.hostname)) return;
            if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (href.startsWith('javascript:')) return;
            if (link.target === '_blank') return;
            if (link.hasAttribute('download')) return;
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;

            // Mostra a animação e navega
            e.preventDefault();
            const overlay = criarOverlay();
            requestAnimationFrame(() => {
                overlay.classList.add('dws-pt-saindo');
                setTimeout(() => {
                    window.location.href = href;
                }, 700);
            });
        });
    }

    // Segurança EXTRA: remove overlay depois de 2s se algo der errado
    function segurancaOverlay() {
        // Se a página já carregou e ainda tem overlay preso, remove
        window.addEventListener('load', () => {
            setTimeout(() => {
                const overlay = document.querySelector('.dws-pt-overlay');
                if (overlay && overlay.classList.contains('dws-pt-saindo')) {
                    console.warn('[page-transition] Overlay preso detectado. Removendo...');
                    removerOverlay();
                }
            }, 2000);
        });

        // Verifica a cada 3s se há overlay preso
        setInterval(() => {
            const overlay = document.querySelector('.dws-pt-overlay');
            if (overlay && (overlay.style.visibility === 'visible' || overlay.classList.contains('dws-pt-saindo'))) {
                // Só remove se já passou tempo suficiente (indica que travou)
                const tempoPreso = parseInt(overlay.dataset.tempo || '0');
                if (tempoPreso > 3000) {
                    console.warn('[page-transition] Overlay travado. Removendo...');
                    removerOverlay();
                } else {
                    overlay.dataset.tempo = String(tempoPreso + 3000);
                }
            }
        }, 3000);
    }

    // Inicializa quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        // Garante que NÃO exista overlay preso ao carregar a página
        removerOverlay();

        interceptarLinks();
        segurancaOverlay();
    }

    // Expõe função global para remover overlay manualmente
    window.DWS_RemoverOverlay = removerOverlay;
})();