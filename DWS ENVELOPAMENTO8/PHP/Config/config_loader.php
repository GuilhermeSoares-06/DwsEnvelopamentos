<?php
// =============================================
// config_loader.php — Carrega config.php e expõe helper
// =============================================

if (!function_exists('config')) {
    function config(?string $chave = null, $default = null)
    {
        static $cfg = null;

        if ($cfg === null) {
            $arquivo = __DIR__ . '/config.php';
            if (!is_readable($arquivo)) {
                error_log('[CONFIG] config.php não encontrado em ' . $arquivo);
                $cfg = [];
            } else {
                $cfg = require $arquivo;
            }
        }

        if ($chave === null) return $cfg;

        // Suporte a notação com ponto: config('db.host')
        $partes = explode('.', $chave);
        $valor = $cfg;
        foreach ($partes as $p) {
            if (!is_array($valor) || !array_key_exists($p, $valor)) {
                return $default;
            }
            $valor = $valor[$p];
        }
        return $valor;
    }
}