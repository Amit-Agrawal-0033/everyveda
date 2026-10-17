<?php
/**
 * Dynamic XML Sitemap Generator for Search Engines
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;

class SitemapController extends Controller
{
    public function index(Request $request): void
    {
        $baseUrl = rtrim(env('APP_URL', 'https://everyveda.com'), '/');

        // Fetch published products
        $products = Database::fetchAll(
            "SELECT slug, updated_at, created_at FROM products WHERE status = 'published' ORDER BY id DESC"
        );

        // Fetch active categories
        $categories = Database::fetchAll(
            "SELECT slug, created_at FROM categories WHERE is_active = 1 ORDER BY sort_order ASC"
        );

        // Fetch published blog posts
        $posts = Database::fetchAll(
            "SELECT slug, updated_at, created_at, published_at FROM blog_posts WHERE is_published = 1 ORDER BY id DESC"
        );

        // Fetch active CMS pages
        $pages = Database::fetchAll(
            "SELECT slug, updated_at, created_at FROM pages WHERE is_active = 1"
        );

        header('Content-Type: application/xml; charset=utf-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        // 1. Homepage
        $this->renderUrl("{$baseUrl}/", date('c'), 'daily', '1.0');

        // 2. Catalog
        $this->renderUrl("{$baseUrl}/catalog", date('c'), 'daily', '0.9');

        // 3. Blog Index
        $this->renderUrl("{$baseUrl}/blog", date('c'), 'weekly', '0.7');

        // 4. Contact Us
        $this->renderUrl("{$baseUrl}/contact-us", date('c'), 'monthly', '0.6');

        // 5. Categories
        foreach ($categories as $cat) {
            $catUrl = "{$baseUrl}/catalog?category=" . urlencode($cat['slug']);
            $this->renderUrl($catUrl, date('c', strtotime($cat['created_at'])), 'weekly', '0.8');
        }

        // 6. Products
        foreach ($products as $p) {
            $prodUrl = "{$baseUrl}/product/" . urlencode($p['slug']);
            $lastmod = !empty($p['updated_at']) ? $p['updated_at'] : $p['created_at'];
            $this->renderUrl($prodUrl, date('c', strtotime($lastmod)), 'weekly', '0.8');
        }

        // 7. Blog Posts
        foreach ($posts as $post) {
            $postUrl = "{$baseUrl}/blog/" . urlencode($post['slug']);
            $lastmod = !empty($post['updated_at']) ? $post['updated_at'] : ($post['published_at'] ?? $post['created_at']);
            $this->renderUrl($postUrl, date('c', strtotime($lastmod)), 'monthly', '0.7');
        }

        // 8. CMS Pages
        foreach ($pages as $page) {
            $pageUrl = "{$baseUrl}/page/" . urlencode($page['slug']);
            $lastmod = !empty($page['updated_at']) ? $page['updated_at'] : $page['created_at'];
            $this->renderUrl($pageUrl, date('c', strtotime($lastmod)), 'monthly', '0.5');
        }

        echo '</urlset>';
        exit;
    }

    private function renderUrl(string $loc, string $lastmod, string $changefreq, string $priority): void
    {
        $locXml = htmlspecialchars($loc, ENT_XML1, 'UTF-8');
        $enUrl = $loc . (str_contains($loc, '?') ? '&' : '?') . 'lang=en';
        $hiUrl = $loc . (str_contains($loc, '?') ? '&' : '?') . 'lang=hi';

        echo "  <url>\n";
        echo "    <loc>{$locXml}</loc>\n";
        echo "    <lastmod>{$lastmod}</lastmod>\n";
        echo "    <changefreq>{$changefreq}</changefreq>\n";
        echo "    <priority>{$priority}</priority>\n";
        echo "    <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"" . htmlspecialchars($enUrl, ENT_XML1, 'UTF-8') . "\" />\n";
        echo "    <xhtml:link rel=\"alternate\" hreflang=\"hi\" href=\"" . htmlspecialchars($hiUrl, ENT_XML1, 'UTF-8') . "\" />\n";
        echo "  </url>\n";
    }
}
