<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Business Reports & CSV Exports</h1>
        <p class="text-muted small mb-0">Export sanitized, spreadsheet-ready financial, sales, inventory, and customer data.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Orders & Sales Report -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-cart-check fs-4 text-primary me-2"></i>
                <h5 class="card-title mb-0">Orders & GST Sales Report</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small">
                    Exports complete order breakdowns including subtotal, coupon discounts, shipping charges, CGST, SGST, IGST tax breakdown, payment method, transaction status, and timestamps.
                </p>
                <form action="<?= url('/admin/reports/orders.csv') ?>" method="GET">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-01') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Order Status Filter</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="bi bi-cloud-arrow-down me-2"></i> Download Orders CSV
                    </button>
                </form>
            </div>
            <div class="card-footer bg-light py-2 small text-muted">
                <i class="bi bi-shield-check text-success me-1"></i> Formula injection protected (=, +, -, @ sanitized)
            </div>
        </div>
    </div>

    <!-- Inventory & Stock Valuation Report -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-boxes fs-4 text-success me-2"></i>
                <h5 class="card-title mb-0">Inventory & Stock Valuation Report</h5>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <p class="text-muted small">
                        Real-time snapshot of product catalog, pack size variants, stock keeping units (SKUs), selling prices, active stock on hand, reserved inventory, and stock health alerts.
                    </p>
                    <ul class="list-unstyled small text-secondary mb-4">
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Includes all active & inactive catalog items</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Highlights units below low-stock alert thresholds</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Pack size variant breakdown with specific SKUs</li>
                    </ul>
                </div>
                <div>
                    <a href="<?= url('/admin/reports/inventory.csv') ?>" class="btn btn-success w-100 py-2 shadow-sm">
                        <i class="bi bi-cloud-arrow-down me-2"></i> Download Inventory CSV
                    </a>
                </div>
            </div>
            <div class="card-footer bg-light py-2 small text-muted">
                <i class="bi bi-shield-check text-success me-1"></i> Updated in real-time from active database
            </div>
        </div>
    </div>

    <!-- Customers & Lifetime Spend Report -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-people fs-4 text-info me-2"></i>
                <h5 class="card-title mb-0">Customer Analytics & Spend Report</h5>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <p class="text-muted small">
                        Complete export of customer accounts, contact details, total orders placed, lifetime order value, and registration dates for marketing and customer service reconciliations.
                    </p>
                    <ul class="list-unstyled small text-secondary mb-4">
                        <li class="mb-2"><i class="bi bi-check2 text-info me-2"></i>Customer name, email address, and mobile phone</li>
                        <li class="mb-2"><i class="bi bi-check2 text-info me-2"></i>Total fulfilled orders count</li>
                        <li class="mb-2"><i class="bi bi-check2 text-info me-2"></i>Cumulative lifetime expenditure in INR</li>
                    </ul>
                </div>
                <div>
                    <a href="<?= url('/admin/reports/customers.csv') ?>" class="btn btn-outline-primary w-100 py-2">
                        <i class="bi bi-cloud-arrow-down me-2"></i> Download Customers CSV
                    </a>
                </div>
            </div>
            <div class="card-footer bg-light py-2 small text-muted">
                <i class="bi bi-shield-check text-success me-1"></i> Respects privacy standards and data safety
            </div>
        </div>
    </div>

    <!-- Security & Compliance Guidelines -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100 bg-light">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-shield-lock fs-4 text-warning me-2"></i>
                <h5 class="card-title mb-0">Export Security & Compliance</h5>
            </div>
            <div class="card-body p-4">
                <h6 class="fw-bold mb-2">CSV Injection Prevention</h6>
                <p class="text-muted small mb-3">
                    Every exported cell beginning with formula prefix characters (<code>=</code>, <code>+</code>, <code>-</code>, <code>@</code>, tab, or carriage return) is automatically prefixed with a single quote (<code>'</code>). This neutralizes malicious formula execution when opened in Microsoft Excel or Google Sheets.
                </p>
                <h6 class="fw-bold mb-2">Role-Based Access Control</h6>
                <p class="text-muted small mb-0">
                    Reports access is restricted strictly to Super Administrators and authorized administrative roles. All export requests are logged with user ID, IP address, and timestamp in the security audit ledger.
                </p>
            </div>
        </div>
    </div>
</div>
