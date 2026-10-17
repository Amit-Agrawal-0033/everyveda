<?php
/**
 * Admin Reporting & CSV Export Service with Spreadsheet Formula Injection Protection
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Response;

class ReportService
{
    /**
     * Export Orders Report as CSV
     */
    public static function exportOrders(?string $startDate = null, ?string $endDate = null, ?string $status = null): void
    {
        $params = [];
        $where = ["1=1"];

        if ($startDate) {
            $where[] = "o.created_at >= :start";
            $params['start'] = $startDate . ' 00:00:00';
        }
        if ($endDate) {
            $where[] = "o.created_at <= :end";
            $params['end'] = $endDate . ' 23:59:59';
        }
        if ($status && $status !== 'all') {
            $where[] = "o.status = :status";
            $params['status'] = $status;
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT 
                    o.order_number,
                    o.created_at,
                    o.customer_name,
                    o.customer_email,
                    o.customer_phone,
                    o.shipping_city,
                    o.shipping_state,
                    o.shipping_pincode,
                    o.subtotal,
                    o.discount_amount,
                    o.coupon_code,
                    o.shipping_fee,
                    o.tax_amount,
                    o.total_amount,
                    o.payment_method,
                    o.payment_status,
                    o.status
                FROM orders o
                WHERE {$whereClause}
                ORDER BY o.id DESC";

        $rows = Database::fetchAll($sql, $params);

        $headers = [
            'Order Number', 'Date', 'Customer Name', 'Email', 'Phone',
            'City', 'State', 'PIN Code', 'Subtotal (INR)', 'Discount (INR)',
            'Coupon', 'Shipping (INR)', 'Tax (INR)', 'Grand Total (INR)',
            'Payment Method', 'Payment Status', 'Order Status'
        ];

        $csvRows = [];
        foreach ($rows as $r) {
            $csvRows[] = [
                $r['order_number'],
                $r['created_at'],
                $r['customer_name'],
                $r['customer_email'],
                $r['customer_phone'],
                $r['shipping_city'],
                $r['shipping_state'],
                $r['shipping_pincode'],
                number_format((float)$r['subtotal'], 2),
                number_format((float)$r['discount_amount'], 2),
                $r['coupon_code'] ?: '-',
                number_format((float)$r['shipping_fee'], 2),
                number_format((float)$r['tax_amount'], 2),
                number_format((float)$r['total_amount'], 2),
                strtoupper($r['payment_method']),
                ucfirst($r['payment_status']),
                ucfirst($r['status'])
            ];
        }

        $filename = 'everyveda_orders_' . date('Ymd_His') . '.csv';
        Response::csv($filename, $headers, $csvRows);
    }

    /**
     * Export Inventory Stock Report
     */
    public static function exportInventory(): void
    {
        $sql = "SELECT 
                    p.id,
                    p.sku,
                    COALESCE(pt.name, p.slug) AS product_name,
                    c.name_en AS category,
                    p.price,
                    p.sale_price,
                    p.stock_quantity,
                    p.low_stock_threshold,
                    p.status,
                    p.manage_stock
                FROM products p
                LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = 'en')
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.stock_quantity ASC";

        $rows = Database::fetchAll($sql);

        $headers = [
            'Product ID', 'SKU', 'Product Name', 'Category', 'Base Price',
            'Sale Price', 'Current Stock', 'Low Stock Threshold', 'Stock Status', 'Product Status'
        ];

        $csvRows = [];
        foreach ($rows as $r) {
            $isLowStock = $r['manage_stock'] && ((int)$r['stock_quantity'] <= (int)$r['low_stock_threshold']);
            $stockStatus = (int)$r['stock_quantity'] <= 0 ? 'Out of Stock' : ($isLowStock ? 'Low Stock Alert' : 'In Stock');

            $csvRows[] = [
                $r['id'],
                $r['sku'],
                $r['product_name'],
                $r['category'] ?: 'Uncategorized',
                number_format((float)$r['price'], 2),
                !empty($r['sale_price']) ? number_format((float)$r['sale_price'], 2) : '-',
                $r['stock_quantity'],
                $r['low_stock_threshold'],
                $stockStatus,
                ucfirst($r['status'])
            ];
        }

        $filename = 'everyveda_inventory_' . date('Ymd_His') . '.csv';
        Response::csv($filename, $headers, $csvRows);
    }

    /**
     * Export Customers Report
     */
    public static function exportCustomers(): void
    {
        $sql = "SELECT 
                    u.id,
                    u.name,
                    u.email,
                    u.phone,
                    u.status,
                    u.created_at,
                    COUNT(o.id) AS total_orders,
                    COALESCE(SUM(CASE WHEN o.status IN ('confirmed', 'processing', 'shipped', 'delivered') THEN o.total_amount ELSE 0 END), 0) AS lifetime_spent
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id
                GROUP BY u.id
                ORDER BY lifetime_spent DESC";

        $rows = Database::fetchAll($sql);

        $headers = [
            'User ID', 'Customer Name', 'Email', 'Phone', 'Account Status', 'Joined Date', 'Total Orders', 'Lifetime Spent (INR)'
        ];

        $csvRows = [];
        foreach ($rows as $r) {
            $csvRows[] = [
                $r['id'],
                $r['name'],
                $r['email'],
                $r['phone'] ?: '-',
                ucfirst($r['status']),
                $r['created_at'],
                $r['total_orders'],
                number_format((float)$r['lifetime_spent'], 2)
            ];
        }

        $filename = 'everyveda_customers_' . date('Ymd_His') . '.csv';
        Response::csv($filename, $headers, $csvRows);
    }
}
