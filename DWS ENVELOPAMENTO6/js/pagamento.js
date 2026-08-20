// =============================================
// pagamento.js - Integração com Mercado Pago
// (Checkout Pro - redireciona o cliente para o
// checkout hospedado pelo Mercado Pago)
// =============================================

async function iniciarPagamento(servicoId, emailCliente = '') {
    try {
        const resposta = await fetch('../../PHP/Pagamento/criar_preferencia.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `servico_id=${encodeURIComponent(servicoId)}&email=${encodeURIComponent(emailCliente)}`
        });

        const dados = await resposta.json();

        if (dados.status !== 'sucesso') {
            alert(dados.mensagem || 'Erro ao iniciar pagamento.');
            return;
        }

        // Redireciona o cliente para o checkout do Mercado Pago
        window.location.href = dados.init_point;

    } catch (erro) {
        console.error('Erro ao iniciar pagamento:', erro);
        alert('Não foi possível conectar ao serviço de pagamento.');
    }
}

async function verificarStatusPagamento(servicoId) {
    try {
        const resposta = await fetch(`../../PHP/Pagamento/status_pagamento.php?servico_id=${servicoId}`);
        const dados = await resposta.json();
        return dados.dados?.serstatus_pagamento || 'desconhecido';
    } catch (erro) {
        console.error('Erro ao verificar status:', erro);
        return 'erro';
    }
}

// =============================================
// Exemplo de uso, logo após agendar.php responder:
//
// const respostaAgendamento = await fetch('/PHP/Servico/agendar.php', {...});
// const dadosAgendamento = await respostaAgendamento.json();
// if (dadosAgendamento.status === 'sucesso') {
//     iniciarPagamento(dadosAgendamento.servico_id, emailDigitadoNoForm);
// }
// =============================================