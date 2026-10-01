<?php
if (!defined('DEBUG')) {
    define('DEBUG', in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'])
        || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false);
}
// =============================================
// bootstrap.php - Inicialização comum
// =============================================
// Incluir como PRIMEIRA linha de todo arquivo
// PHP (antes de qualquer require).
//
// Carrega: config de sessão, helpers, segurança e conexão.

// 1. Configurações seguras de sessão
require_once __DIR__ . '/config_sessao.php';

// 2. Helpers (funções utilitárias)
require_once __DIR__ . '/helpers.php';

// 3. Segurança (headers, rate limit, CSRF)
require_once __DIR__ . '/security.php';

// 4. Conexão com banco
require_once __DIR__ . '/Banco/conexao.php';