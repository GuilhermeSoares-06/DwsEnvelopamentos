<?php
// =============================================
// token.php - Geração e validação de JWT simples (HS256)
// =============================================

define('JWT_SECRET', 'DWS_SECRET_KEY_2026_envelopamento_@#$');
define('JWT_EXPIRY',  60 * 60 * 8); // 8 horas

class Token {

    // ---- Geração ------------------------------------------------
    public static function gerar(array $payload): string {
        $header  = self::b64url(json_encode(['alg'=>'HS256','typ'=>'JWT']));
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRY;
        $pay     = self::b64url(json_encode($payload));
        $sig     = self::b64url(hash_hmac('sha256', "$header.$pay", JWT_SECRET, true));
        return "$header.$pay.$sig";
    }

    // ---- Validação -----------------------------------------------
    public static function validar(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$header, $pay, $sig] = $parts;
        $expected = self::b64url(hash_hmac('sha256', "$header.$pay", JWT_SECRET, true));

        // Comparação segura contra timing attacks
        if (!hash_equals($expected, $sig)) return null;

        $data = json_decode(self::b64urlDecode($pay), true);
        if (!$data) return null;
        if (isset($data['exp']) && $data['exp'] < time()) return null; // expirado

        return $data;
    }

    // ---- Extrai token do header Authorization: Bearer <token> ----
    public static function doHeader(): ?string {
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) return $m[1];
        return null;
    }

    // ---- Extrai token do cookie (para páginas HTML) ---------------
    public static function doCookie(): ?string {
        return $_COOKIE['dws_token'] ?? null;
    }

    // ---- Protege rota: retorna payload ou mata com 401 -----------
    public static function exigirLogin(bool $jsonResponse = true): array {
        $t = self::doHeader() ?? self::doCookie();
        if (!$t) self::naoAutorizado($jsonResponse);
        $data = self::validar($t);
        if (!$data) self::naoAutorizado($jsonResponse);
        return $data;
    }

    // ---- Exige que seja funcionário/admin ------------------------
    public static function exigirAdmin(bool $jsonResponse = true): array {
        $data = self::exigirLogin($jsonResponse);
        if (($data['tipo'] ?? '') !== 'funcionario') {
            if ($jsonResponse) {
                http_response_code(403);
                header('Content-Type: application/json');
                die(json_encode(['status'=>'erro','mensagem'=>'Acesso negado.']));
            }
            header('Location: /telas/Cliente/loginClientes.html');
            exit;
        }
        return $data;
    }

    // ---- Helpers internos ----------------------------------------
    private static function b64url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    private static function b64urlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
    }
    private static function naoAutorizado(bool $json): void {
        http_response_code(401);
        if ($json) {
            header('Content-Type: application/json');
            die(json_encode(['status'=>'erro','mensagem'=>'Não autorizado. Faça login.']));
        }
        header('Location: /telas/Cliente/loginClientes.html');
        exit;
    }
}
?>
