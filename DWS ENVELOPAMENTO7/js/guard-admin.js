// =============================================
// guard-admin.js - Protege páginas da área ADM (telas/ADM/*.html)
// Coloque como PRIMEIRO <script> do <head>:
//   <script src="../../js/guard-admin.js"></script>
// Esconde a página até o servidor confirmar o login; se não estiver
// logado (ou der erro), manda para login.html.
// OBS: a segurança real continua nos PHPs (que checam $_SESSION['admin_id']);
// este guard evita que a tela apareça para quem não está logado.
// NÃO use na própria tela de login.
// =============================================
(function () {
    'use strict';

    var URL_CHECK = '../../PHP/ADM/verificar_sessao.php';
    var URL_LOGIN = 'login.html';

    // esconde já, antes de renderizar qualquer coisa
    var estilo = document.createElement('style');
    estilo.id = 'guardAdminHide';
    estilo.textContent = 'html{visibility:hidden !important}';
    document.head.appendChild(estilo);

    function liberar() {
        var s = document.getElementById('guardAdminHide');
        if (s) s.remove();
    }

    function negar() {
        window.location.replace(URL_LOGIN);
    }

    function verificar() {
        fetch(URL_CHECK + '?t=' + Date.now(), { credentials: 'include', cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (d) { d && d.logado ? liberar() : negar(); })
            .catch(negar); // na dúvida, bloqueia
    }

    verificar();

    // Botão "voltar" do navegador depois de sair: revalida
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            var s = document.createElement('style');
            s.id = 'guardAdminHide';
            s.textContent = 'html{visibility:hidden !important}';
            document.head.appendChild(s);
            verificar();
        }
    });
})();