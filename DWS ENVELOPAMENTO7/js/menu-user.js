// =============================================
// menu-user.js - Menu do usuário DINÂMICO + RÁPIDO
// =============================================

(function() {
    'use strict';

    console.log('👤 menu-user.js carregado');

    const CACHE_KEY = 'dws_sessao_cliente';
    const CACHE_TTL = 30 * 1000;

    // =============================================
    // CACHE
    // =============================================
    function getCache() {
        try {
            const raw = sessionStorage.getItem(CACHE_KEY);
            if (!raw) return null;
            const data = JSON.parse(raw);
            if (Date.now() - data.timestamp > CACHE_TTL) {
                sessionStorage.removeItem(CACHE_KEY);
                return null;
            }
            return data.sessao;
        } catch (e) {
            return null;
        }
    }

    function setCache(sessao) {
        try {
            sessionStorage.setItem(CACHE_KEY, JSON.stringify({
                sessao: sessao,
                timestamp: Date.now()
            }));
        } catch (e) {}
    }

    function limparCache() {
        try {
            sessionStorage.removeItem(CACHE_KEY);
        } catch (e) {}
    }

    // Expõe globalmente
    window.DWS_Sessao = {
        get: getCache,
        set: setCache,
        limpar: limparCache,
        verificar: verificarSessao
    };

    // =============================================
    // DETECTA CAMINHO CORRETO DO PHP
    // =============================================
    function detectarCaminhoPHP() {
        const path = window.location.pathname;
        if (path.includes('/telas/Cliente/')) return '../../PHP/Clientes/';
        if (path.includes('/telas/ADM/')) return '../../PHP/Clientes/';
        if (path.includes('/telas/')) return '../PHP/Clientes/';
        return '../../PHP/Clientes/';
    }

    // =============================================
    // MONTA MENU LOGADO
    // =============================================
    function montarMenuLogado(menu, dados) {
        menu.innerHTML = `
            <div class="user-info">
                <span class="user-name">👤 ${escapeHtml(dados.nome || 'Cliente')}</span>
                <span class="user-type">${dados.cpf ? 'CPF: ' + formatarCPF(dados.cpf) : 'Cliente'}</span>
            </div>
            <hr>
            <a href="editar_perfil.html">
                <i class="fas fa-user-edit"></i> Editar Perfil
            </a>
            <a href="meus_agendamentos.html">
                <i class="fas fa-calendar-alt"></i> Meus Agendamentos
            </a>
            <hr>
            <a href="#" id="btnSair" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        `;

        document.getElementById('btnSair')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Deseja realmente sair?')) {
                limparCache();
                window.location.href = detectarCaminhoPHP() + 'logoutcliente.php';
            }
        });
    }

    // =============================================
    // MONTA MENU DESLOGADO
    // =============================================
    function montarMenuDeslogado(menu) {
        menu.innerHTML = `
            <a href="loginClientes.html">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="CadastroCliente.html">
                <i class="fas fa-user-plus"></i> Cadastro
            </a>
        `;
    }

    // =============================================
    // VERIFICA SESSÃO (com cache)
    // =============================================
    async function verificarSessao(forcar = false) {
    if (!forcar) {
        const cached = getCache();
        if (cached) return cached;
    }

    try {
        const res = await fetch(detectarCaminhoPHP() + 'sessao_cliente.php?t=' + Date.now(), {
            credentials: 'include',
            cache: 'no-store'
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);

        const texto = await res.text();

        // Se veio HTML (erro de PHP), não tenta parsear
        if (texto.trim().startsWith('<')) {
            console.warn('Resposta HTML em vez de JSON');
            return { logado: false };
        }

        const data = JSON.parse(texto);
        setCache(data);
        return data;
    } catch (e) {
        console.warn('Erro sessão:', e.message);
        return { logado: false };
    }
}

    // =============================================
    // INICIALIZA MENU
    // =============================================
    async function inicializarMenuUser() {
        const iconeUser = document.getElementById('userIcon');
        const menuUser = document.getElementById('menuUser');

        if (!iconeUser || !menuUser) {
            console.warn('⚠️ userIcon ou menuUser não encontrados');
            return;
        }

        console.log('✅ Menu user inicializado');

        // Mostra menu do cache imediatamente
        const cached = getCache();
        if (cached) {
            if (cached.logado) {
                montarMenuLogado(menuUser, cached);
            } else {
                montarMenuDeslogado(menuUser);
            }
        } else {
            menuUser.innerHTML = '<a href="loginClientes.html"><i class="fas fa-sign-in-alt"></i> Login</a>';
        }

        // Abre/fecha menu
        iconeUser.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            menuUser.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!menuUser.classList.contains('show')) return;
            if (menuUser.contains(e.target)) return;
            if (iconeUser.contains(e.target)) return;
            menuUser.classList.remove('show');
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') menuUser.classList.remove('show');
        });

        // Verifica no servidor em background
        verificarSessao(false).then(sessao => {
            if (sessao.logado) {
                montarMenuLogado(menuUser, sessao);
            } else {
                montarMenuDeslogado(menuUser);
            }
        });
    }

    // =============================================
    // HELPERS
    // =============================================
    function escapeHtml(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }

    function formatarCPF(cpf) {
        const limpo = String(cpf).replace(/\D/g, '');
        if (limpo.length !== 11) return cpf;
        return limpo.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    // =============================================
    // INICIA
    // =============================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarMenuUser);
    } else {
        inicializarMenuUser();
    }
})();