/**
 * main.js - JavaScript principal do site
 * Usado em: principal.html, sobre.html
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // =============================================
    // INICIALIZAÇÃO DO CARROSSEL (principal.html)
    // =============================================
    var carouselElement = document.getElementById('carouselExampleInterval');
    if (carouselElement) {
        var carousel = new bootstrap.Carousel(carouselElement, {
            interval: 3000,
            ride: 'carousel',
            wrap: true,
            pause: 'hover'
        });
        carousel.cycle();
    }

    // =============================================
    // MENU DO USUÁRIO (todas as páginas)
    // =============================================
    function inicializarMenuUser() {
        const iconeUser = document.getElementById("userIcon");
        const menuUser = document.getElementById("menuUser");

        if (iconeUser && menuUser) {
            // Abrir/fechar menu ao clicar no ícone
            iconeUser.addEventListener("click", function(e) {
                e.stopPropagation();
                menuUser.style.display = menuUser.style.display === "flex" ? "none" : "flex";
            });

            // Fecha o menu ao clicar fora
            document.addEventListener("click", function(e) {
                if (!iconeUser.contains(e.target) && !menuUser.contains(e.target)) {
                    menuUser.style.display = "none";
                }
            });
        }
    }

    inicializarMenuUser();

});