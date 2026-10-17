<?php
/**
 * CSRF Protection Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware
{
    private array $except = [
        '/webhook/razorpay',
    ];

    public function handle(Request $request): bool
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        $uri = $request->uri();
        foreach ($this->except as $exemptPath) {
            if ($uri === $exemptPath || str_starts_with($uri, $exemptPath)) {
                return true;
            }
        }

        $token = $request->post('_csrf_token') ?? $request->header('X-CSRF-TOKEN');

        if (!verify_csrf_token($token)) {
            http_response_code(403);
            if ($request->isAjax()) {
                Response::json(['error' => 'Security token invalid or expired. Please refresh the page.'], 403);
                return false;
            }

            set_flash('error', 'Session expired. Please submit the form again.');
            Response::redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
            return false;
        }

        return true;
    }
}
