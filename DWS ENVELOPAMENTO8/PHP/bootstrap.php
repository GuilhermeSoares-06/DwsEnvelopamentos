<?php
if (!defined('DEBUG')) {
    define('DEBUG', in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'])
        || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false);
}

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/config_sessao.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/Banco/conexao.php';