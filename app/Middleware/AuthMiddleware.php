<?php
/**
 * Customer Authentication Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        if (!is_authenticated()) {
            if ($request->isAjax()) {
                Response::json(['error' => 'Authentication required.', 'redirect' => url('/account/login')], 401);
                return false;
            }

            $_SESSION['intended_url'] = $request->uri();
            set_flash('info', 'Please sign in to access this page.');
            Response::redirect(url('/account/login'));
            return false;
        }

        return true;
    }
}
