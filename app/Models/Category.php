<?php
/**
 * Category Data Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Category
{
    /**
     * Get all active categories with product counts
     */
    public static function getActiveWithCounts(): array
    {
        return Database::fetchAll(
            "SELECT c.*, COUNT(p.id) AS product_count 
             FROM categories c
             LEFT JOIN products p ON (c.id = p.category_id AND p.status = 'published')
             WHERE c.is_active = 1
             GROUP BY c.id
             ORDER BY c.sort_order ASC, c.id ASC"
        );
    }

    /**
     * Find category by slug
     */
    public static function findBySlug(string $slug): ?array
    {
        return Database::fetchOne(
            "SELECT * FROM categories WHERE slug = :slug AND is_active = 1",
            ['slug' => $slug]
        );
    }
}
