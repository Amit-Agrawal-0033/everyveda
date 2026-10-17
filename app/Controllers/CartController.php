<?php
/**
 * Shopping Cart Controller (Web & AJAX JSON API)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\CartService;
use App\Services\CouponService;

class CartController extends Controller
{
    /**
     * View Shopping Cart
     */
    public function index(Request $request): void
    {
        $cart = CartService::getCartDetails();

        $this->view('cart/index', [
            'meta_title' => __('shopping_cart') . ' — EveryVeda',
            'cart' => $cart
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request): void
    {
        $this->validateCsrf($request);

        $productId = (int)$request->post('product_id');
        $variantId = $request->post('variant_id') ? (int)$request->post('variant_id') : null;
        $quantity = max(1, (int)$request->post('quantity', 1));
        $isBuyNow = (bool)$request->post('buy_now', false);

        try {
            $cart = CartService::addItem($productId, $variantId, $quantity);

            if ($request->isAjax()) {
                $this->json([
                    'success' => true,
                    'message' => 'Item added to your wellness cart!',
                    'cart' => $cart,
                    'redirect' => $isBuyNow ? url('/checkout') : null
                ]);
                return;
            }

            if ($isBuyNow) {
                $this->redirect(url('/checkout'));
                return;
            }

            $this->redirect(url('/cart'), 'success', 'Item added to your cart!');
        } catch (\Throwable $e) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 400);
                return;
            }
            $this->redirect($_SERVER['HTTP_REFERER'] ?? url('/cart'), 'error', $e->getMessage());
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request): void
    {
        $this->validateCsrf($request);

        $itemId = (int)$request->post('item_id');
        $quantity = (int)$request->post('quantity', 1);

        try {
            $cart = CartService::updateItem($itemId, $quantity);

            if ($request->isAjax()) {
                $this->json(['success' => true, 'cart' => $cart]);
                return;
            }

            $this->redirect(url('/cart'), 'success', 'Cart updated successfully.');
        } catch (\Throwable $e) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 400);
                return;
            }
            $this->redirect(url('/cart'), 'error', $e->getMessage());
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request): void
    {
        $this->validateCsrf($request);

        $itemId = (int)$request->post('item_id');
        $cart = CartService::removeItem($itemId);

        if ($request->isAjax()) {
            $this->json(['success' => true, 'cart' => $cart]);
            return;
        }

        $this->redirect(url('/cart'), 'info', 'Item removed from cart.');
    }

    /**
     * Apply coupon discount
     */
    public function applyCoupon(Request $request): void
    {
        $this->validateCsrf($request);

        $code = trim((string)$request->post('coupon_code', ''));
        if (empty($code)) {
            $this->redirect(url('/cart'), 'error', 'Please enter a coupon code.');
            return;
        }

        $cart = CartService::getCartDetails();
        $userId = auth_user()['id'] ?? null;

        $result = CouponService::validateCoupon($code, $cart['subtotal'], $userId);

        if (!$result['valid']) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'error' => $result['message']], 400);
                return;
            }
            $this->redirect(url('/cart'), 'error', $result['message']);
            return;
        }

        $_SESSION['applied_coupon'] = strtoupper($code);

        if ($request->isAjax()) {
            $updatedCart = CartService::getCartDetails();
            $this->json(['success' => true, 'message' => $result['message'], 'cart' => $updatedCart]);
            return;
        }

        $this->redirect(url('/cart'), 'success', $result['message']);
    }

    /**
     * Remove applied coupon
     */
    public function removeCoupon(Request $request): void
    {
        $this->validateCsrf($request);

        unset($_SESSION['applied_coupon']);

        if ($request->isAjax()) {
            $cart = CartService::getCartDetails();
            $this->json(['success' => true, 'cart' => $cart]);
            return;
        }

        $this->redirect(url('/cart'), 'info', 'Coupon removed.');
    }

    /**
     * Get Cart count JSON
     */
    public function count(Request $request): void
    {
        $this->json(['count' => CartService::getItemCount()]);
    }
}
