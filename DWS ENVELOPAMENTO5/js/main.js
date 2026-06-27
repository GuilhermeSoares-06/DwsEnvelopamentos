/**
 * main.js - JavaScript principal do site
 * Usado em: principal.html, sobre.html
 * Depende de: auth.js (carregado antes)
 */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Carrossel Bootstrap (principal.html) ----
    const carouselEl = document.getElementById('carouselExampleInterval');
    if (carouselEl) {
        new bootstrap.Carousel(carouselEl, {
            interval: 4000,
            ride: 'carousel',
            wrap: true,
            pause: 'hover'
        }).cycle();
    }

    // O menu de usuário é inicializado pelo auth.js automaticamente via DWSAuth.init()
    // Mas se auth.js não estiver disponível, inicializa o dropdown básico
    if (typeof DWSAuth === 'undefined') {
        const iconeUser = document.getElementById('userIcon');
        const menuUser  = document.getElementById('menuUser');
        if (iconeUser && menuUser) {
            iconeUser.addEventListener('click', e => {
                e.stopPropagation();
                menuUser.style.display = menuUser.style.display === 'flex' ? 'none' : 'flex';
            });
            document.addEventListener('click', e => {
                if (!iconeUser.contains(e.target) && !menuUser.contains(e.target)) {
                    menuUser.style.display = 'none';
                }
            });
        }
    }
});
