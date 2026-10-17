<?php
/**
 * Admin Dashboard Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\InventoryService;

class DashboardController extends Controller
{
    public function index(Request $request): void
    {
        // 1. Total Orders
        $totalOrders = (int)Database::fetchColumn("SELECT COUNT(*) FROM orders");

        // 2. Total Net Sales (Strictly completed/captured sales)
        $totalSales = (float)Database::fetchColumn(
            "SELECT COALESCE(SUM(total_amount), 0) FROM orders 
             WHERE status IN ('confirmed', 'processing', 'shipped', 'delivered') 
             AND (payment_status = 'captured' OR (payment_method = 'cod' AND status = 'delivered'))"
        );

        // 3. Status Breakdowns
        $pendingOrders = (int)Database::fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
        $processingOrders = (int)Database::fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'processing'");
        $shippedOrders = (int)Database::fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'shipped'");
        $cancelledOrders = (int)Database::fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'");
        $refundedOrders = (int)Database::fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'refunded'");

        // 4. Payments stats
        $capturedPayments = (int)Database::fetchColumn("SELECT COUNT(*) FROM payments WHERE status = 'captured'");
        $failedPayments = (int)Database::fetchColumn("SELECT COUNT(*) FROM payments WHERE status = 'failed'");

        // 5. Total Active Products & Customers
        $totalProducts = (int)Database::fetchColumn("SELECT COUNT(*) FROM products WHERE status = 'published'");
        $totalCustomers = (int)Database::fetchColumn("SELECT COUNT(*) FROM users");

        // 6. Recent 8 Orders
        $recentOrders = Database::fetchAll(
            "SELECT id, order_number, customer_name, total_amount, payment_method, payment_status, status, created_at 
             FROM orders 
             ORDER BY id DESC LIMIT 8"
        );

        // 7. Low Stock Alerts
        $lowStockItems = InventoryService::getLowStockProducts();

        // 8. Top 5 Best Sellers
        $bestSellers = Database::fetchAll(
            "SELECT p.id, p.sku, COALESCE(pt.name, p.slug) AS name, p.stock_quantity, SUM(oi.quantity) AS total_sold
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = 'en')
             GROUP BY p.id
             ORDER BY total_sold DESC
             LIMIT 5"
        );

        $this->adminView('admin/dashboard/index', [
            'meta_title' => 'Dashboard — EveryVeda Admin',
            'stats' => [
                'total_orders' => $totalOrders,
                'total_sales' => $totalSales,
                'pending_orders' => $pendingOrders,
                'processing_orders' => $processingOrders,
                'shipped_orders' => $shippedOrders,
                'cancelled_orders' => $cancelledOrders,
                'refunded_orders' => $refundedOrders,
                'captured_payments' => $capturedPayments,
                'failed_payments' => $failedPayments,
                'total_products' => $totalProducts,
                'total_customers' => $totalCustomers
            ],
            'recentOrders' => $recentOrders,
            'lowStockItems' => $lowStockItems,
            'bestSellers' => $bestSellers
        ]);
    }
}
