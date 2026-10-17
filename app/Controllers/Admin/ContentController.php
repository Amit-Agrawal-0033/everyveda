<?php
/**
 * Admin Content Management Controller (Banners, Blog Posts, CMS Pages)
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\AdminAuthService;

class ContentController extends Controller
{
    // ==========================================
    // 1. BANNERS
    // ==========================================
    public function banners(Request $request): void
    {
        $banners = Database::fetchAll("SELECT * FROM banners ORDER BY sort_order ASC, id ASC");
        $this->adminView('admin/content/banners', [
            'meta_title' => 'Homepage Banners — EveryVeda Admin',
            'banners' => $banners
        ]);
    }

    public function storeBanner(Request $request): void
    {
        $this->validateCsrf($request);

        $image = 'placeholder-banner.svg';
        $uploaded = $request->file('image');
        if ($uploaded && !empty($uploaded['name']) && $uploaded['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($uploaded['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
                $safeName = 'banner_' . time() . '.' . $ext;
                if (move_uploaded_file($uploaded['tmp_name'], PUBLIC_PATH . '/assets/images/banners/' . $safeName)) {
                    $image = $safeName;
                }
            }
        }

        Database::insert('banners', [
            'title_en' => trim((string)$request->post('title_en')),
            'title_hi' => trim((string)$request->post('title_hi')),
            'subtitle_en' => trim((string)$request->post('subtitle_en', '')),
            'subtitle_hi' => trim((string)$request->post('subtitle_hi', '')),
            'link_url' => trim((string)$request->post('link_url', '/catalog')),
            'image_path' => $image,
            'sort_order' => (int)$request->post('sort_order', 0),
            'is_active' => !empty($request->post('is_active')) ? 1 : 0
        ]);

        $this->redirect(url('/admin/content/banners'), 'success', 'Banner created successfully.');
    }

    public function deleteBanner(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        Database::delete('banners', "id = :id", ['id' => (int)$id]);
        $this->redirect(url('/admin/content/banners'), 'info', 'Banner removed.');
    }

    // ==========================================
    // 2. BLOG POSTS
    // ==========================================
    public function blog(Request $request): void
    {
        $posts = Database::fetchAll(
            "SELECT b.*, bt_en.title AS title_en, bt_hi.title AS title_hi
             FROM blog_posts b
             LEFT JOIN blog_translations bt_en ON (b.id = bt_en.blog_post_id AND bt_en.locale = 'en')
             LEFT JOIN blog_translations bt_hi ON (b.id = bt_hi.blog_post_id AND bt_hi.locale = 'hi')
             ORDER BY b.id DESC"
        );

        $this->adminView('admin/content/blog', [
            'meta_title' => 'Blog Posts Management — EveryVeda Admin',
            'posts' => $posts
        ]);
    }

    public function createBlogPost(Request $request): void
    {
        $this->adminView('admin/content/blog_create', [
            'meta_title' => 'Write Ayurvedic Article — EveryVeda Admin'
        ]);
    }

    public function storeBlogPost(Request $request): void
    {
        $this->validateCsrf($request);

        $errors = $this->validate($request->all(), [
            'slug' => 'required',
            'title_en' => 'required|min:3|max:255',
            'title_hi' => 'required|min:3|max:255',
            'content_en' => 'required',
            'content_hi' => 'required'
        ]);

        if (!empty($errors)) {
            flash_input($request->all());
            $this->redirect(url('/admin/content/blog/create'), 'error', reset($errors));
            return;
        }

        $slug = slugify((string)$request->post('slug'));
        $image = 'placeholder-blog.svg';
        $uploaded = $request->file('featured_image');
        if ($uploaded && !empty($uploaded['name']) && $uploaded['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($uploaded['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
                $safeName = 'blog_' . time() . '.' . $ext;
                if (move_uploaded_file($uploaded['tmp_name'], PUBLIC_PATH . '/assets/images/' . $safeName)) {
                    $image = $safeName;
                }
            }
        }

        $postId = Database::transaction(function () use ($request, $slug, $image) {
            $id = Database::insert('blog_posts', [
                'slug' => $slug,
                'featured_image' => $image,
                'author_name' => trim((string)$request->post('author_name', 'EveryVeda Team')),
                'is_published' => !empty($request->post('is_published')) ? 1 : 0,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            Database::insert('blog_translations', [
                'blog_post_id' => $id,
                'locale' => 'en',
                'title' => trim((string)$request->post('title_en')),
                'summary' => trim((string)$request->post('summary_en', '')),
                'content' => trim((string)$request->post('content_en')),
                'meta_title' => trim((string)$request->post('meta_title_en', '')),
                'meta_description' => trim((string)$request->post('meta_description_en', ''))
            ]);

            Database::insert('blog_translations', [
                'blog_post_id' => $id,
                'locale' => 'hi',
                'title' => trim((string)$request->post('title_hi')),
                'summary' => trim((string)$request->post('summary_hi', '')),
                'content' => trim((string)$request->post('content_hi')),
                'meta_title' => trim((string)$request->post('meta_title_hi', '')),
                'meta_description' => trim((string)$request->post('meta_description_hi', ''))
            ]);

            return $id;
        });

        $this->redirect(url('/admin/content/blog'), 'success', 'Article published successfully.');
    }

    public function deleteBlogPost(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        Database::delete('blog_posts', "id = :id", ['id' => (int)$id]);
        $this->redirect(url('/admin/content/blog'), 'info', 'Article deleted.');
    }

    // ==========================================
    // 3. CMS PAGES
    // ==========================================
    public function pages(Request $request): void
    {
        $pages = Database::fetchAll(
            "SELECT p.*, pt_en.title AS title_en, pt_hi.title AS title_hi
             FROM pages p
             LEFT JOIN page_translations pt_en ON (p.id = pt_en.page_id AND pt_en.locale = 'en')
             LEFT JOIN page_translations pt_hi ON (p.id = pt_hi.page_id AND pt_hi.locale = 'hi')
             ORDER BY p.id ASC"
        );

        $this->adminView('admin/content/pages', [
            'meta_title' => 'CMS Pages Management — EveryVeda Admin',
            'pages' => $pages
        ]);
    }

    public function editPage(Request $request, string $id): void
    {
        $page = Database::fetchOne("SELECT * FROM pages WHERE id = :id", ['id' => (int)$id]);
        if (!$page) {
            $this->redirect(url('/admin/content/pages'), 'error', 'Page not found.');
            return;
        }

        $transEn = Database::fetchOne("SELECT * FROM page_translations WHERE page_id = :id AND locale = 'en'", ['id' => (int)$id]) ?: [];
        $transHi = Database::fetchOne("SELECT * FROM page_translations WHERE page_id = :id AND locale = 'hi'", ['id' => (int)$id]) ?: [];

        $this->adminView('admin/content/page_edit', [
            'meta_title' => 'Edit Page: ' . $page['slug'] . ' — EveryVeda Admin',
            'page' => $page,
            'transEn' => $transEn,
            'transHi' => $transHi
        ]);
    }

    public function updatePage(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $pageId = (int)$id;

        Database::transaction(function () use ($request, $pageId) {
            Database::query(
                "INSERT INTO page_translations (page_id, locale, title, content, meta_title, meta_description)
                 VALUES (:pid, 'en', :title, :content, :mtitle, :mdesc)
                 ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description)",
                [
                    'pid' => $pageId,
                    'title' => trim((string)$request->post('title_en')),
                    'content' => trim((string)$request->post('content_en')),
                    'mtitle' => trim((string)$request->post('meta_title_en', '')),
                    'mdesc' => trim((string)$request->post('meta_description_en', ''))
                ]
            );

            Database::query(
                "INSERT INTO page_translations (page_id, locale, title, content, meta_title, meta_description)
                 VALUES (:pid, 'hi', :title, :content, :mtitle, :mdesc)
                 ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description)",
                [
                    'pid' => $pageId,
                    'title' => trim((string)$request->post('title_hi')),
                    'content' => trim((string)$request->post('content_hi')),
                    'mtitle' => trim((string)$request->post('meta_title_hi', '')),
                    'mdesc' => trim((string)$request->post('meta_description_hi', ''))
                ]
            );

            AdminAuthService::logAudit((int)admin_user()['id'], 'update_page', 'pages', $pageId, "Updated CMS page #{$pageId}");
        });

        $this->redirect(url('/admin/content/pages'), 'success', 'Page content updated successfully.');
    }
}
