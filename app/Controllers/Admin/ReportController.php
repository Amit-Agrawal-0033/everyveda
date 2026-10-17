<?php
/**
 * Admin Reports & Analytics Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function index(Request $request): void
    {
        $this->adminView('admin/reports/index', [
            'meta_title' => 'Downloadable Reports — EveryVeda Admin'
        ]);
    }

    public function ordersCsv(Request $request): void
    {
        ReportService::exportOrders(
            $request->get('start_date'),
            $request->get('end_date'),
            $request->get('status')
        );
    }

    public function inventoryCsv(Request $request): void
    {
        ReportService::exportInventory();
    }

    public function customersCsv(Request $request): void
    {
        ReportService::exportCustomers();
    }
}
