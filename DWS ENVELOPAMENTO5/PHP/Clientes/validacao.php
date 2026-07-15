<?php
// =============================================
// validacao.php - Funções de validação
// =============================================

/**
 * Valida CPF brasileiro
 * @param string $cpf CPF com ou sem pontuação
 * @return bool
 */
function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se tem 11 dígitos
    if (strlen($cpf) !== 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais (ex: 111.111.111-11)
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Calcula o primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += (int)$cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Calcula o segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += (int)$cpf[$i] * (11 - $i);
    }
    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Verifica se os dígitos calculados conferem
    return ($cpf[9] == $digito1 && $cpf[10] == $digito2);
}

/**
 * Valida email
 * @param string $email
 * @return bool
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida telefone (formato básico)
 * @param string $telefone
 * @return bool
 */
function validarTelefone($telefone) {
    $telefone = preg_replace('/\D/', '', $telefone);
    return strlen($telefone) >= 10 && strlen($telefone) <= 11;
}

/**
 * Sanitiza dados para evitar XSS
 * @param string $input
 * @return string
 */
function sanitizar($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
?>