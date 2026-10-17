<?php
/**
 * Blog & Ayurvedic Articles Data Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class BlogPost
{
    /**
     * Get published blog posts
     */
    public static function getPublished(int $limit = 6, ?string $locale = null): array
    {
        $locale = $locale ?: current_locale();
        $sql = "SELECT 
                    b.id, b.slug, b.featured_image, b.author_name, b.published_at,
                    COALESCE(bt.title, b.slug) AS title,
                    bt.summary,
                    bt.content
                FROM blog_posts b
                LEFT JOIN blog_translations bt ON (b.id = bt.blog_post_id AND bt.locale = :locale)
                WHERE b.is_published = 1
                ORDER BY b.published_at DESC
                LIMIT {$limit}";

        return Database::fetchAll($sql, ['locale' => $locale]);
    }

    /**
     * Find single post by slug
     */
    public static function findBySlug(string $slug, ?string $locale = null): ?array
    {
        $locale = $locale ?: current_locale();
        $sql = "SELECT 
                    b.id, b.slug, b.featured_image, b.author_name, b.published_at,
                    COALESCE(bt.title, b.slug) AS title,
                    bt.summary,
                    bt.content,
                    bt.meta_title,
                    bt.meta_description
                FROM blog_posts b
                LEFT JOIN blog_translations bt ON (b.id = bt.blog_post_id AND bt.locale = :locale)
                WHERE b.slug = :slug AND b.is_published = 1";

        return Database::fetchOne($sql, ['slug' => $slug, 'locale' => $locale]);
    }
}
