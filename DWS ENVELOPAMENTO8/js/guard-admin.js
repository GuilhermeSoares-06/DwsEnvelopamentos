// =============================================
// guard-admin.js — Protege páginas da área ADM
// NUNCA redireciona por erro de rede — só por negativa explícita.
// =============================================
(function () {
    'use strict';

    var URL_CHECK   = '../../PHP/ADM/verificar_sessao.php';
    var URL_LOGIN   = 'login.html';
    var MAX_TENTATIVAS = 2;
    var tentativas  = 0;

    // Esconde a página enquanto valida (evita "flash" de conteúdo)
    var estilo = document.createElement('style');
    estilo.id = 'guardAdminHide';
    estilo.textContent = 'html{visibility:hidden !important}';
    document.head.appendChild(estilo);

    function liberar() {
        var s = document.getElementById('guardAdminHide');
        if (s) s.remove();
    }

    function negar(motivo) {
        console.warn('[guard-admin] Acesso negado:', motivo);
        window.location.replace(URL_LOGIN);
    }

    function verificar() {
        tentativas++;

        fetch(URL_CHECK + '?t=' + Date.now(), {
            credentials: 'include',
            cache: 'no-store',
            headers: { 'Accept': 'application/json' }
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (d) {
            // Servidor confirmou login → libera
            if (d && d.logado === true) {
                liberar();
                if (d.nome) {
                    ['adminName', 'sidebarName', 'dashboardName'].forEach(function (id) {
                        var el = document.getElementById(id);
                        if (el) el.textContent = d.nome;
                    });
                    try { localStorage.setItem('adminNome', d.nome); } catch (e) {}
                }
                return;
            }

            // Servidor disse EXPLICITAMENTE que não está logado → nega
            if (d && d.logado === false) {
                negar(d.motivo || 'sem_sessao');
                return;
            }

            // Resposta estranha (nem true nem false) → tenta de novo
            if (tentativas < MAX_TENTATIVAS) {
                setTimeout(verificar, 500);
            } else {
                console.warn('[guard-admin] Resposta inválida após tentativas. Deixando PHP proteger.');
                liberar();
            }
        })
        .catch(function (erro) {
            console.warn('[guard-admin] Erro na verificação (' + tentativas + '):', erro.message);

            if (tentativas < MAX_TENTATIVAS) {
                setTimeout(verificar, 800);
            } else {
                // NÃO redireciona por erro de rede — PHP protege de verdade
                console.error('[guard-admin] Falha após ' + MAX_TENTATIVAS + ' tentativas. Liberando — o PHP protege.');
                liberar();
            }
        });
    }

    verificar();

    // Voltar pelo navegador (bfcache): revalida
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            var s = document.createElement('style');
            s.id = 'guardAdminHide';
            s.textContent = 'html{visibility:hidden !important}';
            document.head.appendChild(s);
            tentativas = 0;
            verificar();
        }
    });
})();