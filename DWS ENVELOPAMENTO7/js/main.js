// =============================================
// main.js - Menu lateral + verificação de sessão + error handler
// =============================================

// Tratamento global de erros
window.addEventListener('error', function (e) {
    console.error('[ERRO GLOBAL]', e.message, e.filename, e.lineno);
});
window.addEventListener('unhandledrejection', function (e) {
    console.error('[PROMISE REJEITADA]', e.reason);
});

window.fetchComTimeout = async function (url, options = {}, timeoutMs = 15000) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeoutMs);
    try {
        const res = await fetch(url, { ...options, signal: controller.signal });
        clearTimeout(timer);
        return res;
    } catch (e) {
        clearTimeout(timer);
        if (e.name === 'AbortError') throw new Error('Tempo esgotado. Verifique sua conexão.');
        throw e;
    }
};

document.addEventListener('DOMContentLoaded', function () {
    console.log('✅ main.js carregado');

    // Sidebar mobile (admin)
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        });
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function () {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }

    // Verifica se é página admin
    const isAdminPage = window.location.pathname.includes('/ADM/');
    if (isAdminPage) {
        console.log('🔒 Página admin detectada');
        verificarSessaoAdmin();
    }
});

async function verificarSessaoAdmin() {
    try {
        const res = await fetch('../../PHP/ADM/verificar_sessao.php', {
            credentials: 'include',
            cache: 'no-store',
        });
        const data = await res.json();
        if (data.logado) {
            const nome = data.nome || 'Administrador';
            ['adminName', 'sidebarName', 'dashboardName'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = nome;
            });
            localStorage.setItem('adminNome', nome);
        } else {
            if (window.location.pathname.includes('principalFUN.html')) {
                window.location.href = 'login.html';
            }
        }
    } catch (e) {
        console.error('❌ Erro ao verificar sessão admin:', e);
    }
}