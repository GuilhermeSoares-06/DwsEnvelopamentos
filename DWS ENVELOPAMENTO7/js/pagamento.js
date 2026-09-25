// =============================================
// pagamento.js - Integração com Mercado Pago
// =============================================

async function iniciarPagamento(servicoId, emailCliente = '') {
    console.log('💳 Iniciando pagamento...');
    console.log('📦 Serviço ID:', servicoId);
    console.log('📧 Email:', emailCliente);

    try {
        const resposta = await fetch('../../PHP/Pagamento/criar_preferencia.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            credentials: 'include',
            body: `servico_id=${encodeURIComponent(servicoId)}&email=${encodeURIComponent(emailCliente)}`
        });

        console.log('📡 Status HTTP:', resposta.status);

        const textoResposta = await resposta.text();
        console.log('📄 Resposta bruta:', textoResposta);

        let dados;
        try {
            dados = JSON.parse(textoResposta);
        } catch (e) {
            console.error('❌ Resposta não é JSON:', textoResposta);
            alert('Erro: o servidor não retornou JSON válido.\n\nVerifique o console para mais detalhes.');
            return;
        }

        console.log('📦 Dados parseados:', dados);

        if (dados.status !== 'sucesso') {
            const msg = dados.mensagem || 'Erro ao iniciar pagamento.';
            console.error('❌ Erro:', msg);
            if (dados.debug) {
                console.error('🔍 Debug completo:', dados.debug);
            }
            alert(msg);
            return;
        }

        if (!dados.init_point) {
            alert('Erro: URL de pagamento não retornada.');
            return;
        }

        console.log('✅ Redirecionando para:', dados.init_point);
        window.location.href = dados.init_point;

    } catch (erro) {
        console.error('❌ Erro na requisição:', erro);
        alert('Não foi possível conectar ao serviço de pagamento.\n\nErro: ' + erro.message);
    }
}

async function verificarStatusPagamento(servicoId) {
    try {
        const resposta = await fetch(`../../PHP/Pagamento/status_pagamento.php?servico_id=${servicoId}`, {
            credentials: 'include'
        });
        const dados = await resposta.json();
        return dados.dados?.serstatus_pagamento || 'desconhecido';
    } catch (erro) {
        console.error('Erro ao verificar status:', erro);
        return 'erro';
    }
}