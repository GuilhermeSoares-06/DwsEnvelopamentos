<?php
// =============================================
// helpers.php - Funções utilitárias
// =============================================

function valorParaFloat($valor): float {
    if (is_numeric($valor)) return (float)$valor;
    $valor = trim((string)$valor);
    $valor = str_replace(['R$', ' ', "\xc2\xa0"], '', $valor);
    $temVirgula = strpos($valor, ',') !== false;
    $temPonto   = strpos($valor, '.') !== false;
    if ($temVirgula && $temPonto) {
        $posV = strrpos($valor, ',');
        $posP = strrpos($valor, '.');
        if ($posV > $posP) {
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

function formatBr($valor): string {
    return number_format((float)$valor, 2, ',', '.');
}

function responderJson(array $dados, int $httpCode = 200): void {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function responderSucesso(string $mensagem, array $extras = []): void {
    responderJson(array_merge(['status' => 'sucesso', 'mensagem' => $mensagem], $extras));
}

function responderErro(string $mensagem, int $httpCode = 400, array $extras = []): void {
    responderJson(array_merge(['status' => 'erro', 'mensagem' => $mensagem], $extras), $httpCode);
}

function lerJsonEntrada(): array {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    return is_array($json) ? $json : [];
}

function limparTelefone(string $telefone): string {
    return preg_replace('/\D/', '', $telefone);
}

function telefoneParaWhatsApp(string $telefone, string $dddPadrao = '14'): string {
    $tel = limparTelefone($telefone);
    if (strlen($tel) < 8) return '';
    $tel = ltrim($tel, '0');
    if (strlen($tel) === 13 || strlen($tel) === 12) return $tel;
    if (strlen($tel) === 11 || strlen($tel) === 10) return '55' . $tel;
    if (strlen($tel) === 9 || strlen($tel) === 8) return '55' . $dddPadrao . $tel;
    return '';
}

function formatarTelefone(?string $telefone): string {
    if (empty($telefone)) return '';
    $tel = limparTelefone($telefone);
    if (strlen($tel) === 11) return '(' . substr($tel, 0, 2) . ') ' . substr($tel, 2, 5) . '-' . substr($tel, 7, 4);
    if (strlen($tel) === 10) return '(' . substr($tel, 0, 2) . ') ' . substr($tel, 2, 4) . '-' . substr($tel, 6, 4);
    return $telefone;
}

function validarCPF(string $cpf): bool {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11) return false;
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += (int)$cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ((int)$cpf[$c] !== $d) return false;
    }
    return true;
}

function formatarCPF(string $cpf): string {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11) return $cpf;
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

function validarEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function esc(?string $texto): string {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

function formatarDataBR(?string $data): string {
    if (empty($data)) return '';
    try { return (new DateTime($data))->format('d/m/Y'); }
    catch (Exception $e) { return $data; }
}

function formatarDataHoraBR(?string $data): string {
    if (empty($data)) return '';
    try { return (new DateTime($data))->format('d/m/Y H:i'); }
    catch (Exception $e) { return $data; }
}