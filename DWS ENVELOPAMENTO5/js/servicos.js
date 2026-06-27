/**
 * servicos.js - JavaScript da página de serviços (servico.html)
 * Depende de: auth.js
 */
document.addEventListener('DOMContentLoaded', function () {
    // Menu de usuário gerenciado pelo auth.js

    // Botão de fazer pedido via WhatsApp
    const btnPedido = document.querySelector('.btn-pedido');
    if (btnPedido && btnPedido.href === '#') {
        btnPedido.addEventListener('click', fazerPedido);
    }
});

function fazerPedido() {
    const tel   = '5514996175617';
    const texto = 'Olá! Gostaria de fazer um pedido:\nTenho interesse nos serviços da DWS Envelopamento.';
    window.open('https://wa.me/' + tel + '?text=' + encodeURIComponent(texto), '_blank');
}
