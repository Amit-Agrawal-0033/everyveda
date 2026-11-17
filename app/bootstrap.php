<?php
/**
 * EveryVeda Application Bootstrapper
 * Initializes Environment, Autoloading, Configuration, Session, Error Handling
 */

declare(strict_types=1);

define('EVERYVEDA_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('RESOURCE_PATH', BASE_PATH . '/resources');
define('ROOT_PATH', BASE_PATH);

// 1. Native PSR-4 Autoloader
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Load Composer Autoloader if available
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// 3. Load Helper Functions
require_once APP_PATH . '/Helpers/functions.php';

// 4. Load Environment Variables (.env)
load_env(BASE_PATH . '/.env');

// 5. Error Reporting and Exception Handling
$isProduction = env('APP_ENV', 'production') === 'production';
$isDebug = filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);

if ($isDebug && !$isProduction) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    $logDir = STORAGE_PATH . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    ini_set('error_log', is_dir($logDir) && is_writable($logDir) ? ($logDir . '/error.log') : 'php://stderr');
}

// 6. Timezone Configuration (India Standard Time by default)
$timezone = env('APP_TIMEZONE', 'Asia/Kolkata');
date_default_timezone_set($timezone);

// 7. Secure Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    $sessionLifetime = (int)env('SESSION_LIFETIME', 7200);
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
               || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
               || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    
    $secureCookie = filter_var(env('SESSION_SECURE_COOKIE', $isHttps), FILTER_VALIDATE_BOOLEAN);

    // Resilient Session Storage: Use STORAGE_PATH if writable, otherwise fallback to system temp (/tmp on Vercel)
    $sessionDir = STORAGE_PATH . '/sessions';
    if (!is_dir($sessionDir)) {
        @mkdir($sessionDir, 0755, true);
    }
    if (is_dir($sessionDir) && is_writable($sessionDir)) {
        session_save_path($sessionDir);
    } else {
        $tempSessionDir = sys_get_temp_dir() . '/everyveda_sessions';
        if (!is_dir($tempSessionDir)) {
            @mkdir($tempSessionDir, 0777, true);
        }
        if (is_dir($tempSessionDir) && is_writable($tempSessionDir)) {
            session_save_path($tempSessionDir);
        }
    }

    ini_set('session.cookie_lifetime', (string)$sessionLifetime);
    ini_set('session.gc_maxlifetime', (string)$sessionLifetime);
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path' => '/',
        'domain' => '',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// 8. Session Expiry & Inactivity Check
if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity'] > (int)env('SESSION_LIFETIME', 7200))) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['_last_activity'] = time();

// 9. Locale Initialization
if (!isset($_SESSION['locale'])) {
    if (isset($_COOKIE['everyveda_locale']) && in_array($_COOKIE['everyveda_locale'], ['en', 'hi'], true)) {
        $_SESSION['locale'] = $_COOKIE['everyveda_locale'];
    } else {
        $_SESSION['locale'] = env('APP_DEFAULT_LOCALE', 'en');
    }
}
