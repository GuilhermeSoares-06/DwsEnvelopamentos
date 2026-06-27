/**
 * login_adm.js - Login de funcionário/ADM (login.html)
 */
document.addEventListener('DOMContentLoaded', () => {
    if (DWSAuth.isLoggedIn() && DWSAuth.getUser()?.tipo === 'funcionario') {
        window.location.href = 'principalFUN.html';
        return;
    }

    const form    = document.querySelector('form');
    const btnLogin = document.querySelector('.btn-login');
    if (!form) return;

    form.addEventListener('submit', async e => {
        e.preventDefault();

        const nome  = form.querySelector('[name="nome"]').value.trim();
        const senha = form.querySelector('[name="senha"]').value.trim();

        if (!nome || !senha) {
            showErro('Preencha usuário e senha.');
            return;
        }

        btnLogin.disabled = true;
        btnLogin.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';

        const resultado = await DWSAuth.login(
            '../../PHP/Usuarios/Login.php',
            nome,
            senha
        );

        if (resultado.ok) {
            btnLogin.innerHTML = '<i class="fas fa-check"></i> Acesso liberado!';
            setTimeout(() => { window.location.href = 'principalFUN.html'; }, 700);
        } else {
            btnLogin.disabled = false;
            btnLogin.innerHTML = '<i class="fas fa-sign-in-alt"></i> ENTRAR';
            showErro(resultado.mensagem);
        }
    });

    function showErro(msg) {
        let div = document.getElementById('erroLogin');
        if (!div) {
            div = document.createElement('div');
            div.id = 'erroLogin';
            div.style.cssText =
                'background:#c91f2c;color:#fff;padding:11px 15px;border-radius:10px;' +
                'margin-top:12px;font-size:14px;text-align:center;';
            const btn = document.querySelector('.btn-login');
            btn.parentNode.insertBefore(div, btn.nextSibling);
        }
        div.textContent = '⚠️ ' + msg;
    }
});
