<?php
/**
 * Admin Product Review Moderation Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;

class ReviewController extends Controller
{
    public function index(Request $request): void
    {
        $status = $request->get('status', 'pending');
        $params = [];
        $where = "1=1";

        if ($status !== 'all') {
            $where = "r.status = :status";
            $params['status'] = $status;
        }

        $reviews = Database::fetchAll(
            "SELECT r.*, p.slug AS product_slug, pt.name AS product_name 
             FROM product_reviews r
             JOIN products p ON r.product_id = p.id
             LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = 'en')
             WHERE {$where}
             ORDER BY r.id DESC",
            $params
        );

        $this->adminView('admin/reviews/index', [
            'meta_title' => 'Customer Reviews Moderation — EveryVeda Admin',
            'reviews' => $reviews,
            'currentStatus' => $status
        ]);
    }

    public function updateStatus(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $reviewId = (int)$id;
        $status = (string)$request->post('status');

        if (in_array($status, ['approved', 'rejected', 'pending'], true)) {
            Database::update('product_reviews', ['status' => $status], "id = :id", ['id' => $reviewId]);
            $this->redirect(url('/admin/reviews'), 'success', "Review marked as {$status}.");
            return;
        }

        $this->redirect(url('/admin/reviews'), 'error', 'Invalid status.');
    }

    public function delete(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        Database::delete('product_reviews', "id = :id", ['id' => (int)$id]);
        $this->redirect(url('/admin/reviews'), 'info', 'Review deleted.');
    }
}
