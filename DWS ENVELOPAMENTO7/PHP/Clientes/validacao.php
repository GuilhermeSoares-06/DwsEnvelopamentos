<?php
// =============================================
// validacao.php - VERSÃO COMPLETA
// =============================================

/**
 * Valida CPF brasileiro
 */
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) !== 11) return false;
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;
    
    // Calcula dígitos verificadores
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) return false;
    }
    
    return true;
}

/**
 * Valida email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida telefone
 */
function validarTelefone($telefone) {
    $telefone = preg_replace('/\D/', '', $telefone);
    return strlen($telefone) >= 10 && strlen($telefone) <= 11;
}

/**
 * Valida data no formato YYYY-MM-DD
 */
function validarData($data) {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        $date = DateTime::createFromFormat('Y-m-d', $data);
        return $date && $date->format('Y-m-d') === $data;
    }
    return false;
}

/**
 * Valida horário no formato HH:MM
 */
function validarHorario($horario) {
    return preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $horario);
}

/**
 * Sanitiza dados
 */
function sanitizar($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Formata CPF para exibição
 */
function formatarCPF($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) === 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . 
               substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    return $cpf;
}

/**
 * Formata telefone para exibição
 */
function formatarTelefone($telefone) {
    $telefone = preg_replace('/\D/', '', $telefone);
    if (strlen($telefone) === 11) {
        return '(' . substr($telefone, 0, 2) . ') ' . 
               substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4);
    }
    if (strlen($telefone) === 10) {
        return '(' . substr($telefone, 0, 2) . ') ' . 
               substr($telefone, 2, 4) . '-' . substr($telefone, 6, 4);
    }
    return $telefone;
}

/**
 * Formata data para exibição
 */
function formatarDataBR($data) {
    if (validarData($data)) {
        $date = DateTime::createFromFormat('Y-m-d', $data);
        return $date->format('d/m/Y');
    }
    return $data;
}