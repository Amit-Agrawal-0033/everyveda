<?php
/**
 * Product Data Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Product
{
    /**
     * Get featured products for homepage
     */
    public static function getFeatured(int $limit = 8, ?string $locale = null): array
    {
        $locale = $locale ?: current_locale();
        $sql = "SELECT 
                    p.*,
                    COALESCE(pt.name, p.slug) AS name,
                    pt.short_description,
                    c.slug AS category_slug,
                    COALESCE(c.name_en, '') AS category_name
                FROM products p
                LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = :locale)
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' AND p.is_featured = 1
                ORDER BY p.id DESC
                LIMIT {$limit}";

        return Database::fetchAll($sql, ['locale' => $locale]);
    }

    /**
     * Get best selling products
     */
    public static function getBestSellers(int $limit = 8, ?string $locale = null): array
    {
        $locale = $locale ?: current_locale();
        $sql = "SELECT 
                    p.*,
                    COALESCE(pt.name, p.slug) AS name,
                    pt.short_description,
                    c.slug AS category_slug
                FROM products p
                LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = :locale)
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' AND p.is_best_seller = 1
                ORDER BY p.views_count DESC, p.id DESC
                LIMIT {$limit}";

        return Database::fetchAll($sql, ['locale' => $locale]);
    }

    /**
     * Get new arrivals
     */
    public static function getNewArrivals(int $limit = 8, ?string $locale = null): array
    {
        $locale = $locale ?: current_locale();
        $sql = "SELECT 
                    p.*,
                    COALESCE(pt.name, p.slug) AS name,
                    pt.short_description,
                    c.slug AS category_slug
                FROM products p
                LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = :locale)
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' AND p.is_new_arrival = 1
                ORDER BY p.created_at DESC
                LIMIT {$limit}";

        return Database::fetchAll($sql, ['locale' => $locale]);
    }

    /**
     * Find single published product by slug
     */
    public static function findBySlug(string $slug, ?string $locale = null): ?array
    {
        $locale = $locale ?: current_locale();
        $sql = "SELECT 
                    p.*,
                    COALESCE(pt.name, p.slug) AS name,
                    pt.short_description,
                    pt.description,
                    pt.ingredients,
                    pt.directions,
                    pt.storage_info,
                    pt.warnings,
                    pt.meta_title,
                    pt.meta_description,
                    c.id AS category_id,
                    c.slug AS category_slug,
                    c.name_en AS category_name_en,
                    c.name_hi AS category_name_hi
                FROM products p
                LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = :locale)
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = :slug AND p.status = 'published'";

        $product = Database::fetchOne($sql, ['slug' => $slug, 'locale' => $locale]);
        if ($product) {
            // Increment view count
            Database::query("UPDATE products SET views_count = views_count + 1 WHERE id = :id", ['id' => $product['id']]);
        }

        return $product;
    }

    /**
     * Get active variants for a product
     */
    public static function getVariants(int $productId): array
    {
        return Database::fetchAll(
            "SELECT * FROM product_variants WHERE product_id = :pid AND is_active = 1 ORDER BY price ASC",
            ['pid' => $productId]
        );
    }

    /**
     * Get gallery images for a product
     */
    public static function getImages(int $productId): array
    {
        return Database::fetchAll(
            "SELECT * FROM product_images WHERE product_id = :pid ORDER BY sort_order ASC",
            ['pid' => $productId]
        );
    }

    /**
     * Get approved reviews for a product
     */
    public static function getReviews(int $productId): array
    {
        return Database::fetchAll(
            "SELECT * FROM product_reviews WHERE product_id = :pid AND status = 'approved' ORDER BY id DESC",
            ['pid' => $productId]
        );
    }

    /**
     * Get average rating and count
     */
    public static function getRatingStats(int $productId): array
    {
        $stats = Database::fetchOne(
            "SELECT COUNT(*) AS total_reviews, COALESCE(AVG(rating), 5.0) AS avg_rating 
             FROM product_reviews 
             WHERE product_id = :pid AND status = 'approved'",
            ['pid' => $productId]
        );

        return [
            'count' => (int)($stats['total_reviews'] ?? 0),
            'average' => round((float)($stats['avg_rating'] ?? 5.0), 1)
        ];
    }

    /**
     * Get related products in same category
     */
    public static function getRelated(int $productId, ?int $categoryId, int $limit = 4, ?string $locale = null): array
    {
        $locale = $locale ?: current_locale();
        $params = ['pid' => $productId, 'locale' => $locale];
        $catClause = "";

        if ($categoryId) {
            $catClause = "AND p.category_id = :cid";
            $params['cid'] = $categoryId;
        }

        $sql = "SELECT 
                    p.*,
                    COALESCE(pt.name, p.slug) AS name,
                    pt.short_description,
                    c.slug AS category_slug
                FROM products p
                LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = :locale)
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' AND p.id != :pid {$catClause}
                ORDER BY p.is_best_seller DESC, p.id DESC
                LIMIT {$limit}";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Search and Filter Catalog with Pagination
     */
    public static function filter(array $filters, ?string $locale = null, int $page = 1, int $perPage = 12): array
    {
        $locale = $locale ?: current_locale();
        $where = ["p.status = 'published'"];
        $params = ['locale' => $locale];

        // Category Filter
        if (!empty($filters['category'])) {
            $where[] = "(c.slug = :category_slug OR c.id = :category_id)";
            $params['category_slug'] = $filters['category'];
            $params['category_id'] = is_numeric($filters['category']) ? (int)$filters['category'] : 0;
        }

        // Search Query (Search in English/Hindi name, SKU, or description)
        if (!empty($filters['q'])) {
            $where[] = "(pt.name LIKE :search_term OR p.sku LIKE :search_sku OR pt.short_description LIKE :search_desc)";
            $searchWildcard = '%' . trim($filters['q']) . '%';
            $params['search_term'] = $searchWildcard;
            $params['search_sku'] = $searchWildcard;
            $params['search_desc'] = $searchWildcard;
        }

        // In Stock Only
        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock_quantity > 0";
        }

        // Price Filter
        if (!empty($filters['min_price'])) {
            $where[] = "COALESCE(p.sale_price, p.price) >= :min_price";
            $params['min_price'] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = "COALESCE(p.sale_price, p.price) <= :max_price";
            $params['max_price'] = (float)$filters['max_price'];
        }

        $whereClause = implode(' AND ', $where);

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        $orderBy = match ($sort) {
            'price_asc' => 'COALESCE(p.sale_price, p.price) ASC',
            'price_desc' => 'COALESCE(p.sale_price, p.price) DESC',
            'popular' => 'p.views_count DESC, p.id DESC',
            default => 'p.id DESC'
        };

        // Total count for pagination
        $countSql = "SELECT COUNT(*) 
                     FROM products p
                     LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = :locale)
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE {$whereClause}";
        $total = (int)Database::fetchColumn($countSql, $params);

        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $itemsSql = "SELECT 
                        p.*,
                        COALESCE(pt.name, p.slug) AS name,
                        pt.short_description,
                        c.slug AS category_slug,
                        c.name_en AS category_name_en,
                        c.name_hi AS category_name_hi
                     FROM products p
                     LEFT JOIN product_translations pt ON (p.id = pt.product_id AND pt.locale = :locale)
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE {$whereClause}
                     ORDER BY {$orderBy}
                     LIMIT {$perPage} OFFSET {$offset}";

        $items = Database::fetchAll($itemsSql, $params);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages
        ];
    }
}
