<?php
// =============================================
// bootstrap.php — Inicialização central
// Toda entrada PHP pública deve incluir este arquivo.
// =============================================

// 1) Carrega config
require_once __DIR__ . '/Config/config_loader.php';

// 2) Constantes globais
if (!defined('APP_ENV'))   define('APP_ENV',   config('app.env', 'production'));
if (!defined('APP_DEBUG')) define('APP_DEBUG', (bool) config('app.debug', false));
if (!defined('DEBUG'))     define('DEBUG',     APP_DEBUG);
if (!defined('APP_URL'))   define('APP_URL',   rtrim((string) config('app.url', ''), '/'));

// 3) Timezone
date_default_timezone_set(config('app.timezone', 'America/Sao_Paulo'));

// 4) Erros conforme ambiente
error_reporting(E_ALL);
if (APP_DEBUG) {
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// 5) Dependências base
require_once __DIR__ . '/config_sessao.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/Banco/conexao.php';