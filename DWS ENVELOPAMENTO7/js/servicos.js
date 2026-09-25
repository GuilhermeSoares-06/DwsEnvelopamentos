// =============================================
// servicos.js - APENAS FUNÇÃO DE PEDIDO WHATSAPP
// (o menu do usuário agora é controlado pelo menu-user.js)
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ servicos.js carregado');
});

// Função para fazer pedido via WhatsApp
function fazerPedido() {
    let telefone = "5514996175617";
    let texto = `Olá! Gostaria de fazer um pedido:\nTenho interesse nos serviços da DWS Envelopamento.`;
    let url = "https://wa.me/" + telefone + "?text=" + encodeURIComponent(texto);
    window.open(url, "_blank");
}