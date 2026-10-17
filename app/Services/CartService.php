<?php
/**
 * Shopping Cart Service (Guest & Persistent Customer Cart, Variant-Aware, Server-Side Recalculation)
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class CartService
{
    /**
     * Get or create active cart ID
     */
    public static function getCartId(): int
    {
        $userId = auth_user()['id'] ?? null;
        $sessionId = session_id();

        if ($userId) {
            $cart = Database::fetchOne("SELECT id FROM carts WHERE user_id = :uid", ['uid' => $userId]);
            if ($cart) {
                return (int)$cart['id'];
            }
            return Database::insert('carts', [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $cart = Database::fetchOne("SELECT id FROM carts WHERE session_id = :sid AND user_id IS NULL", ['sid' => $sessionId]);
        if ($cart) {
            return (int)$cart['id'];
        }

        return Database::insert('carts', [
            'user_id' => null,
            'session_id' => $sessionId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Add item to cart with stock validation
     */
    public static function addItem(int $productId, ?int $variantId = null, int $quantity = 1): array
    {
        $cartId = self::getCartId();
        $quantity = max(1, $quantity);

        // Fetch product & variant
        $product = Database::fetchOne("SELECT * FROM products WHERE id = :id AND status = 'published'", ['id' => $productId]);
        if (!$product) {
            throw new \InvalidArgumentException("Product not found or unavailable.");
        }

        $availableStock = (int)$product['stock_quantity'];
        if ($variantId) {
            $variant = Database::fetchOne(
                "SELECT * FROM product_variants WHERE id = :vid AND product_id = :pid AND is_active = 1",
                ['vid' => $variantId, 'pid' => $productId]
            );
            if (!$variant) {
                throw new \InvalidArgumentException("Selected pack size or variant is invalid.");
            }
            $availableStock = (int)$variant['stock_quantity'];
        }

        // Check current quantity in cart
        $existing = Database::fetchOne(
            "SELECT id, quantity FROM cart_items 
             WHERE cart_id = :cid AND product_id = :pid AND " . ($variantId ? "variant_id = :vid" : "variant_id IS NULL"),
            $variantId ? ['cid' => $cartId, 'pid' => $productId, 'vid' => $variantId] : ['cid' => $cartId, 'pid' => $productId]
        );

        $newQty = $existing ? ((int)$existing['quantity'] + $quantity) : $quantity;

        if ($product['manage_stock'] && $newQty > $availableStock) {
            throw new \RuntimeException("Only {$availableStock} unit(s) available in stock.");
        }

        if ($existing) {
            Database::update('cart_items', [
                'quantity' => $newQty,
                'updated_at' => date('Y-m-d H:i:s')
            ], "id = :id", ['id' => $existing['id']]);
        } else {
            Database::insert('cart_items', [
                'cart_id' => $cartId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return self::getCartDetails();
    }

    /**
     * Update item quantity in cart
     */
    public static function updateItem(int $cartItemId, int $quantity): array
    {
        $cartId = self::getCartId();

        $item = Database::fetchOne(
            "SELECT ci.*, p.stock_quantity AS p_stock, p.manage_stock, pv.stock_quantity AS v_stock 
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             LEFT JOIN product_variants pv ON ci.variant_id = pv.id
             WHERE ci.id = :id AND ci.cart_id = :cid",
            ['id' => $cartItemId, 'cid' => $cartId]
        );

        if (!$item) {
            throw new \InvalidArgumentException("Cart item not found.");
        }

        if ($quantity <= 0) {
            return self::removeItem($cartItemId);
        }

        $availableStock = $item['variant_id'] ? (int)$item['v_stock'] : (int)$item['p_stock'];
        if ($item['manage_stock'] && $quantity > $availableStock) {
            throw new \RuntimeException("Only {$availableStock} unit(s) available in stock.");
        }

        Database::update('cart_items', [
            'quantity' => $quantity,
            'updated_at' => date('Y-m-d H:i:s')
        ], "id = :id", ['id' => $cartItemId]);

        return self::getCartDetails();
    }

    /**
     * Remove item from cart
     */
    public static function removeItem(int $cartItemId): array
    {
        $cartId = self::getCartId();
        Database::delete('cart_items', "id = :id AND cart_id = :cid", ['id' => $cartItemId, 'cid' => $cartId]);
        return self::getCartDetails();
    }

    /**
     * Clear all items in cart
     */
    public static function clearCart(): void
    {
        $cartId = self::getCartId();
        Database::delete('cart_items', "cart_id = :cid", ['cid' => $cartId]);
        unset($_SESSION['applied_coupon']);
    }

    /**
     * Get total quantity of items in active cart
     */
    public static function getItemCount(): int
    {
        $cartId = self::getCartId();
        $count = Database::fetchColumn(
            "SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE cart_id = :cid",
            ['cid' => $cartId]
        );
        return (int)$count;
    }

    /**
     * Get complete validated cart details with fresh database prices, discounts, shipping, tax
     */
    public static function getCartDetails(): array
    {
        $cartId = self::getCartId();
        $locale = current_locale();

        $rows = Database::fetchAll(
            "SELECT 
                ci.id AS item_id,
                ci.quantity,
                p.id AS product_id,
                p.sku AS product_sku,
                p.slug AS product_slug,
                p.price AS base_product_price,
                p.sale_price AS base_product_sale_price,
                p.stock_quantity AS product_stock,
                p.manage_stock,
                p.tax_rate,
                p.weight_grams,
                p.primary_image,
                COALESCE(pt.name, p.slug) AS product_name,
                pv.id AS variant_id,
                pv.sku AS variant_sku,
                pv.variant_name_en,
                pv.variant_name_hi,
                pv.price AS variant_price,
                pv.sale_price AS variant_sale_price,
                pv.stock_quantity AS variant_stock
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             LEFT JOIN product_translations pt ON (pt.product_id = p.id AND pt.locale = :locale)
             LEFT JOIN product_variants pv ON ci.variant_id = pv.id
             WHERE ci.cart_id = :cid AND p.status = 'published'",
            ['cid' => $cartId, 'locale' => $locale]
        );

        $items = [];
        $subtotal = 0.0;
        $totalWeight = 0;

        foreach ($rows as $r) {
            // Determine price snapshot: variant sale price > variant price > product sale price > product price
            $unitPrice = (float)$r['base_product_price'];
            if (!empty($r['base_product_sale_price']) && (float)$r['base_product_sale_price'] > 0) {
                $unitPrice = (float)$r['base_product_sale_price'];
            }

            if (!empty($r['variant_id'])) {
                $unitPrice = (float)$r['variant_price'];
                if (!empty($r['variant_sale_price']) && (float)$r['variant_sale_price'] > 0) {
                    $unitPrice = (float)$r['variant_sale_price'];
                }
            }

            $qty = (int)$r['quantity'];
            $lineTotal = round($unitPrice * $qty, 2);
            $subtotal += $lineTotal;
            $totalWeight += ((int)$r['weight_grams']) * $qty;

            $items[] = [
                'item_id' => (int)$r['item_id'],
                'product_id' => (int)$r['product_id'],
                'variant_id' => $r['variant_id'] ? (int)$r['variant_id'] : null,
                'name' => $r['product_name'],
                'variant_name' => $locale === 'hi' ? ($r['variant_name_hi'] ?: $r['variant_name_en']) : ($r['variant_name_en'] ?: $r['variant_name_hi']),
                'sku' => $r['variant_sku'] ?: $r['product_sku'],
                'slug' => $r['product_slug'],
                'image' => $r['primary_image'] ?: 'placeholder.svg',
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'line_total' => $lineTotal,
                'tax_rate' => (float)$r['tax_rate'],
                'stock' => $r['variant_id'] ? (int)$r['variant_stock'] : (int)$r['product_stock']
            ];
        }

        // Apply Coupon Discount
        $discountAmount = 0.0;
        $couponCode = $_SESSION['applied_coupon'] ?? null;
        $couponData = null;

        if ($couponCode) {
            $couponResult = CouponService::validateCoupon($couponCode, $subtotal, auth_user()['id'] ?? null);
            if ($couponResult['valid']) {
                $discountAmount = $couponResult['discount'];
                $couponData = $couponResult['coupon'];
            } else {
                unset($_SESSION['applied_coupon']); // Invalidate if cart changes make it invalid
            }
        }

        $taxableAmount = max(0, $subtotal - $discountAmount);

        // Shipping Calculation
        $shippingThreshold = (float)env('SHIPPING_FREE_THRESHOLD', 499.00);
        $standardShippingFee = (float)env('SHIPPING_STANDARD_FEE', 50.00);

        $shippingFee = ($subtotal >= $shippingThreshold || count($items) === 0) ? 0.00 : $standardShippingFee;

        // In India Ayurvedic consumer products, MRP is tax-inclusive. We compute the tax breakdown (GST 12%)
        $taxRate = (float)env('GST_TAX_RATE', 12.00);
        // Tax = TaxableAmount - (TaxableAmount / (1 + TaxRate/100))
        $taxAmount = round($taxableAmount - ($taxableAmount / (1 + ($taxRate / 100))), 2);

        $finalTotal = round($taxableAmount + $shippingFee, 2);

        return [
            'items' => $items,
            'count' => array_sum(array_column($items, 'quantity')),
            'subtotal' => round($subtotal, 2),
            'discount' => round($discountAmount, 2),
            'coupon_code' => $couponCode,
            'coupon' => $couponData,
            'shipping_fee' => $shippingFee,
            'shipping_threshold' => $shippingThreshold,
            'free_shipping_remaining' => max(0, round($shippingThreshold - $subtotal, 2)),
            'tax_amount' => $taxAmount,
            'tax_rate' => $taxRate,
            'total' => $finalTotal
        ];
    }

    /**
     * Merge guest cart into registered user cart upon sign-in
     */
    public static function mergeGuestCartIntoUser(int $userId): void
    {
        $sessionId = session_id();
        $guestCart = Database::fetchOne("SELECT id FROM carts WHERE session_id = :sid AND user_id IS NULL", ['sid' => $sessionId]);
        if (!$guestCart) {
            return;
        }

        $userCartId = self::getCartId(); // Will fetch or create user cart
        $guestItems = Database::fetchAll("SELECT * FROM cart_items WHERE cart_id = :cid", ['cid' => $guestCart['id']]);

        foreach ($guestItems as $item) {
            try {
                self::addItem((int)$item['product_id'], $item['variant_id'] ? (int)$item['variant_id'] : null, (int)$item['quantity']);
            } catch (\Throwable $e) {
                // Ignore stock exceptions during auto-merge
            }
        }

        // Clean up guest cart
        Database::delete('cart_items', "cart_id = :cid", ['cid' => $guestCart['id']]);
        Database::delete('carts', "id = :cid", ['cid' => $guestCart['id']]);
    }
}
