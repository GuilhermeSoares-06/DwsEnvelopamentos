<?php
// =============================================
// helpers.php - Funções utilitárias compartilhadas
// =============================================
// Este arquivo deve ser incluído por TODOS os
// arquivos PHP. Ele NÃO abre sessão nem conexão,
// só define funções.

// =============================================
// CONVERSÃO DE MOEDA
// =============================================

/**
 * Converte QUALQUER formato monetário para float.
 * "800"        → 800.00
 * "800,00"     → 800.00   (BR)
 * "800.00"     → 800.00   (US)
 * "2.500,00"   → 2500.00  (BR com milhar)
 * "2,500.00"   → 2500.00  (US com milhar)
 * "R$ 800,00"  → 800.00
 */
function valorParaFloat($valor) {
    if (is_numeric($valor)) return (float)$valor;

    $valor = trim((string)$valor);
    $valor = str_replace(['R$', ' ', "\xc2\xa0"], '', $valor);

    $temVirgula = strpos($valor, ',') !== false;
    $temPonto   = strpos($valor, '.') !== false;

    if ($temVirgula && $temPonto) {
        $posVirgula = strrpos($valor, ',');
        $posPonto   = strrpos($valor, '.');
        if ($posVirgula > $posPonto) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } else {
            $valor = str_replace(',', '', $valor);
        }
    } elseif ($temVirgula) {
        $valor = str_replace(',', '.', $valor);
    }
    return (float)$valor;
}

/**
 * Formata número para BR: 1234.5 → "1.234,50"
 */
function formatBr($valor) {
    return number_format((float)$valor, 2, ',', '.');
}

// =============================================
// RESPOSTAS JSON PADRONIZADAS
// =============================================

/**
 * Envia resposta JSON e encerra o script.
 */
function responderJson(array $dados, int $httpCode = 200): void {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Atalho para resposta de sucesso.
 */
function responderSucesso(string $mensagem, array $extras = []): void {
    responderJson(array_merge([
        'status' => 'sucesso',
        'mensagem' => $mensagem
    ], $extras));
}

/**
 * Atalho para resposta de erro.
 */
function responderErro(string $mensagem, int $httpCode = 400, array $extras = []): void {
    responderJson(array_merge([
        'status' => 'erro',
        'mensagem' => $mensagem
    ], $extras), $httpCode);
}

// =============================================
// JSON DE ENTRADA
// =============================================

/**
 * Lê o corpo da requisição como JSON (ou form-data).
 */
function lerJsonEntrada(): array {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    return is_array($json) ? $json : [];
}

// =============================================
// VALIDAÇÃO DE TELEFONE
// =============================================

/**
 * Limpa telefone e retorna SÓ dígitos.
 */
function limparTelefone(string $telefone): string {
    return preg_replace('/\D/', '', $telefone);
}

/**
 * Normaliza telefone para formato WhatsApp (com DDI 55).
 * Retorna '' se inválido.
 * Aceita DDD padrão se o número tiver 8 ou 9 dígitos.
 */
function telefoneParaWhatsApp(string $telefone, string $dddPadrao = '14'): string {
    $tel = limparTelefone($telefone);
    if (strlen($tel) < 8) return '';

    // Remove zeros à esquerda
    $tel = ltrim($tel, '0');

    // Já tem DDI (55) + DDD + 9 dígitos = 13
    if (strlen($tel) === 13) return $tel;
    if (strlen($tel) === 12) return $tel;

    // DDD + 9 ou 8 dígitos → adiciona 55
    if (strlen($tel) === 11 || strlen($tel) === 10) {
        return '55' . $tel;
    }

    // Só número (8 ou 9 dígitos) → adiciona DDD + 55
    if (strlen($tel) === 9 || strlen($tel) === 8) {
        return '55' . $dddPadrao . $tel;
    }

    return '';
}

/**
 * Formata telefone para exibição: (14) 99999-9999
 */
function formatarTelefone(?string $telefone): string {
    if (empty($telefone)) return '';
    $tel = limparTelefone($telefone);
    if (strlen($tel) === 11) {
        return '(' . substr($tel, 0, 2) . ') ' . substr($tel, 2, 5) . '-' . substr($tel, 7, 4);
    }
    if (strlen($tel) === 10) {
        return '(' . substr($tel, 0, 2) . ') ' . substr($tel, 2, 4) . '-' . substr($tel, 6, 4);
    }
    return $telefone;
}

// =============================================
// VALIDAÇÃO DE CPF
// =============================================

function validarCPF(string $cpf): bool {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11) return false;
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;

    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ((int)$cpf[$c] !== $d) return false;
    }
    return true;
}

function formatarCPF(string $cpf): string {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11) return $cpf;
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' .
           substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

// =============================================
// VALIDAÇÃO DE EMAIL
// =============================================

function validarEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// =============================================
// ESCAPE DE SAÍDA (segurança)
// =============================================

function esc(?string $texto): string {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// =============================================
// DATAS
// =============================================

function formatarDataBR(?string $data): string {
    if (empty($data)) return '';
    try {
        $d = new DateTime($data);
        return $d->format('d/m/Y');
    } catch (Exception $e) {
        return $data;
    }
}

function formatarDataHoraBR(?string $data): string {
    if (empty($data)) return '';
    try {
        $d = new DateTime($data);
        return $d->format('d/m/Y H:i');
    } catch (Exception $e) {
        return $data;
    }
}