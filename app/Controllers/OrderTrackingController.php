<?php
/**
 * Public Order Tracking Controller (Privacy-Preserving)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Order;

class OrderTrackingController extends Controller
{
    /**
     * Show tracking lookup form or tracking timeline if token provided
     */
    public function index(Request $request): void
    {
        $token = trim((string)$request->get('ref', ''));
        $orderNumber = trim((string)$request->get('order_number', ''));

        $trackingData = null;

        if (!empty($token)) {
            $trackingData = Order::findByTrackingToken($token);
        } elseif (!empty($orderNumber)) {
            // Find order by order number and get its tracking token
            $order = Database::fetchOne("SELECT tracking_token FROM orders WHERE order_number = :onum", ['onum' => $orderNumber]);
            if ($order) {
                $trackingData = Order::findByTrackingToken($order['tracking_token']);
            }
        }

        $this->view('orders/track', [
            'meta_title' => __('track_order') . ' — EveryVeda',
            'trackingData' => $trackingData,
            'searched' => !empty($token) || !empty($orderNumber)
        ]);
    }
}
