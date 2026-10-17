<?php
/**
 * Customer Account, Orders & Profile Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Order;
use App\Services\AuthService;
use App\Services\InvoiceService;
use App\Services\MailerService;

class AccountController extends Controller
{
    /**
     * Show Login Page
     */
    public function showLogin(Request $request): void
    {
        if (is_authenticated()) {
            $this->redirect(url('/account'));
            return;
        }

        $this->view('account/login', [
            'meta_title' => __('login') . ' — EveryVeda'
        ]);
    }

    /**
     * Process Customer Login
     */
    public function login(Request $request): void
    {
        $this->validateCsrf($request);

        $email = (string)$request->post('email', '');
        $password = (string)$request->post('password', '');

        $errors = $this->validate($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            flash_input($request->all());
            $this->redirect(url('/account/login'), 'error', reset($errors));
            return;
        }

        try {
            $user = AuthService::attempt($email, $password);
            if (!$user) {
                flash_input(['email' => $email]);
                $this->redirect(url('/account/login'), 'error', 'Invalid email or password combination.');
                return;
            }

            $intended = $_SESSION['intended_url'] ?? url('/account');
            unset($_SESSION['intended_url']);

            $this->redirect($intended, 'success', 'Welcome back to EveryVeda, ' . $user['name'] . '!');
        } catch (\Throwable $e) {
            flash_input(['email' => $email]);
            $this->redirect(url('/account/login'), 'error', $e->getMessage());
        }
    }

    /**
     * Show Register Page
     */
    public function showRegister(Request $request): void
    {
        if (is_authenticated()) {
            $this->redirect(url('/account'));
            return;
        }

        $this->view('account/register', [
            'meta_title' => __('register') . ' — EveryVeda'
        ]);
    }

    /**
     * Process Customer Registration
     */
    public function register(Request $request): void
    {
        $this->validateCsrf($request);

        $errors = $this->validate($request->all(), [
            'name' => 'required|min:2|max:150',
            'email' => 'required|email',
            'phone' => 'required|phone',
            'password' => 'required|min:8|confirmed'
        ]);

        if (!empty($errors)) {
            flash_input($request->all());
            $this->redirect(url('/account/register'), 'error', reset($errors));
            return;
        }

        try {
            $user = AuthService::register($request->all());
            $this->redirect(url('/account'), 'success', 'Welcome to EveryVeda, ' . $user['name'] . '! Your account is active.');
        } catch (\Throwable $e) {
            flash_input($request->all());
            $this->redirect(url('/account/register'), 'error', $e->getMessage());
        }
    }

    /**
     * Customer Logout
     */
    public function logout(Request $request): void
    {
        AuthService::logout();
        $this->redirect(url('/'), 'info', 'You have been logged out successfully.');
    }

    /**
     * Forgot Password View
     */
    public function showForgotPassword(Request $request): void
    {
        $this->view('account/forgot_password', [
            'meta_title' => 'Reset Password — EveryVeda'
        ]);
    }

    /**
     * Send Password Reset Link
     */
    public function sendResetLink(Request $request): void
    {
        $this->validateCsrf($request);

        $email = trim((string)$request->post('email', ''));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect(url('/account/forgot-password'), 'error', 'Please enter a valid email address.');
            return;
        }

        $token = AuthService::createPasswordResetToken($email);
        if ($token) {
            MailerService::sendPasswordReset($email, $token);
        }

        // Generic response to prevent user enumeration
        $this->redirect(url('/account/login'), 'success', 'If an account exists with that email, a password reset link has been sent.');
    }

    /**
     * Reset Password Form
     */
    public function showResetPassword(Request $request): void
    {
        $token = (string)$request->get('token', '');
        $email = (string)$request->get('email', '');

        $this->view('account/reset_password', [
            'meta_title' => 'Create New Password — EveryVeda',
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Process Reset Password
     */
    public function resetPassword(Request $request): void
    {
        $this->validateCsrf($request);

        $token = (string)$request->post('token', '');
        $password = (string)$request->post('password', '');

        $errors = $this->validate($request->all(), [
            'token' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        if (!empty($errors)) {
            $this->redirect($_SERVER['HTTP_REFERER'] ?? url('/account/login'), 'error', reset($errors));
            return;
        }

        $success = AuthService::resetPassword($token, $password);
        if (!$success) {
            $this->redirect(url('/account/forgot-password'), 'error', 'This password reset link is invalid or has expired.');
            return;
        }

        $this->redirect(url('/account/login'), 'success', 'Your password has been reset successfully. Please log in.');
    }

    /**
     * Customer Dashboard
     */
    public function dashboard(Request $request): void
    {
        $userId = (int)auth_user()['id'];
        $recentOrders = Database::fetchAll(
            "SELECT * FROM orders WHERE user_id = :uid ORDER BY id DESC LIMIT 5",
            ['uid' => $userId]
        );

        $addressCount = (int)Database::fetchColumn(
            "SELECT COUNT(*) FROM customer_addresses WHERE user_id = :uid",
            ['uid' => $userId]
        );

        $this->view('account/dashboard', [
            'meta_title' => __('dashboard') . ' — EveryVeda',
            'recentOrders' => $recentOrders,
            'addressCount' => $addressCount
        ]);
    }

    /**
     * Customer Order History
     */
    public function orders(Request $request): void
    {
        $userId = (int)auth_user()['id'];
        $orders = Database::fetchAll(
            "SELECT * FROM orders WHERE user_id = :uid ORDER BY id DESC",
            ['uid' => $userId]
        );

        $this->view('account/orders', [
            'meta_title' => __('orders') . ' — EveryVeda',
            'orders' => $orders
        ]);
    }

    /**
     * Customer Order Details
     */
    public function orderDetail(Request $request, string $id): void
    {
        $userId = (int)auth_user()['id'];
        $orderData = Order::findForCustomer((int)$id, $userId);

        if (!$orderData) {
            $this->redirect(url('/account/orders'), 'error', 'Order not found or unauthorized.');
            return;
        }

        $this->view('account/order_detail', [
            'meta_title' => 'Order #' . $orderData['order']['order_number'] . ' — EveryVeda',
            'order' => $orderData['order'],
            'items' => $orderData['items'],
            'shipment' => $orderData['shipment'],
            'timeline' => $orderData['timeline']
        ]);
    }

    /**
     * Customer Download Tax Invoice
     */
    public function downloadInvoice(Request $request, string $id): void
    {
        $userId = (int)auth_user()['id'];
        $order = Database::fetchOne("SELECT id FROM orders WHERE id = :id AND user_id = :uid", ['id' => (int)$id, 'uid' => $userId]);

        if (!$order) {
            http_response_code(403);
            echo "Unauthorized access to invoice.";
            return;
        }

        echo InvoiceService::generateHtml((int)$id);
        exit;
    }

    /**
     * Saved Addresses
     */
    public function addresses(Request $request): void
    {
        $userId = (int)auth_user()['id'];
        $addresses = Database::fetchAll(
            "SELECT * FROM customer_addresses WHERE user_id = :uid ORDER BY is_default_shipping DESC, id DESC",
            ['uid' => $userId]
        );

        $this->view('account/addresses', [
            'meta_title' => __('addresses') . ' — EveryVeda',
            'addresses' => $addresses
        ]);
    }

    /**
     * Save New Address
     */
    public function saveAddress(Request $request): void
    {
        $this->validateCsrf($request);
        $userId = (int)auth_user()['id'];

        $errors = $this->validate($request->all(), [
            'recipient_name' => 'required|min:2|max:150',
            'phone' => 'required|phone',
            'address_line1' => 'required|min:5|max:255',
            'city' => 'required|min:2|max:100',
            'state' => 'required|min:2|max:100',
            'pincode' => 'required|pincode'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/account/addresses'), 'error', reset($errors));
            return;
        }

        $isDefault = !empty($request->post('is_default_shipping')) ? 1 : 0;
        if ($isDefault) {
            Database::query("UPDATE customer_addresses SET is_default_shipping = 0 WHERE user_id = :uid", ['uid' => $userId]);
        }

        Database::insert('customer_addresses', [
            'user_id' => $userId,
            'recipient_name' => trim((string)$request->post('recipient_name')),
            'phone' => preg_replace('/[^\d]/', '', (string)$request->post('phone')),
            'address_line1' => trim((string)$request->post('address_line1')),
            'address_line2' => trim((string)$request->post('address_line2', '')),
            'landmark' => trim((string)$request->post('landmark', '')),
            'city' => trim((string)$request->post('city')),
            'state' => trim((string)$request->post('state')),
            'pincode' => trim((string)$request->post('pincode')),
            'is_default_shipping' => $isDefault
        ]);

        $this->redirect(url('/account/addresses'), 'success', 'Address saved successfully.');
    }

    /**
     * Delete Address
     */
    public function deleteAddress(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $userId = (int)auth_user()['id'];

        Database::delete('customer_addresses', "id = :id AND user_id = :uid", ['id' => (int)$id, 'uid' => $userId]);
        $this->redirect(url('/account/addresses'), 'info', 'Address deleted.');
    }

    /**
     * Customer Profile Form
     */
    public function profile(Request $request): void
    {
        $userId = (int)auth_user()['id'];
        $user = Database::fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $userId]);

        $this->view('account/profile', [
            'meta_title' => __('profile') . ' — EveryVeda',
            'user' => $user
        ]);
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request): void
    {
        $this->validateCsrf($request);
        $userId = (int)auth_user()['id'];

        $errors = $this->validate($request->all(), [
            'name' => 'required|min:2|max:150',
            'phone' => 'required|phone'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/account/profile'), 'error', reset($errors));
            return;
        }

        AuthService::updateProfile($userId, $request->all());
        $this->redirect(url('/account/profile'), 'success', 'Profile updated successfully.');
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request): void
    {
        $this->validateCsrf($request);
        $userId = (int)auth_user()['id'];

        $errors = $this->validate($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/account/profile'), 'error', reset($errors));
            return;
        }

        try {
            AuthService::changePassword($userId, (string)$request->post('old_password'), (string)$request->post('password'));
            $this->redirect(url('/account/profile'), 'success', 'Password updated successfully.');
        } catch (\Throwable $e) {
            $this->redirect(url('/account/profile'), 'error', $e->getMessage());
        }
    }
}
