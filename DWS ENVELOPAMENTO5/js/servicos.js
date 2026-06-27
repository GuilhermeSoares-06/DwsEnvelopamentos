/**
 * servicos.js - JavaScript da página de serviços
 * Usado em: servico.html
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // =============================================
    // MENU DO USUÁRIO
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

});

// Função para fazer pedido via WhatsApp
function fazerPedido() {
    let telefone = "5514996175617";
    let texto = `Olá! Gostaria de fazer um pedido:\nTenho interesse nos serviços da DWS Envelopamento.`;
    let url = "https://wa.me/" + telefone + "?text=" + encodeURIComponent(texto);
    window.open(url, "_blank");
}