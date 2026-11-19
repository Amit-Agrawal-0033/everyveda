<?php
/**
 * Global Helper Functions for EveryVeda.com
 */

declare(strict_types=1);

/**
 * Load .env file into putenv, $_ENV, and $_SERVER
 */
function load_env(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);

            // Strip surrounding quotes
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }

            putenv("{$key}={$val}");
            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
        }
    }
}

/**
 * Get environment variable with default fallback
 */
function env(string $key, mixed $default = null): mixed
{
    $val = getenv($key);
    if ($val === false) {
        $val = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    if ($val === 'true' || $val === '(true)') return true;
    if ($val === 'false' || $val === '(false)') return false;
    if ($val === 'null' || $val === '(null)') return null;
    if ($val === 'empty' || $val === '(empty)') return '';

    return $val;
}

/**
 * Safe HTML output escaping (Context-aware XSS prevention)
 */
function e(mixed $value): string
{
    if ($value === null) {
        return '';
    }
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Format amount into Indian Rupee (INR) representation
 */
function format_inr(float|int|string|null $amount, bool $showDecimals = true): string
{
    $val = (float)($amount ?? 0);
    $formatted = number_format($val, $showDecimals ? 2 : 0, '.', ',');
    return '₹' . $formatted;
}

/**
 * Generate application URL
 */
function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    $baseUrl = rtrim((string)env('APP_URL', ''), '/');
    return $baseUrl . $path;
}

/**
 * Generate public asset URL with timestamp cache-busting
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $localFile = PUBLIC_PATH . '/' . $path;
    $v = file_exists($localFile) ? filemtime($localFile) : time();
    return url('/' . $path . '?v=' . $v);
}

/**
 * Get current active locale ('en' or 'hi')
 */
function current_locale(): string
{
    return $_SESSION['locale'] ?? env('APP_DEFAULT_LOCALE', 'en');
}

/**
 * Translation helper
 */
function __(string $key, array $replace = [], ?string $locale = null): string
{
    static $translations = [];
    $locale = $locale ?: current_locale();

    if (!isset($translations[$locale])) {
        $langFile = RESOURCE_PATH . "/lang/{$locale}.php";
        if (file_exists($langFile)) {
            $translations[$locale] = require $langFile;
        } else {
            $translations[$locale] = [];
        }
    }

    $text = $translations[$locale][$key] ?? null;

    // Fallback to English if translation is missing in Hindi
    if ($text === null && $locale !== 'en') {
        if (!isset($translations['en'])) {
            $fallbackFile = RESOURCE_PATH . "/lang/en.php";
            $translations['en'] = file_exists($fallbackFile) ? require $fallbackFile : [];
        }
        $text = $translations['en'][$key] ?? $key;
    }

    if ($text === null) {
        $text = $key;
    }

    foreach ($replace as $placeholder => $value) {
        $text = str_replace(':' . $placeholder, (string)$value, $text);
    }

    return $text;
}

/**
 * Get CSRF Token from Session, generating if missing (with cryptographically signed HMAC fallback)
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $appKey = (string)env('APP_KEY', 'everyveda-base64-secret-appkey-9a8f7e6d5c4b3a210987654321fedcba');
        $time = time();
        $random = bin2hex(random_bytes(16));
        $sig = hash_hmac('sha256', "{$time}.{$random}", $appKey);
        $_SESSION['_csrf_token'] = "{$time}.{$random}.{$sig}";
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Generate CSRF Hidden HTML Field
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF Token (Session match + Serverless resilient HMAC fallback)
 */
function verify_csrf_token(?string $token): bool
{
    if (empty($token)) {
        return false;
    }

    // 1. Primary check: Exact match with current session token
    if (!empty($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token)) {
        return true;
    }

    // 2. Resilient cryptographic HMAC check for serverless cross-instance requests
    $appKey = (string)env('APP_KEY', 'everyveda-base64-secret-appkey-9a8f7e6d5c4b3a210987654321fedcba');
    $parts = explode('.', $token);
    if (count($parts) === 3) {
        [$timeStr, $random, $sig] = $parts;
        $tokenTime = (int)$timeStr;
        $lifetime = (int)env('CSRF_TOKEN_LIFETIME', 7200);

        // Valid if generated within configured lifetime (2 hours)
        if ($tokenTime > 0 && (time() - $tokenTime) <= $lifetime) {
            $expectedSig = hash_hmac('sha256', "{$tokenTime}.{$random}", $appKey);
            if (hash_equals($expectedSig, $sig)) {
                // Restore into current session
                $_SESSION['_csrf_token'] = $token;
                return true;
            }
        }
    }

    return false;
}

/**
 * Set flash message for next request
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['_flash'][$type][] = $message;
}

/**
 * Get flash messages by type
 */
function get_flashes(): array
{
    $flashes = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flashes;
}

/**
 * Store old form input in session
 */
function flash_input(array $data): void
{
    $_SESSION['_old_input'] = $data;
}

/**
 * Retrieve old input value
 */
function old(string $field, mixed $default = ''): mixed
{
    $val = $_SESSION['_old_input'][$field] ?? $default;
    return is_string($val) ? e($val) : $val;
}

/**
 * Clear old input from session
 */
function clear_old_input(): void
{
    unset($_SESSION['_old_input']);
}

/**
 * Get authenticated customer user
 */
function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Check if customer user is authenticated
 */
function is_authenticated(): bool
{
    return !empty($_SESSION['user']['id']);
}

/**
 * Get authenticated admin user
 */
function admin_user(): ?array
{
    return $_SESSION['admin'] ?? null;
}

/**
 * Check if admin is authenticated
 */
function is_admin_authenticated(): bool
{
    return !empty($_SESSION['admin']['id']);
}

/**
 * Check admin permission
 */
function admin_can(string $permission): bool
{
    $admin = admin_user();
    if (!$admin) {
        return false;
    }
    if (($admin['role_slug'] ?? '') === 'super_admin') {
        return true;
    }
    $permissions = $_SESSION['admin']['permissions'] ?? [];
    return in_array('all', $permissions, true) || in_array($permission, $permissions, true);
}

/**
 * Slugify a string (supporting Latin and transliteration)
 */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    return empty($text) ? 'item-' . time() : $text;
}

/**
 * Sanitize CSV fields against spreadsheet formula injection (=, +, -, @, \t, \r)
 */
function sanitize_csv_cell(mixed $value): string
{
    $str = (string)($value ?? '');
    if ($str !== '' && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
        return "'" . $str;
    }
    return $str;
}
