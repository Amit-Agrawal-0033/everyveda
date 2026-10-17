<?php
/**
 * Checkout & Payment Processing Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;

class CheckoutController extends Controller
{
    /**
     * View Checkout Page
     */
    public function index(Request $request): void
    {
        $cart = CartService::getCartDetails();
        if (empty($cart['items'])) {
            $this->redirect(url('/cart'), 'warning', 'Your shopping cart is empty.');
            return;
        }

        $userId = auth_user()['id'] ?? null;
        $savedAddresses = [];
        if ($userId) {
            $savedAddresses = Database::fetchAll(
                "SELECT * FROM customer_addresses WHERE user_id = :uid ORDER BY is_default_shipping DESC, id DESC",
                ['uid' => $userId]
            );
        }

        $this->view('checkout/index', [
            'meta_title' => __('checkout') . ' — EveryVeda',
            'cart' => $cart,
            'savedAddresses' => $savedAddresses,
            'enableCod' => (bool)env('ENABLE_COD', true)
        ]);
    }

    /**
     * Submit Checkout
     */
    public function submit(Request $request): void
    {
        $this->validateCsrf($request);

        $rules = [
            'name' => 'required|min:2|max:150',
            'email' => 'required|email',
            'phone' => 'required|phone',
            'shipping_address_line1' => 'required|min:5|max:255',
            'shipping_city' => 'required|min:2|max:100',
            'shipping_state' => 'required|min:2|max:100',
            'shipping_pincode' => 'required|pincode',
            'payment_method' => 'required'
        ];

        // If billing address is different, validate billing fields
        if (empty($request->post('same_as_shipping'))) {
            $rules['billing_address_line1'] = 'required|min:5|max:255';
            $rules['billing_city'] = 'required|min:2|max:100';
            $rules['billing_state'] = 'required|min:2|max:100';
            $rules['billing_pincode'] = 'required|pincode';
        }

        $errors = $this->validate($request->all(), $rules);
        if (!empty($errors)) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors, 'message' => reset($errors)], 422);
                return;
            }
            flash_input($request->all());
            $this->redirect(url('/checkout'), 'error', reset($errors));
            return;
        }

        try {
            $result = CheckoutService::processCheckout($request->all());

            if ($request->isAjax()) {
                $this->json(array_merge(['success' => true], $result));
                return;
            }

            if ($result['status'] === 'success') {
                $this->redirect($result['redirect_url']);
                return;
            }

            // Standard non-AJAX Razorpay fallback view
            $this->view('checkout/razorpay_modal', ['checkoutData' => $result]);
        } catch (\Throwable $e) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 400);
                return;
            }
            flash_input($request->all());
            $this->redirect(url('/checkout'), 'error', $e->getMessage());
        }
    }

    /**
     * Verify Razorpay Payment Signature (AJAX)
     */
    public function verifyRazorpay(Request $request): void
    {
        $this->validateCsrf($request);

        $orderId = (string)$request->post('razorpay_order_id', '');
        $paymentId = (string)$request->post('razorpay_payment_id', '');
        $signature = (string)$request->post('razorpay_signature', '');

        if (empty($orderId) || empty($paymentId) || empty($signature)) {
            $this->json(['success' => false, 'error' => 'Incomplete payment parameters returned from payment gateway.'], 400);
            return;
        }

        try {
            $result = CheckoutService::reconcilePayment($orderId, $paymentId, $signature);
            $this->json([
                'success' => true,
                'redirect_url' => url('/order-confirmed?token=' . $result['tracking_token'])
            ]);
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Order Confirmed View
     */
    public function confirmed(Request $request): void
    {
        $token = $request->get('token');
        if (empty($token)) {
            $this->redirect(url('/'));
            return;
        }

        $trackingData = Order::findByTrackingToken($token);
        if (!$trackingData) {
            $this->redirect(url('/'), 'error', 'Order not found.');
            return;
        }

        $this->view('checkout/confirmed', [
            'meta_title' => __('order_confirmed') . ' — EveryVeda',
            'order' => $trackingData['order'],
            'items' => $trackingData['items'],
            'trackingToken' => $token
        ]);
    }
}
