/**
 * cadastro.js - Cadastro de clientes (CadastroCliente.html)
 */
document.addEventListener('DOMContentLoaded', () => {
    const form    = document.querySelector('form');
    const btnCad  = document.querySelector('.btn-cadastro');
    if (!form) return;

    // Máscara CPF
    const inputCPF = form.querySelector('[name="cpf"]');
    if (inputCPF) {
        inputCPF.addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            e.target.value = v;
        });
    }

    // Máscara Telefone
    const inputTel = form.querySelector('[name="telefone"]');
    if (inputTel) {
        inputTel.addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4,5})(\d{0,4})/, '($1) $2-$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            e.target.value = v;
        });
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();

        const nome     = form.querySelector('[name="nome"]').value.trim();
        const senha    = form.querySelector('[name="Senha"]').value.trim();
        const cpf      = form.querySelector('[name="cpf"]').value.trim();
        const endereco = form.querySelector('[name="endereco"]').value.trim();
        const telefone = form.querySelector('[name="telefone"]').value.trim();

        // Validação frontend
        const erros = [];
        if (!nome)                           erros.push('Nome é obrigatório.');
        if (senha.length < 4)                erros.push('Senha deve ter pelo menos 4 caracteres.');
        if (cpf.replace(/\D/g,'').length !== 11) erros.push('CPF inválido.');
        if (!telefone)                       erros.push('Telefone é obrigatório.');
        if (!endereco)                       erros.push('Endereço é obrigatório.');

        if (erros.length) { showMensagem(erros.join(' '), false); return; }

        btnCad.disabled = true;
        btnCad.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cadastrando...';

        const fd = new FormData(form);
        const res  = await fetch(form.action, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.status === 'sucesso') {
            showMensagem('✅ ' + data.mensagem, true);
            btnCad.innerHTML = '<i class="fas fa-check"></i> Cadastrado!';
            setTimeout(() => { window.location.href = 'loginClientes.html'; }, 2000);
        } else {
            btnCad.disabled = false;
            btnCad.innerHTML = '<i class="fas fa-user-plus"></i> CADASTRAR';
            showMensagem('❌ ' + data.mensagem, false);
        }
    });

    function showMensagem(msg, ok) {
        let div = document.getElementById('msgCadastro');
        if (!div) {
            div = document.createElement('div');
            div.id = 'msgCadastro';
            div.style.cssText =
                'padding:11px 15px;border-radius:10px;margin-top:12px;' +
                'font-size:14px;text-align:center;font-weight:500;';
            const btn = document.querySelector('.btn-cadastro');
            btn.parentNode.insertBefore(div, btn.nextSibling);
        }
        div.style.background = ok ? '#1e7e34' : '#c91f2c';
        div.style.color = '#fff';
        div.textContent = msg;
        div.style.display = 'block';
    }
});
