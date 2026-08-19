// =============================================
// main.js - COM MENU LATERAL
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ main.js carregado');
    
    // =============================================
    // ABRE/FECHA SIDEBAR
    // =============================================
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
        
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }
    
    // =============================================
    // CARREGA DADOS DO USUÁRIO
    // =============================================
    carregarDadosUsuario();
});

function carregarDadosUsuario() {
    const fotoSalva = localStorage.getItem('fotoPerfilDWS');
    if (fotoSalva) {
        const userIcon = document.getElementById('userIcon');
        const sidebarImg = document.getElementById('sidebarUserImg');
        if (userIcon) userIcon.src = fotoSalva;
        if (sidebarImg) sidebarImg.src = fotoSalva;
    }
}