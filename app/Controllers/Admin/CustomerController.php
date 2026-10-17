<?php
/**
 * Admin Customer Management Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\ReportService;

class CustomerController extends Controller
{
    public function index(Request $request): void
    {
        $q = trim((string)$request->get('q', ''));
        $where = ["1=1"];
        $params = [];

        if ($q !== '') {
            $where[] = "(u.name LIKE :q OR u.email LIKE :q OR u.phone LIKE :q)";
            $params['q'] = '%' . $q . '%';
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    u.*, 
                    COUNT(o.id) AS order_count,
                    COALESCE(SUM(CASE WHEN o.status IN ('confirmed', 'processing', 'shipped', 'delivered') THEN o.total_amount ELSE 0 END), 0) AS total_spend
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id
                WHERE {$whereClause}
                GROUP BY u.id
                ORDER BY u.id DESC";

        $customers = Database::fetchAll($sql, $params);

        $this->adminView('admin/customers/index', [
            'meta_title' => 'Customers — EveryVeda Admin',
            'customers' => $customers,
            'q' => $q
        ]);
    }

    public function show(Request $request, string $id): void
    {
        $userId = (int)$id;
        $customer = Database::fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $userId]);

        if (!$customer) {
            $this->redirect(url('/admin/customers'), 'error', 'Customer not found.');
            return;
        }

        $orders = Database::fetchAll("SELECT * FROM orders WHERE user_id = :uid ORDER BY id DESC", ['uid' => $userId]);
        $addresses = Database::fetchAll("SELECT * FROM customer_addresses WHERE user_id = :uid", ['uid' => $userId]);

        $this->adminView('admin/customers/show', [
            'meta_title' => "Customer: {$customer['name']} — EveryVeda Admin",
            'customer' => $customer,
            'orders' => $orders,
            'addresses' => $addresses
        ]);
    }

    public function export(Request $request): void
    {
        ReportService::exportCustomers();
    }
}
