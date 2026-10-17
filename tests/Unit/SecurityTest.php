<?php
/**
 * Security Unit Tests: XSS, CSV Injection, CSRF, Token Randomness
 */

declare(strict_types=1);

namespace Tests\Unit;

class SecurityTest
{
    public function testHtmlEscaping(): void
    {
        $xssVector = '<script>alert("xss")</script>';
        $escaped = e($xssVector);
        assert(!str_contains($escaped, '<script>'), "HTML escaping must neutralize script tags");
        assert(str_contains($escaped, '&lt;script&gt;'), "HTML escaping must convert < to &lt;");

        $attrVector = '"><img src=x onerror=alert(1)>';
        $escapedAttr = e($attrVector);
        assert(!str_contains($escapedAttr, '<img'), "HTML escaping must neutralize image onerror tags");
        assert(str_contains($escapedAttr, '&quot;&gt;'), "HTML escaping must convert quotes and brackets");
    }

    public function testCsvFormulaInjectionSanitization(): void
    {
        // Vectors that execute commands or formulas in Excel/Sheets
        $vectors = [
            '=SUM(A1:A10)' => "'=SUM(A1:A10)",
            '+100+200' => "'+100+200",
            '-100-200' => "'-100-200",
            '@SUM(B1:B5)' => "'@SUM(B1:B5)",
            "\t=cmd|' /C calc'!A0" => "'\t=cmd|' /C calc'!A0",
            "\r=cmd|' /C calc'!A0" => "'\r=cmd|' /C calc'!A0"
        ];

        foreach ($vectors as $raw => $expected) {
            $sanitized = sanitize_csv_cell($raw);
            assert($sanitized === $expected, "CSV cell '{$raw}' must be prepended with single quote to prevent execution, got '{$sanitized}'");
        }

        // Safe strings should not be modified
        $safeStrings = ['EveryVeda', 'Order #10023', 'Ashwagandha Premium', 'info@everyveda.com'];
        foreach ($safeStrings as $safe) {
            assert(sanitize_csv_cell($safe) === $safe, "Safe cell '{$safe}' should not be prefixed");
        }
    }

    public function testCsrfTokenGenerationAndValidation(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = csrf_token();
        assert(!empty($token), "CSRF token must not be empty");
        assert(strlen($token) === 64, "CSRF token must be 64 characters (32 bytes hex)");
        assert(isset($_SESSION['_csrf_token']), "CSRF token must be stored in session");
        assert($_SESSION['_csrf_token'] === $token, "CSRF token in session must match generated token");

        $fieldHtml = csrf_field();
        assert(str_contains($fieldHtml, 'name="_csrf_token"'), "csrf_field() must generate input with name _csrf_token");
        assert(str_contains($fieldHtml, "value=\"{$token}\""), "csrf_field() must contain active token value");

        assert(verify_csrf_token($token) === true, "verify_csrf_token() must validate the current session token");
        assert(verify_csrf_token('invalid_token_string') === false, "verify_csrf_token() must reject invalid token");
        assert(verify_csrf_token('') === false, "verify_csrf_token() must reject empty token");
    }

    public function testPasswordHashingBcrypt(): void
    {
        $password = "Ayurveda@2026Safe!";
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        assert(password_verify($password, $hash), "Password verification must succeed for valid password");
        assert(!password_verify("WrongPassword123", $hash), "Password verification must fail for invalid password");
        assert(password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]) === false, "Hash cost 12 should not need rehash");
    }

    public function testTrackingTokenRandomness(): void
    {
        $tokens = [];
        for ($i = 0; $i < 100; $i++) {
            $token = bin2hex(random_bytes(16));
            assert(strlen($token) === 32, "Token must be 32 characters hex");
            assert(!isset($tokens[$token]), "Generated tracking tokens must be cryptographically unique");
            $tokens[$token] = true;
        }
    }
}
