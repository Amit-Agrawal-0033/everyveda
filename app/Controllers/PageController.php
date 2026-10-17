<?php
/**
 * CMS Content Pages, Contact Inquiries & Newsletter Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;

class PageController extends Controller
{
    /**
     * Render CMS page by slug
     */
    public function show(Request $request, string $slug): void
    {
        $locale = current_locale();

        $page = Database::fetchOne(
            "SELECT p.*, pt.title, pt.content, pt.meta_title, pt.meta_description
             FROM pages p
             LEFT JOIN page_translations pt ON (p.id = pt.page_id AND pt.locale = :locale)
             WHERE p.slug = :slug AND p.is_active = 1",
            ['slug' => $slug, 'locale' => $locale]
        );

        if (!$page) {
            http_response_code(404);
            $this->view('pages/404', ['meta_title' => __('404_not_found')]);
            return;
        }

        $this->view('pages/show', [
            'meta_title' => $page['meta_title'] ?: ($page['title'] . ' — EveryVeda'),
            'meta_description' => $page['meta_description'] ?: strip_tags(substr($page['content'], 0, 160)),
            'page' => $page
        ]);
    }

    /**
     * Contact Us Page
     */
    public function contact(Request $request): void
    {
        $this->view('pages/contact', [
            'meta_title' => __('contact_us') . ' — EveryVeda'
        ]);
    }

    /**
     * Submit Contact Form
     */
    public function submitContact(Request $request): void
    {
        $this->validateCsrf($request);

        $errors = $this->validate($request->all(), [
            'name' => 'required|min:2|max:150',
            'email' => 'required|email',
            'phone' => 'phone',
            'subject' => 'required|min:3|max:200',
            'message' => 'required|min:10|max:2000'
        ]);

        if (!empty($errors)) {
            flash_input($request->all());
            $this->redirect(url('/contact-us'), 'error', reset($errors));
            return;
        }

        Database::insert('contact_enquiries', [
            'name' => trim((string)$request->post('name')),
            'email' => strtolower(trim((string)$request->post('email'))),
            'phone' => preg_replace('/[^\d]/', '', (string)$request->post('phone', '')),
            'subject' => trim((string)$request->post('subject')),
            'message' => trim((string)$request->post('message')),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->redirect(url('/contact-us'), 'success', 'Thank you! Your message has been received. Our Vaidyas and support team will reply within 24 hours.');
    }

    /**
     * Subscribe to Newsletter
     */
    public function subscribeNewsletter(Request $request): void
    {
        $this->validateCsrf($request);

        $email = strtolower(trim((string)$request->post('email', '')));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'error' => 'Please provide a valid email address.'], 422);
                return;
            }
            $this->redirect($_SERVER['HTTP_REFERER'] ?? url('/'), 'error', 'Please provide a valid email address.');
            return;
        }

        $existing = Database::fetchOne("SELECT id FROM newsletter_subscribers WHERE email = :email", ['email' => $email]);
        if (!$existing) {
            Database::insert('newsletter_subscribers', [
                'email' => $email,
                'status' => 'subscribed',
                'subscribed_at' => date('Y-m-d H:i:s')
            ]);
        }

        if ($request->isAjax()) {
            $this->json(['success' => true, 'message' => __('subscribe_success')]);
            return;
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? url('/'), 'success', __('subscribe_success'));
    }
}
