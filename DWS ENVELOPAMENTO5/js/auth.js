/**
 * auth.js - Gerenciamento de autenticação via token JWT
 * Usado em: todas as páginas HTML
 *
 * - Armazena token no localStorage
 * - Verifica sessão ao carregar a página
 * - Atualiza o header (ícone de usuário / menu)
 * - Funções: DWSAuth.login(), DWSAuth.logout(), DWSAuth.getToken(), DWSAuth.getUser()
 */

const DWSAuth = (() => {
    const TOKEN_KEY = 'dws_token';
    const USER_KEY  = 'dws_user';

    // ---- Helpers de storage ----
    function saveSession(token, user) {
        localStorage.setItem(TOKEN_KEY, token);
        localStorage.setItem(USER_KEY, JSON.stringify(user));
    }

    function clearSession() {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_KEY);
    }

    function getToken() {
        return localStorage.getItem(TOKEN_KEY) || null;
    }

    function getUser() {
        try {
            return JSON.parse(localStorage.getItem(USER_KEY)) || null;
        } catch {
            return null;
        }
    }

    function isLoggedIn() {
        return !!getToken() && !!getUser();
    }

    // ---- Cabeçalho Authorization para fetch ----
    function authHeaders() {
        const t = getToken();
        return t ? { 'Authorization': 'Bearer ' + t } : {};
    }

    // ---- Login (cliente ou funcionário) ----
    async function login(url, nome, senha) {
        const fd = new FormData();
        fd.append('nome',  nome);
        fd.append('senha', senha);

        const res  = await fetch(url, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.status === 'sucesso') {
            saveSession(data.token, data.usuario);
            return { ok: true, usuario: data.usuario };
        }
        return { ok: false, mensagem: data.mensagem || 'Erro ao fazer login.' };
    }

    // ---- Logout ----
    async function logout(redirectUrl = '../../telas/Cliente/principal.html') {
        clearSession();
        try {
            await fetch('../../PHP/Usuarios/Logout.php');
        } catch (_) { /* ignora */ }
        if (redirectUrl) window.location.href = redirectUrl;
    }

    // ---- Verifica sessão com o servidor (valida expiração) ----
    async function verificarSessao() {
        const token = getToken();
        if (!token) return false;

        try {
            const res  = await fetch('../../PHP/Auth/verificar.php', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            if (!data.logado) {
                clearSession();
                return false;
            }
            // Atualiza dados locais
            localStorage.setItem(USER_KEY, JSON.stringify(data.usuario));
            return true;
        } catch {
            return false;  // sem conexão — mantém sessão local
        }
    }

    // ---- Atualiza o header da página (ícone de usuário) ----
    function atualizarHeader() {
        const menuUser = document.getElementById('menuUser');
        if (!menuUser) return;

        const user = getUser();

        if (user) {
            // Usuário logado
            menuUser.innerHTML = `
                <span style="padding:8px 14px;color:#F23535;font-weight:bold;font-size:13px">
                    👤 ${user.nome.split(' ')[0]}
                </span>
                ${user.tipo === 'funcionario'
                    ? `<a href="../../telas/ADM/principalFUN.html">Painel ADM</a>`
                    : ''}
                <a href="#" onclick="DWSAuth.logout(); return false;">Sair</a>
            `;
        } else {
            // Não logado
            menuUser.innerHTML = `
                <a href="../../telas/Cliente/loginClientes.html">Login</a>
                <a href="../../telas/Cliente/CadastroCliente.html">Cadastro</a>
            `;
        }
    }

    // ---- Inicialização automática ----
    function init() {
        // Atualiza header imediatamente com dados locais
        atualizarHeader();

        // Valida token com o servidor em background
        if (getToken()) {
            verificarSessao().then(ok => {
                if (!ok) atualizarHeader(); // força atualização se expirou
            });
        }

        // Menu dropdown do ícone de usuário
        const iconeUser = document.getElementById('userIcon');
        const menuUser  = document.getElementById('menuUser');
        if (iconeUser && menuUser) {
            iconeUser.addEventListener('click', e => {
                e.stopPropagation();
                menuUser.style.display = menuUser.style.display === 'flex' ? 'none' : 'flex';
            });
            document.addEventListener('click', e => {
                if (!iconeUser.contains(e.target) && !menuUser.contains(e.target)) {
                    menuUser.style.display = 'none';
                }
            });
        }
    }

    // ---- Protege página (redireciona se não logado) ----
    function exigirLogin(redirectUrl = '../../telas/Cliente/loginClientes.html') {
        if (!isLoggedIn()) {
            window.location.href = redirectUrl;
            return false;
        }
        return true;
    }

    // ---- Protege página de ADM ----
    function exigirAdmin(redirectUrl = '../../telas/Cliente/login.html') {
        const user = getUser();
        if (!isLoggedIn() || !user || user.tipo !== 'funcionario') {
            window.location.href = redirectUrl;
            return false;
        }
        return true;
    }

    // API pública
    return { login, logout, getToken, getUser, isLoggedIn, authHeaders,
             verificarSessao, atualizarHeader, init, exigirLogin, exigirAdmin,
             saveSession, clearSession };
})();

// Inicializa automaticamente quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => DWSAuth.init());
