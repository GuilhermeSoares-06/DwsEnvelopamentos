// =============================================
// login-transition.js — Animação EXCLUSIVA de login
// Não conflita com page-transition.js
// Só roda quando a URL tem ?login=ok
// =============================================
(function() {
    'use strict';

    if (window.top !== window.self) return;

    // 🛡️ Só roda uma vez por página
    if (window.__dwsLoginTransitionInicializado) return;
    window.__dwsLoginTransitionInicializado = true;

    // 🛡️ Só roda se a URL tiver ?login=ok
    const params = new URLSearchParams(window.location.search);
    if (params.get('login') !== 'ok') return;

    // ============================================================
    // CAMINHO BASE
    // ============================================================
    function detectarBase() {
        const path = window.location.pathname.replace(/\\/g, '/');
        if (/\/(telas|PHP)\/[^/]+\/[^/]+$/.test(path)) return '../../';
        if (/\/(telas|PHP)\/[^/]+$/.test(path)) return '../';
        return './';
    }
    const BASE = detectarBase();
    const LOGO_URL = BASE + 'img/logoLOJA.png';

    const nomeCliente = params.get('nome') || 'Cliente';

    // ============================================================
    // 🔥 LIMPA CACHE DA SESSÃO — força revalidação em qualquer lugar
    // ============================================================
    try {
        sessionStorage.removeItem('dws_sessao_cliente');
        sessionStorage.removeItem('dws_login_transition_mostrada');
    } catch (e) {}

    if (window.DWS_Sessao && typeof window.DWS_Sessao.limpar === 'function') {
        try { window.DWS_Sessao.limpar(); } catch (e) {}
    }

    // ============================================================
    // BLOQUEIA SCROLL
    // ============================================================
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';

    // ============================================================
    // INJETA CSS
    // ============================================================
    function injetarCSS() {
        if (document.getElementById('dws-login-css')) return;

        const style = document.createElement('style');
        style.id = 'dws-login-css';
        style.textContent = `
            #dwsLoginTransition {
                position: fixed;
                inset: 0;
                z-index: 999999;
                background: linear-gradient(135deg, #f23535 0%, #c91f2c 50%, #a01824 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Orbitron', 'Inter', sans-serif;
                color: #fff;
                overflow: hidden;
                opacity: 1;
                transition: opacity 0.6s cubic-bezier(0.65, 0, 0.35, 1);
            }

            #dwsLoginTransition.dws-lt-saindo {
                opacity: 0;
                pointer-events: none;
            }

            #dwsLoginTransition::before {
                content: '';
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
                background-size: 40px 40px;
                opacity: 0;
                animation: dwsLtGrid 0.6s ease 0.1s forwards;
            }
            @keyframes dwsLtGrid {
                0% { opacity: 0; }
                100% { opacity: 1; }
            }

            #dwsLoginTransition::after {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 60%;
                height: 100%;
                background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.15) 50%,
                    transparent 100%);
                animation: dwsLtBrilho 1.8s cubic-bezier(0.65, 0, 0.35, 1) 0.3s infinite;
            }
            @keyframes dwsLtBrilho {
                0% { left: -100%; }
                100% { left: 150%; }
            }

            .dws-lt-content {
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 24px;
                text-align: center;
                padding: 20px;
                opacity: 0;
                animation: dwsLtContentIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.15s forwards;
            }
            @keyframes dwsLtContentIn {
                0% { opacity: 0; transform: translateY(30px) scale(0.9); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }

            .dws-lt-logo {
                height: 90px;
                width: auto;
                filter: drop-shadow(0 0 25px rgba(255, 255, 255, 0.7));
                animation: dwsLtLogoPulse 1.6s ease-in-out infinite;
            }
            @keyframes dwsLtLogoPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.08); }
            }

            .dws-lt-check {
                width: 110px;
                height: 110px;
                border-radius: 50%;
                background: linear-gradient(135deg, #4CAF50, #2e7d32);
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.6), 0 20px 50px rgba(0, 0, 0, 0.3);
                animation: dwsLtCheckPulse 1.6s ease-out infinite;
                margin: 8px 0;
            }
            @keyframes dwsLtCheckPulse {
                0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.6), 0 20px 50px rgba(0, 0, 0, 0.3); }
                70% { box-shadow: 0 0 0 25px rgba(255, 255, 255, 0), 0 20px 50px rgba(0, 0, 0, 0.3); }
                100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0), 0 20px 50px rgba(0, 0, 0, 0.3); }
            }
            .dws-lt-check i {
                font-size: 3.5rem;
                color: #fff;
                transform: scale(0) rotate(-45deg);
                animation: dwsLtCheckDraw 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.5s forwards;
            }
            @keyframes dwsLtCheckDraw {
                0% { transform: scale(0) rotate(-45deg); opacity: 0; }
                70% { transform: scale(1.2) rotate(0deg); opacity: 1; }
                100% { transform: scale(1) rotate(0deg); opacity: 1; }
            }

            .dws-lt-titulo {
                font-size: 0.75rem;
                font-weight: 600;
                letter-spacing: 6px;
                text-transform: uppercase;
                color: rgba(255, 255, 255, 0.8);
                animation: dwsLtFadeIn 0.6s ease 0.9s both;
            }
            .dws-lt-nome {
                font-size: clamp(1.6rem, 5vw, 2.4rem);
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 1px;
                text-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
                margin-top: -10px;
                animation: dwsLtFadeIn 0.6s ease 1.1s both;
            }
            .dws-lt-nome span {
                display: block;
                font-size: 0.55em;
                letter-spacing: 4px;
                color: rgba(255, 255, 255, 0.85);
                font-weight: 600;
                margin-top: 6px;
            }
            @keyframes dwsLtFadeIn {
                0% { opacity: 0; transform: translateY(15px); }
                100% { opacity: 1; transform: translateY(0); }
            }

            .dws-lt-barra {
                width: 220px;
                height: 3px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 3px;
                overflow: hidden;
                margin-top: 15px;
            }
            .dws-lt-barra span {
                display: block;
                height: 100%;
                width: 0%;
                background: #fff;
                box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
                animation: dwsLtBarra 2.2s cubic-bezier(0.65, 0, 0.35, 1) 0.3s forwards;
            }
            @keyframes dwsLtBarra {
                0% { width: 0%; }
                100% { width: 100%; }
            }

            .dws-lt-particula {
                position: absolute;
                width: 6px;
                height: 6px;
                background: rgba(255, 255, 255, 0.4);
                border-radius: 50%;
                pointer-events: none;
            }
            .dws-lt-particula:nth-child(1) { top: 20%; left: 15%; animation: dwsLtFloat 3s ease-in-out infinite; }
            .dws-lt-particula:nth-child(2) { top: 70%; left: 25%; animation: dwsLtFloat 4s ease-in-out infinite 0.5s; }
            .dws-lt-particula:nth-child(3) { top: 30%; right: 20%; animation: dwsLtFloat 3.5s ease-in-out infinite 1s; }
            .dws-lt-particula:nth-child(4) { bottom: 25%; right: 30%; animation: dwsLtFloat 4.5s ease-in-out infinite 0.3s; }
            .dws-lt-particula:nth-child(5) { top: 50%; left: 8%; animation: dwsLtFloat 3.2s ease-in-out infinite 0.8s; }

            @keyframes dwsLtFloat {
                0%, 100% { transform: translateY(0); opacity: 0.3; }
                50% { transform: translateY(-25px); opacity: 0.8; }
            }

            @media (max-width: 600px) {
                .dws-lt-logo { height: 65px; }
                .dws-lt-check { width: 85px; height: 85px; }
                .dws-lt-check i { font-size: 2.6rem; }
                .dws-lt-barra { width: 180px; }
            }

            @media (prefers-reduced-motion: reduce) {
                #dwsLoginTransition *,
                #dwsLoginTransition *::before,
                #dwsLoginTransition *::after {
                    animation-duration: 0.01ms !important;
                    transition-duration: 0.01ms !important;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // ============================================================
    // INJETA OVERLAY
    // ============================================================
    function injetarOverlay() {
        if (document.getElementById('dwsLoginTransition')) return;

        const overlay = document.createElement('div');
        overlay.id = 'dwsLoginTransition';
        overlay.setAttribute('aria-hidden', 'false');
        overlay.setAttribute('role', 'status');
        overlay.setAttribute('aria-label', 'Login realizado com sucesso');

        overlay.innerHTML = `
            <span class="dws-lt-particula"></span>
            <span class="dws-lt-particula"></span>
            <span class="dws-lt-particula"></span>
            <span class="dws-lt-particula"></span>
            <span class="dws-lt-particula"></span>

            <div class="dws-lt-content">
                <img src="${LOGO_URL}" alt="DWS Envelopamento" class="dws-lt-logo"
                     onerror="this.style.display='none'">

                <div class="dws-lt-check">
                    <i class="fas fa-check"></i>
                </div>

                <div class="dws-lt-titulo">LOGIN REALIZADO</div>
                <div class="dws-lt-nome">
                    ${escapeHtml(nomeCliente)}
                    <span>BEM-VINDO DE VOLTA</span>
                </div>

                <div class="dws-lt-barra">
                    <span></span>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);
    }

    function escapeHtml(texto) {
        const div = document.createElement('div');
        div.textContent = (texto === null || texto === undefined) ? '' : String(texto);
        return div.innerHTML;
    }

    // ============================================================
    // ENCERRA A ANIMAÇÃO E LIMPA A URL
    // ============================================================
    function encerrar() {
        const overlay = document.getElementById('dwsLoginTransition');
        if (!overlay) return;

        overlay.classList.add('dws-lt-saindo');

        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';

        setTimeout(() => {
            overlay.remove();

            const url = new URL(window.location.href);
            url.searchParams.delete('login');
            url.searchParams.delete('nome');
            url.searchParams.delete('t');
            window.history.replaceState({}, '', url.pathname + (url.search ? url.search : ''));

            // 🔥 Dispara um evento global pra outros scripts saberem
            //    que o login terminou e a sessão está pronta
            window.dispatchEvent(new CustomEvent('dws:login-completo'));
        }, 700);
    }

    // ============================================================
    // INICIALIZAÇÃO
    // ============================================================
    function init() {
        // Marca que já rodou nesta URL
        try {
            sessionStorage.setItem('dws_login_transition_mostrada', '1');
        } catch (e) {}

        injetarCSS();
        injetarOverlay();

        // Encerra depois de 2.8s
        setTimeout(encerrar, 2800);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();