<?php
/**
 * Administrator Authentication Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Services\AdminAuthService;

class AuthController extends Controller
{
    /**
     * Show Admin Login Form
     */
    public function showLogin(Request $request): void
    {
        if (is_admin_authenticated()) {
            $this->redirect(url('/admin/dashboard'));
            return;
        }

        $this->view('admin/auth/login', [
            'meta_title' => 'Admin Sign In — EveryVeda Control Center'
        ], null); // Render without standard layout
    }

    /**
     * Process Admin Login
     */
    public function login(Request $request): void
    {
        $this->validateCsrf($request);

        $email = (string)$request->post('email', '');
        $password = (string)$request->post('password', '');
        $ip = $request->ip();

        $errors = $this->validate($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            flash_input(['email' => $email]);
            $this->redirect(url('/admin/login'), 'error', reset($errors));
            return;
        }

        try {
            $admin = AdminAuthService::attempt($email, $password, $ip);
            if (!$admin) {
                flash_input(['email' => $email]);
                $this->redirect(url('/admin/login'), 'error', 'Invalid administrative credentials.');
                return;
            }

            $intended = $_SESSION['admin_intended_url'] ?? url('/admin/dashboard');
            unset($_SESSION['admin_intended_url']);

            $this->redirect($intended, 'success', 'Welcome, ' . $admin['name'] . '!');
        } catch (\Throwable $e) {
            flash_input(['email' => $email]);
            $this->redirect(url('/admin/login'), 'error', $e->getMessage());
        }
    }

    /**
     * Admin Logout
     */
    public function logout(Request $request): void
    {
        AdminAuthService::logout($request->ip());
        $this->redirect(url('/admin/login'), 'info', 'You have been logged out of the admin panel.');
    }
}
