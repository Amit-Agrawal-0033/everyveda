<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('/pricing') ?>" class="small text-muted text-decoration-none">← Back to Pricing Policy</a>
            <h1 class="h3 fw-bold text-dark m-0 mt-1">Sample Statutory GST Tax Invoice</h1>
            <small class="text-muted">Conforms to Section 31 of CGST Act, 2017 and Rule 46 of CGST Rules</small>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-outline-dark">
                <i class="bi bi-printer me-1"></i> Print / Save PDF
            </button>
        </div>
    </div>

    <!-- Invoice Sheet Simulation -->
    <div class="card border shadow p-4 p-md-5 bg-white mx-auto rounded-3" style="max-width: 900px;">
        <!-- Header -->
        <div class="row align-items-center pb-4 mb-4 border-bottom">
            <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div style="width: 32px; height: 32px; background: #214D36; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #C6A45C; font-weight: 800; font-size: 16px;">E</div>
                    <span class="fs-4 fw-bold text-success" style="letter-spacing: -0.5px;">Every<span style="color: #C6A45C;">Veda</span></span>
                </div>
                <small class="text-muted d-block">EveryVeda Organics Private Limited</small>
                <small class="text-muted d-block">B-42, Herb Park, Industrial Area, Phase-2</small>
                <small class="text-muted d-block">Varanasi, Uttar Pradesh — 221002, India</small>
                <small class="text-muted d-block"><strong>GSTIN:</strong> 09AABCE1234F1Z5 • <strong>State Code:</strong> 09 (UP)</small>
                <small class="text-muted d-block"><strong>FSSAI Lic No:</strong> 12724001000889 • <strong>AYUSH Lic:</strong> UP-AYU-884</small>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold text-uppercase mb-2">TAX INVOICE</span>
                <h6 class="fw-bold mb-1">Invoice No: EV/2026-27/00142</h6>
                <small class="text-muted d-block"><strong>Invoice Date:</strong> 17 Nov 2026</small>
                <small class="text-muted d-block"><strong>Order ID:</strong> EV-ORD-20261117-9842</small>
                <small class="text-muted d-block"><strong>Payment Method:</strong> Razorpay (UPI)</small>
                <small class="text-muted d-block"><strong>Place of Supply:</strong> Uttar Pradesh (09)</small>
            </div>
        </div>

        <!-- Customer Billing & Shipping Address -->
        <div class="row mb-4 pb-3 border-bottom small">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <strong class="text-dark d-block mb-1 text-uppercase">Billed To (Customer):</strong>
                <span class="fw-bold d-block">Aarav Sharma</span>
                <span class="text-muted d-block">Flat 402, Lotus Apartments, Sigra</span>
                <span class="text-muted d-block">Varanasi, Uttar Pradesh — 221010</span>
                <span class="text-muted d-block">Mobile: +91 98765 43210</span>
                <span class="text-muted d-block">State: Uttar Pradesh (Code: 09)</span>
            </div>
            <div class="col-sm-6">
                <strong class="text-dark d-block mb-1 text-uppercase">Shipped To (Delivery Destination):</strong>
                <span class="fw-bold d-block">Aarav Sharma</span>
                <span class="text-muted d-block">Flat 402, Lotus Apartments, Sigra</span>
                <span class="text-muted d-block">Varanasi, Uttar Pradesh — 221010</span>
                <span class="text-muted d-block">Carrier: Delhivery Express Surface</span>
                <span class="text-muted d-block">Airway Bill (AWB): DLHV-984210398</span>
            </div>
        </div>

        <!-- Goods Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Description of Goods</th>
                        <th>HSN Code</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Taxable Value</th>
                        <th class="text-end">CGST (6%)</th>
                        <th class="text-end">SGST (6%)</th>
                        <th class="text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <strong>EveryVeda Royal Gold Chyawanprash (500g)</strong><br>
                            <small class="text-muted">SKU: EV-CHY-001</small>
                        </td>
                        <td><code>30049011</code></td>
                        <td class="text-center">1</td>
                        <td class="text-end">₹499.00</td>
                        <td class="text-end text-success">-₹49.90</td>
                        <td class="text-end">₹400.98</td>
                        <td class="text-end">₹24.06</td>
                        <td class="text-end">₹24.06</td>
                        <td class="text-end fw-bold">₹449.10</td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>
                            <strong>Triphala Churna Digestive Cleanse (250g)</strong><br>
                            <small class="text-muted">SKU: EV-TRP-001 (5% GST)</small>
                        </td>
                        <td><code>12119029</code></td>
                        <td class="text-center">1</td>
                        <td class="text-end">₹199.00</td>
                        <td class="text-end text-success">-₹19.90</td>
                        <td class="text-end">₹170.57</td>
                        <td class="text-end">₹4.26</td>
                        <td class="text-end">₹4.27</td>
                        <td class="text-end fw-bold">₹179.10</td>
                    </tr>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end fw-bold">Subtotal (MRP Incl. GST):</td>
                        <td class="text-end fw-bold">₹571.55</td>
                        <td class="text-end fw-bold">₹28.32</td>
                        <td class="text-end fw-bold">₹28.33</td>
                        <td class="text-end fw-bold">₹628.20</td>
                    </tr>
                    <tr>
                        <td colspan="9" class="text-end">Subtotal After Coupon Discount (VEDA10):</td>
                        <td class="text-end fw-bold">₹628.20</td>
                    </tr>
                    <tr>
                        <td colspan="9" class="text-end">Insured Shipping & Delivery (Free over ₹499):</td>
                        <td class="text-end text-success fw-bold">FREE (₹0.00)</td>
                    </tr>
                    <tr class="fs-6 table-success">
                        <td colspan="9" class="text-end fw-bold text-dark">Total Payable Invoice Amount:</td>
                        <td class="text-end fw-bold text-success fs-5">₹628.20</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- GST Summary Box -->
        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="border rounded p-3 bg-light small">
                    <strong class="d-block mb-2 text-dark">GST Breakdown Summary by Tax Slab:</strong>
                    <table class="table table-sm table-bordered m-0 bg-white small">
                        <thead>
                            <tr class="table-light">
                                <th>Tax Rate</th>
                                <th class="text-end">Taxable Base</th>
                                <th class="text-end">CGST Amount</th>
                                <th class="text-end">SGST Amount</th>
                                <th class="text-end">Total GST</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>5.0% (HSN 12119029)</td>
                                <td class="text-end">₹170.57</td>
                                <td class="text-end">₹4.26</td>
                                <td class="text-end">₹4.27</td>
                                <td class="text-end fw-bold">₹8.53</td>
                            </tr>
                            <tr>
                                <td>12.0% (HSN 30049011)</td>
                                <td class="text-end">₹400.98</td>
                                <td class="text-end">₹24.06</td>
                                <td class="text-end">₹24.06</td>
                                <td class="text-end fw-bold">₹48.12</td>
                            </tr>
                            <tr class="fw-bold table-light">
                                <td>Total Tax Summary</td>
                                <td class="text-end">₹571.55</td>
                                <td class="text-end">₹28.32</td>
                                <td class="text-end">₹28.33</td>
                                <td class="text-end text-success">₹56.65</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-5">
                <div class="border rounded p-3 bg-light small text-center h-100 d-flex flex-column justify-content-between">
                    <div>
                        <strong class="text-dark d-block">For EveryVeda Organics Pvt Ltd</strong>
                        <small class="text-muted">Digitally Signed & Verified</small>
                    </div>
                    <div class="my-3">
                        <span class="badge bg-success-subtle text-success border border-success p-2">
                            <i class="bi bi-patch-check-fill me-1"></i> Authorized Electronic Signature
                        </span>
                    </div>
                    <small class="text-muted">This is an electronically generated Tax Invoice under Section 31 of CGST Act. No physical signature required.</small>
                </div>
            </div>
        </div>

        <div class="border-top pt-3 text-muted small text-center">
            Thank you for living naturally with EveryVeda! For invoice inquiries or GST credit notes, write to <a href="mailto:support@everyveda.com" class="text-decoration-none">support@everyveda.com</a>.
        </div>
    </div>
</div>
