<?php
/**
 * Inventory Management & Overselling Prevention Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class InventoryService
{
    /**
     * Deduct stock for order items atomically with overselling prevention
     */
    public static function deductStockForOrder(int $orderId): void
    {
        $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = :oid", ['oid' => $orderId]);

        foreach ($items as $item) {
            $qty = (int)$item['quantity'];

            if (!empty($item['variant_id'])) {
                // Deduct from variant
                $stmt = Database::query(
                    "UPDATE product_variants 
                     SET stock_quantity = stock_quantity - :qty 
                     WHERE id = :vid AND stock_quantity >= :qty",
                    ['qty' => $qty, 'vid' => $item['variant_id']]
                );

                if ($stmt->rowCount() === 0) {
                    throw new \RuntimeException("Insufficient stock available for variant SKU: {$item['sku']}");
                }

                // Also decrement parent product total stock
                Database::query(
                    "UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :pid",
                    ['qty' => $qty, 'pid' => $item['product_id']]
                );

                Database::insert('inventory_movements', [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'order_id' => $orderId,
                    'movement_type' => 'order_fulfilled',
                    'quantity' => -$qty,
                    'notes' => "Deduction for Order #{$orderId}"
                ]);
            } else {
                // Deduct from base product
                $stmt = Database::query(
                    "UPDATE products 
                     SET stock_quantity = stock_quantity - :qty 
                     WHERE id = :pid AND stock_quantity >= :qty",
                    ['qty' => $qty, 'pid' => $item['product_id']]
                );

                if ($stmt->rowCount() === 0) {
                    throw new \RuntimeException("Insufficient stock available for product SKU: {$item['sku']}");
                }

                Database::insert('inventory_movements', [
                    'product_id' => $item['product_id'],
                    'variant_id' => null,
                    'order_id' => $orderId,
                    'movement_type' => 'order_fulfilled',
                    'quantity' => -$qty,
                    'notes' => "Deduction for Order #{$orderId}"
                ]);
            }
        }
    }

    /**
     * Restore stock when order is cancelled or refunded
     */
    public static function restoreStockForOrder(int $orderId, string $reason = 'Order cancelled'): void
    {
        $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = :oid", ['oid' => $orderId]);

        foreach ($items as $item) {
            $qty = (int)$item['quantity'];

            if (!empty($item['variant_id'])) {
                Database::query(
                    "UPDATE product_variants SET stock_quantity = stock_quantity + :qty WHERE id = :vid",
                    ['qty' => $qty, 'vid' => $item['variant_id']]
                );
                Database::query(
                    "UPDATE products SET stock_quantity = stock_quantity + :qty WHERE id = :pid",
                    ['qty' => $qty, 'pid' => $item['product_id']]
                );

                Database::insert('inventory_movements', [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'order_id' => $orderId,
                    'movement_type' => 'order_cancelled',
                    'quantity' => $qty,
                    'notes' => $reason
                ]);
            } else {
                Database::query(
                    "UPDATE products SET stock_quantity = stock_quantity + :qty WHERE id = :pid",
                    ['qty' => $qty, 'pid' => $item['product_id']]
                );

                Database::insert('inventory_movements', [
                    'product_id' => $item['product_id'],
                    'variant_id' => null,
                    'order_id' => $orderId,
                    'movement_type' => 'order_cancelled',
                    'quantity' => $qty,
                    'notes' => $reason
                ]);
            }
        }
    }

    /**
     * Get low stock products
     */
    public static function getLowStockProducts(): array
    {
        return Database::fetchAll(
            "SELECT p.id, p.sku, p.stock_quantity, p.low_stock_threshold, pt.name 
             FROM products p
             LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = 'en')
             WHERE p.manage_stock = 1 AND p.stock_quantity <= p.low_stock_threshold AND p.status = 'published'
             ORDER BY p.stock_quantity ASC"
        );
    }
}
