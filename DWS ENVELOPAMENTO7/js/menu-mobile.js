// =============================================
// menu-mobile.js - Menu Hamburguer Global
// Funciona em qualquer página que tenha o header padrão
// =============================================

(function() {
    'use strict';

    console.log('🍔 menu-mobile.js carregado');

    function inicializarMenuMobile() {
        const menuToggle = document.getElementById('menuToggleGlobal');
        const nav = document.getElementById('navGlobal');

        if (!menuToggle || !nav) {
            console.warn('⚠️ menuToggleGlobal ou navGlobal não encontrados nesta página');
            return;
        }

        console.log('✅ Menu mobile inicializado');

        // Abre/fecha menu
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const estaAberto = nav.classList.contains('mobile-open');

            if (estaAberto) {
                fecharMenu();
            } else {
                abrirMenu();
            }
        });

        // Fecha ao clicar em um link
        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                fecharMenu();
            });
        });

        // Fecha ao clicar fora
        document.addEventListener('click', function(e) {
            if (!nav.classList.contains('mobile-open')) return;
            if (nav.contains(e.target)) return;
            if (menuToggle.contains(e.target)) return;
            fecharMenu();
        });

        // Fecha com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && nav.classList.contains('mobile-open')) {
                fecharMenu();
            }
        });

        // Fecha ao redimensionar para desktop
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768 && nav.classList.contains('mobile-open')) {
                    fecharMenu();
                }
            }, 200);
        });

        function abrirMenu() {
            nav.classList.add('mobile-open');
            menuToggle.innerHTML = '<i class="fas fa-times"></i>';
            menuToggle.setAttribute('aria-label', 'Fechar menu');
            document.body.style.overflow = 'hidden';
        }

        function fecharMenu() {
            nav.classList.remove('mobile-open');
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.setAttribute('aria-label', 'Abrir menu');
            document.body.style.overflow = '';
        }
    }

    // Aguarda o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarMenuMobile);
    } else {
        inicializarMenuMobile();
    }
})();