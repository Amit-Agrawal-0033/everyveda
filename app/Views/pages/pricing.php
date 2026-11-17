<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-ev">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>"><?= __('home') ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Pricing & Tax Policy</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border shadow-sm p-4 p-md-5 bg-white rounded-3 mb-4">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold text-uppercase d-inline-block mb-3" style="width: fit-content;">
                    <i class="bi bi-shield-check me-1"></i> Transparent Indian E-Commerce
                </span>
                
                <h1 class="h2 fw-bold text-dark mb-3">Transparent Pricing & GST Policy</h1>
                <p class="lead text-muted mb-4">
                    At EveryVeda.com, we believe that trust is the foundation of genuine wellness. Every price displayed on our website is completely transparent, with zero hidden charges, fair shipping terms, and clear GST compliance.
                </p>

                <hr class="my-4">

                <!-- 1. Currency & Displayed Prices -->
                <div class="mb-5">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-currency-rupee text-success"></i> 1. Currency & Tax Inclusivity</h4>
                    <p class="text-muted">
                        All product prices, variant prices, and shipping charges displayed on EveryVeda.com are listed in <strong>Indian National Rupees (INR / ₹)</strong>.
                    </p>
                    <div class="alert alert-success border-success-subtle p-3 rounded">
                        <strong><i class="bi bi-check-circle-fill me-2"></i> All Product Prices Include GST:</strong>
                        The price you see on the product page and in your shopping cart is the exact price you pay for the product. All applicable Central GST (CGST), State GST (SGST), and Integrated GST (IGST) are already incorporated into the Maximum Retail Price (MRP). No unexpected tax surcharge will be added at checkout.
                    </div>
                </div>

                <!-- 2. GST Classification & HSN Table -->
                <div class="mb-5">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-receipt-cutoff text-success"></i> 2. Statutory GST Classification & HSN Codes</h4>
                    <p class="text-muted">
                        In accordance with the Goods and Services Tax (GST) Act of India, EveryVeda categorizes each herbal formulation strictly by its Harmonized System of Nomenclature (HSN) classification:
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Category & Product Type</th>
                                    <th>HSN Code</th>
                                    <th>Applicable GST Rate</th>
                                    <th>Transaction Tax Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Classical Ayurvedic Churnas</strong><br><small class="text-muted">e.g. Triphala Churna</small></td>
                                    <td><code>12119029</code></td>
                                    <td><span class="badge bg-success">5% GST</span></td>
                                    <td>Intrastate: 2.5% CGST + 2.5% SGST<br>Interstate: 5% IGST</td>
                                </tr>
                                <tr>
                                    <td><strong>Medicinal Rasayanas, Oils & Resins</strong><br><small class="text-muted">e.g. Royal Chyawanprash, Himalayan Shilajit, Joint Relief Oil, Ashwagandha</small></td>
                                    <td><code>30049011</code></td>
                                    <td><span class="badge bg-primary">12% GST</span></td>
                                    <td>Intrastate: 6% CGST + 6% SGST<br>Interstate: 12% IGST</td>
                                </tr>
                                <tr>
                                    <td><strong>Pure Botanical Juices</strong><br><small class="text-muted">e.g. Forest Amla Juice</small></td>
                                    <td><code>20098990</code></td>
                                    <td><span class="badge bg-primary">12% GST</span></td>
                                    <td>Intrastate: 6% CGST + 6% SGST<br>Interstate: 12% IGST</td>
                                </tr>
                                <tr>
                                    <td><strong>Ayurvedic Hair & Skin Elixirs</strong><br><small class="text-muted">e.g. Bhringraj Hair Oil, Kumkumadi Tailam</small></td>
                                    <td><code>33059010</code> / <code>33049990</code></td>
                                    <td><span class="badge bg-info text-dark">18% GST</span></td>
                                    <td>Intrastate: 9% CGST + 9% SGST<br>Interstate: 18% IGST</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Delivery & Shipping Charges -->
                <div class="mb-5">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-truck text-success"></i> 3. Delivery & Shipping Fee Policy</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border p-3 rounded h-100 bg-light">
                                <h6 class="fw-bold text-success mb-2">Orders of ₹499 and Above</h6>
                                <p class="text-muted small m-0">
                                    <strong>FREE Standard Delivery</strong> across all serviceable 19,000+ PIN codes in India. No minimum item count or hidden handling fees.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border p-3 rounded h-100 bg-light">
                                <h6 class="fw-bold text-dark mb-2">Orders Below ₹499</h6>
                                <p class="text-muted small m-0">
                                    A flat, subsidized shipping fee of <strong>₹50</strong> is applied to cover insured carrier transport and packaging costs.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Payment Gateway Security & Accepted Methods -->
                <div class="mb-5">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-lock-fill text-success"></i> 4. Payment Methods & Security</h4>
                    <p class="text-muted">
                        EveryVeda partners with <strong>Razorpay</strong>, an RBI-licensed payment aggregator, to process all online transactions through 256-bit SSL encrypted channels conforming to PCI-DSS Level 1 compliance. We support:
                    </p>
                    <ul class="text-muted small">
                        <li><strong>UPI:</strong> Google Pay, PhonePe, Paytm, BHIM, Cred, and any UPI ID / QR.</li>
                        <li><strong>Credit & Debit Cards:</strong> Visa, MasterCard, RuPay, Maestro, and American Express.</li>
                        <li><strong>Net Banking:</strong> Over 50+ Indian commercial and public sector banks.</li>
                        <li><strong>Wallets & Pay Later:</strong> Major digital wallets and eligible cardless EMI / PayLater facilities.</li>
                        <li><strong>Cash on Delivery (COD):</strong> Available on eligible PIN codes with zero convenience fee.</li>
                    </ul>
                </div>

                <!-- 5. GST Invoices -->
                <div class="mb-4">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-text text-success"></i> 5. Statutory GST Invoices</h4>
                    <p class="text-muted">
                        A computer-generated GST Tax Invoice is issued immediately upon order dispatch and is accessible in your EveryVeda Customer Dashboard. You can also preview our standard invoice format:
                    </p>
                    <a href="<?= url('/sample-invoice') ?>" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-pdf me-1"></i> View Sample GST Tax Invoice Format
                    </a>
                </div>

                <div class="border-top pt-4 mt-5 text-muted small text-center">
                    Questions regarding taxes, corporate bulk orders, or GST credit? Contact our Finance & Compliance desk at <a href="mailto:finance@everyveda.com" class="text-decoration-none fw-semibold">finance@everyveda.com</a>.
                </div>
            </div>
        </div>
    </div>
</div>
