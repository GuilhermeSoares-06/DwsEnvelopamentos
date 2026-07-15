/**
 * main.js - JavaScript principal do site (cabeçalho/menu do usuário)
 * Usado em: principal.html, sobre.html, contato.html
 *
 * OBS: esse arquivo tinha sido sobrescrito com o conteúdo do agendamento.js
 * por engano (por isso o menu de usuário e o "Olá, Nome" pararam de
 * aparecer em principal.html/sobre.html). Reconstruído aqui só com o que é
 * de verdade compartilhado entre as páginas: carrossel (se existir) e o
 * menu do ícone de usuário com verificação de sessão.
 */

document.addEventListener('DOMContentLoaded', function() {

    // =============================================
    // INICIALIZAÇÃO DO CARROSSEL (só existe em principal.html)
    // =============================================
    var carouselElement = document.getElementById('carouselExampleInterval');
    if (carouselElement && typeof bootstrap !== 'undefined') {
        var carousel = new bootstrap.Carousel(carouselElement, {
            interval: 3000,
            ride: 'carousel',
            wrap: true,
            pause: 'hover'
        });
        carousel.cycle();
    }

    // =============================================
    // MENU DO USUÁRIO (todas as páginas que usam main.js)
    // =============================================
    function inicializarMenuUser() {
        const iconeUser = document.getElementById("userIcon");
        const menuUser = document.getElementById("menuUser");

        if (iconeUser && menuUser) {
            iconeUser.addEventListener("click", function(e) {
                e.stopPropagation();
                menuUser.style.display = menuUser.style.display === "flex" ? "none" : "flex";
            });

            document.addEventListener("click", function(e) {
                if (!iconeUser.contains(e.target) && !menuUser.contains(e.target)) {
                    menuUser.style.display = "none";
                }
            });
        }
    }

    inicializarMenuUser();

    // =============================================
    // SESSÃO DO CLIENTE (todas as páginas)
    // Verifica no servidor (sessao_cliente.php) se existe um cliente logado.
    // Se sim, troca os links "Login / Cadastro" do menu do usuário por
    // "Olá, Nome" + "Sair".
    // =============================================
    function verificarSessaoCliente() {
        const menuUser = document.getElementById("menuUser");
        if (!menuUser) return;

        fetch('../../PHP/Usuarios/sessao_cliente.php')
            .then(res => res.json())
            .then(dados => {
                if (dados.logado) {
                    menuUser.innerHTML = `
                        <a href="#" style="pointer-events:none;font-weight:bold;">Olá, ${dados.nome}</a>
                        <a href="../../PHP/Usuarios/LogoutCliente.php">Sair</a>
                    `;
                }
            })
            .catch(() => {
                // Se o endpoint falhar, mantém os links padrão de Login/Cadastro.
            });
    }

    verificarSessaoCliente();

    // =============================================
    // FOTO DE PERFIL SALVA LOCALMENTE (se existir)
    // =============================================
    const userIcon = document.getElementById("userIcon");
    const fotoSalva = localStorage.getItem("fotoPerfilDWS");
    if (userIcon && fotoSalva) {
        userIcon.src = fotoSalva;
    }

});