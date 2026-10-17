<?php
/**
 * Admin Order Management Controller (Workflow State Machine, Shipments, Invoices, CSV)
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Order;
use App\Services\AdminAuthService;
use App\Services\InvoiceService;
use App\Services\ReportService;

class OrderController extends Controller
{
    /**
     * List Orders with Filters & Search
     */
    public function index(Request $request): void
    {
        $filters = [
            'q' => $request->get('q'),
            'status' => $request->get('status'),
            'payment_status' => $request->get('payment_status'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ];

        $page = max(1, (int)$request->get('page', 1));
        $result = Order::adminFilter($filters, $page, 15);

        $this->adminView('admin/orders/index', [
            'meta_title' => 'Orders Management — EveryVeda Admin',
            'orders' => $result['orders'],
            'total' => $result['total'],
            'page' => $result['page'],
            'totalPages' => $result['total_pages'],
            'filters' => $filters
        ]);
    }

    /**
     * Show Order Detail
     */
    public function show(Request $request, string $id): void
    {
        $orderId = (int)$id;
        $order = Database::fetchOne("SELECT * FROM orders WHERE id = :id", ['id' => $orderId]);

        if (!$order) {
            $this->redirect(url('/admin/orders'), 'error', 'Order not found.');
            return;
        }

        $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = :oid", ['oid' => $orderId]);
        $payments = Database::fetchAll("SELECT * FROM payments WHERE order_id = :oid ORDER BY id DESC", ['oid' => $orderId]);
        $shipments = Database::fetchAll("SELECT * FROM shipments WHERE order_id = :oid ORDER BY id DESC", ['oid' => $orderId]);
        $timeline = Database::fetchAll("SELECT * FROM order_status_history WHERE order_id = :oid ORDER BY id DESC", ['oid' => $orderId]);

        $this->adminView('admin/orders/show', [
            'meta_title' => "Order #{$order['order_number']} — EveryVeda Admin",
            'order' => $order,
            'items' => $items,
            'payments' => $payments,
            'shipments' => $shipments,
            'timeline' => $timeline
        ]);
    }

    /**
     * Update Order Status
     */
    public function updateStatus(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $orderId = (int)$id;

        $newStatus = (string)$request->post('status', '');
        $comment = trim((string)$request->post('comment', ''));
        $adminId = (int)admin_user()['id'];

        try {
            Order::updateStatus($orderId, $newStatus, $comment, $adminId);
            AdminAuthService::logAudit($adminId, 'update_order_status', 'orders', $orderId, "Status changed to {$newStatus}");

            $this->redirect(url('/admin/orders/' . $orderId), 'success', "Order status successfully updated to {$newStatus}.");
        } catch (\Throwable $e) {
            $this->redirect(url('/admin/orders/' . $orderId), 'error', $e->getMessage());
        }
    }

    /**
     * Record Courier Shipment
     */
    public function recordShipment(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $orderId = (int)$id;

        $errors = $this->validate($request->all(), [
            'courier_name' => 'required|min:2|max:100',
            'tracking_number' => 'required|min:3|max:100'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/admin/orders/' . $orderId), 'error', reset($errors));
            return;
        }

        $courierName = (string)$request->post('courier_name');
        $trackingNumber = (string)$request->post('tracking_number');
        $trackingUrl = (string)$request->post('tracking_url', '');
        $adminId = (int)admin_user()['id'];

        try {
            Order::recordShipment($orderId, $courierName, $trackingNumber, $trackingUrl, $adminId);
            AdminAuthService::logAudit($adminId, 'record_shipment', 'orders', $orderId, "Dispatched via {$courierName} ({$trackingNumber})");

            $this->redirect(url('/admin/orders/' . $orderId), 'success', 'Shipment recorded and customer notified.');
        } catch (\Throwable $e) {
            $this->redirect(url('/admin/orders/' . $orderId), 'error', $e->getMessage());
        }
    }

    /**
     * Print / Download Tax Invoice
     */
    public function invoice(Request $request, string $id): void
    {
        echo InvoiceService::generateHtml((int)$id);
        exit;
    }

    /**
     * Export Orders to CSV
     */
    public function export(Request $request): void
    {
        ReportService::exportOrders(
            $request->get('start_date'),
            $request->get('end_date'),
            $request->get('status')
        );
    }
}
