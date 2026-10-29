<?php
// =============================================
// config.php — Configurações centralizadas
// ⚠️ NÃO comitar este arquivo no Git (adicione ao .gitignore)
// =============================================
return [
    'db' => [
        'host'    => 'localhost',
        'port'    => 3306,
        'name'    => 'dws',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'app' => [
        'env'      => 'development',          // 'development' | 'production'
        'debug'    => true,                    // false em produção
        'url'      => 'http://localhost/dws',  // URL base (sem barra no final)
        'timezone' => 'America/Sao_Paulo',
        'session_name' => 'DWSSESSID',
        'session_lifetime' => 7200,            // 2h
    ],

    'admin' => [
        // IDs da tabela `funcionarios` que podem ver o Faturamento
        'super_admin_ids' => [1],
    ],

    'agenda' => [
        // Agendamento online não pago libera o dia após X horas
        'expira_pendente_horas' => 24,
    ],

    'contato' => [
        // Recebe o aviso de novo agendamento (somente dígitos, com DDI)
        'whatsapp' => '5514996175617',
    ],

    'mp' => [
        'ambiente'       => 'sandbox',         // 'sandbox' | 'production'
        'access_token'   => 'COLE_AQUI_O_NOVO_TOKEN',
        'webhook_secret' => 'COLE_AQUI_O_NOVO_SECRET',
    ],
];