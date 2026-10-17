<?php
/**
 * Admin Authentication & Session Security Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class AdminAuthMiddleware
{
    public function handle(Request $request): bool
    {
        if (!is_admin_authenticated()) {
            if ($request->isAjax()) {
                Response::json(['error' => 'Admin session expired.'], 401);
                return false;
            }

            $_SESSION['admin_intended_url'] = $request->uri();
            set_flash('error', 'Please log in with administrator credentials.');
            Response::redirect(url('/admin/login'));
            return false;
        }

        // Admin Session Inactivity Check (30 mins inactivity timeout for admin panel)
        $lastAdminActivity = $_SESSION['admin']['last_active'] ?? time();
        if (time() - $lastAdminActivity > 1800) {
            unset($_SESSION['admin']);
            set_flash('warning', 'Admin session timed out due to inactivity.');
            Response::redirect(url('/admin/login'));
            return false;
        }
        $_SESSION['admin']['last_active'] = time();

        return true;
    }
}
