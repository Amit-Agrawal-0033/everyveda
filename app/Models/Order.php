<?php
/**
 * Order Data Model & Status Workflow State Machine
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Services\InventoryService;
use App\Services\MailerService;
use RuntimeException;

class Order
{
    /**
     * Allowed State Transitions:
     * pending -> confirmed, cancelled
     * confirmed -> processing, cancelled
     * processing -> shipped, cancelled
     * shipped -> delivered, cancelled
     * delivered -> refunded (via admin return procedure)
     * cancelled -> (terminal state)
     * refunded -> (terminal state)
     */
    private const ALLOWED_TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered', 'cancelled'],
        'delivered' => ['refunded'],
        'cancelled' => [],
        'refunded' => []
    ];

    /**
     * Public Order Tracking by Non-Sequential Token
     * Strictly avoids exposing private customer PII (address, phone, email, admin notes)
     */
    public static function findByTrackingToken(string $token): ?array
    {
        $order = Database::fetchOne(
            "SELECT 
                id, order_number, tracking_token, status, payment_method, payment_status,
                subtotal, discount_amount, shipping_fee, total_amount,
                shipping_city, shipping_state, shipping_pincode, created_at, updated_at
             FROM orders 
             WHERE tracking_token = :token",
            ['token' => $token]
        );

        if (!$order) {
            return null;
        }

        // Fetch order items (names and quantities, without sensitive financial metadata)
        $items = Database::fetchAll(
            "SELECT sku, product_name_en, product_name_hi, variant_name_en, variant_name_hi, quantity 
             FROM order_items WHERE order_id = :oid",
            ['oid' => $order['id']]
        );

        // Fetch shipment details
        $shipment = Database::fetchOne(
            "SELECT courier_name, tracking_number, tracking_url, shipped_at, delivered_at 
             FROM shipments WHERE order_id = :oid ORDER BY id DESC LIMIT 1",
            ['oid' => $order['id']]
        );

        // Fetch status history timeline
        $timeline = Database::fetchAll(
            "SELECT status, comment, created_at 
             FROM order_status_history WHERE order_id = :oid ORDER BY id ASC",
            ['oid' => $order['id']]
        );

        return [
            'order' => $order,
            'items' => $items,
            'shipment' => $shipment,
            'timeline' => $timeline
        ];
    }

    /**
     * Find order by ID ensuring customer authorization
     */
    public static function findForCustomer(int $orderId, int $userId): ?array
    {
        $order = Database::fetchOne(
            "SELECT * FROM orders WHERE id = :id AND user_id = :uid",
            ['id' => $orderId, 'uid' => $userId]
        );

        if (!$order) {
            return null;
        }

        $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = :oid", ['oid' => $orderId]);
        $shipment = Database::fetchOne("SELECT * FROM shipments WHERE order_id = :oid ORDER BY id DESC LIMIT 1", ['oid' => $orderId]);
        $timeline = Database::fetchAll("SELECT * FROM order_status_history WHERE order_id = :oid ORDER BY id ASC", ['oid' => $orderId]);

        return [
            'order' => $order,
            'items' => $items,
            'shipment' => $shipment,
            'timeline' => $timeline
        ];
    }

    /**
     * Admin Order Filtering with Pagination
     */
    public static function adminFilter(array $filters, int $page = 1, int $perPage = 15): array
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = "(order_number LIKE :q OR customer_name LIKE :q OR customer_email LIKE :q OR customer_phone LIKE :q)";
            $params['q'] = '%' . trim($filters['q']) . '%';
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['payment_status']) && $filters['payment_status'] !== 'all') {
            $where[] = "payment_status = :payment_status";
            $params['payment_status'] = $filters['payment_status'];
        }

        if (!empty($filters['start_date'])) {
            $where[] = "created_at >= :start_date";
            $params['start_date'] = $filters['start_date'] . ' 00:00:00';
        }

        if (!empty($filters['end_date'])) {
            $where[] = "created_at <= :end_date";
            $params['end_date'] = $filters['end_date'] . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $total = (int)Database::fetchColumn("SELECT COUNT(*) FROM orders WHERE {$whereClause}", $params);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $orders = Database::fetchAll(
            "SELECT * FROM orders WHERE {$whereClause} ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'orders' => $orders,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages
        ];
    }

    /**
     * Transition Order Status with State Machine & Audit Guard
     */
    public static function updateStatus(int $orderId, string $newStatus, string $comment = '', ?int $adminId = null): void
    {
        $order = Database::fetchOne("SELECT * FROM orders WHERE id = :id", ['id' => $orderId]);
        if (!$order) {
            throw new RuntimeException("Order not found.");
        }

        $currentStatus = $order['status'];
        if ($currentStatus === $newStatus) {
            return;
        }

        // Validate allowed transitions
        $allowed = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
        if (!in_array($newStatus, $allowed, true)) {
            throw new RuntimeException("Illegal status transition from '{$currentStatus}' to '{$newStatus}'.");
        }

        Database::transaction(function () use ($orderId, $order, $currentStatus, $newStatus, $comment, $adminId) {
            Database::update('orders', [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ], "id = :id", ['id' => $orderId]);

            // If order was cancelled or refunded, restore inventory
            if (in_array($newStatus, ['cancelled', 'refunded'], true) && !in_array($currentStatus, ['cancelled', 'refunded'], true)) {
                InventoryService::restoreStockForOrder($orderId, "Order {$newStatus}: {$comment}");
            }

            // Insert status history
            Database::insert('order_status_history', [
                'order_id' => $orderId,
                'status' => $newStatus,
                'comment' => $comment ?: "Status changed from {$currentStatus} to {$newStatus}",
                'created_by_admin' => $adminId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        });
    }

    /**
     * Add Courier Shipment Record
     */
    public static function recordShipment(int $orderId, string $courierName, string $trackingNumber, ?string $trackingUrl, ?int $adminId = null): void
    {
        Database::transaction(function () use ($orderId, $courierName, $trackingNumber, $trackingUrl, $adminId) {
            Database::insert('shipments', [
                'order_id' => $orderId,
                'courier_name' => trim($courierName),
                'tracking_number' => trim($trackingNumber),
                'tracking_url' => !empty($trackingUrl) ? trim($trackingUrl) : null,
                'shipped_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            self::updateStatus($orderId, 'shipped', "Shipped via {$courierName} (AWB: {$trackingNumber})", $adminId);

            MailerService::sendShipmentNotification($orderId, $courierName, $trackingNumber, $trackingUrl);
        });
    }
}
