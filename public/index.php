<?php
/**
 * EveryVeda.com - Front Controller Entry Point
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../routes/web.php';

use App\Core\Request;
use App\Core\Router;

try {
    $request = Request::capture();
    Router::dispatch($request);
} catch (\Throwable $e) {
    if (env('APP_DEBUG', false)) {
        http_response_code(500);
        echo "<div style='font-family:sans-serif;padding:30px;background:#fff0f0;border-left:5px solid #d9534f;color:#333;'>";
        echo "<h1 style='color:#d9534f;'>Application Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . " : line " . $e->getLine() . "</p>";
        echo "<pre style='background:#f9f9f9;padding:15px;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
        echo "</div>";
    } else {
        error_log("Critical Application Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        http_response_code(500);
        echo "<!DOCTYPE html><html><head><title>System Error - EveryVeda</title><style>body{font-family:system-ui,-apple-system,sans-serif;padding:50px;text-align:center;color:#26332B;background:#FAF7EF;}.box{max-width:500px;margin:auto;background:#fff;padding:40px;border-radius:8px;box-shadow:0 4px 15px rgba(0,0,0,0.05);}h1{color:#214D36;margin-top:0;}a{display:inline-block;margin-top:20px;padding:10px 20px;background:#214D36;color:#fff;text-decoration:none;border-radius:4px;font-weight:600;}</style></head><body><div class='box'><h1>Temporary Service Notice</h1><p>We are currently experiencing a brief technical interruption. Our Vaidya support team is working to restore normal operations.</p><a href='/'>Return to Homepage</a></div></body></html>";
    }
}
