// js/login.js
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const nome = form.querySelector('[name="nome"]').value.trim();
        const senha = form.querySelector('[name="senha"]').value.trim();

        // Validação básica
        if (!nome || !senha) {
            showMessage('Preencha todos os campos.', 'error');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('senha', senha);

            const response = await fetch('../Clientes/LoginClientes.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            console.log('Resposta do login:', data); // Debug

            if (data.status === 'sucesso') {
                // Salva token no localStorage como fallback
                if (data.token) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('usuario', JSON.stringify(data.usuario));
                }

                showMessage('✅ ' + data.mensagem, 'success');
                
                // Redireciona para a página principal do cliente
                setTimeout(() => {
                    window.location.href = '../telas/Cliente/principal.html';
                }, 1000);
            } else {
                showMessage('❌ ' + data.mensagem, 'error');
            }
        } catch (error) {
            console.error('Erro no login:', error);
            showMessage('❌ Erro ao conectar com o servidor.', 'error');
        }
    });

    function showMessage(msg, type) {
        let div = document.getElementById('msgLogin');
        if (!div) {
            div = document.createElement('div');
            div.id = 'msgLogin';
            div.style.cssText = 'padding:11px 15px;border-radius:10px;margin-top:12px;font-size:14px;text-align:center;font-weight:500;';
            const btn = document.querySelector('button[type="submit"]');
            btn.parentNode.insertBefore(div, btn.nextSibling);
        }
        div.style.background = type === 'success' ? '#1e7e34' : '#c91f2c';
        div.style.color = '#fff';
        div.textContent = msg;
        div.style.display = 'block';
    }
});