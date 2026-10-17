<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="h2 fw-bold text-success text-center mb-2"><?= __('contact_us') ?></h1>
            <p class="text-muted text-center mb-5">Have a question about classical Ayurvedic formulations or order tracking? Reach out to our team.</p>

            <div class="row g-4">
                <!-- Contact Info Card -->
                <div class="col-md-5">
                    <div class="card border p-4 text-white h-100 shadow-sm" style="background: linear-gradient(135deg, #183827, #214D36);">
                        <h4 class="fw-bold text-warning mb-4">EveryVeda Headquarters</h4>

                        <div class="d-flex gap-3 mb-4">
                            <i class="bi bi-geo-alt fs-4 text-warning"></i>
                            <div class="small">
                                <strong>Registered Office</strong><br>
                                <?= e($settings['company_name'] ?? 'EveryVeda Organics Pvt Ltd') ?><br>
                                <?= e($settings['company_address'] ?? 'Sector 62, Noida, Uttar Pradesh, 201309') ?>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-4">
                            <i class="bi bi-envelope fs-4 text-warning"></i>
                            <div class="small">
                                <strong>Customer Support Email</strong><br>
                                <a href="mailto:<?= e($settings['contact_email'] ?? 'support@everyveda.com') ?>" class="text-white-50"><?= e($settings['contact_email'] ?? 'support@everyveda.com') ?></a>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-4">
                            <i class="bi bi-telephone fs-4 text-warning"></i>
                            <div class="small">
                                <strong>Helpline Number</strong><br>
                                <a href="tel:<?= e($settings['contact_phone'] ?? '+919876543210') ?>" class="text-white-50"><?= e($settings['contact_phone'] ?? '+91 98765 43210') ?></a>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-auto">
                            <i class="bi bi-clock fs-4 text-warning"></i>
                            <div class="small">
                                <strong>Hours of Operation</strong><br>
                                <?= e($settings['support_hours'] ?? 'Mon - Sat: 9:00 AM - 7:00 PM IST') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form Card -->
                <div class="col-md-7">
                    <div class="card border p-4 bg-white shadow-sm h-100">
                        <h4 class="fw-bold mb-3">Send a Message</h4>
                        <form action="<?= url('/contact-us') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"><?= __('full_name') ?> *</label>
                                    <input type="text" name="name" class="form-control" value="<?= old('name', $user['name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"><?= __('email_address') ?> *</label>
                                    <input type="email" name="email" class="form-control" value="<?= old('email', $user['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"><?= __('phone_number') ?></label>
                                    <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" value="<?= old('phone', $user['phone'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Subject *</label>
                                    <input type="text" name="subject" class="form-control" placeholder="Product query, order inquiry..." value="<?= old('subject') ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Message *</label>
                                    <textarea name="message" rows="4" class="form-control" placeholder="Please write your inquiry here..." required><?= old('message') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-ev-primary py-2 px-4">
                                        Send Message <i class="bi bi-send ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
