<?php
/**
 * Product Catalog & Category Browsing Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Category;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'category' => $request->get('category'),
            'q' => $request->get('q'),
            'in_stock' => $request->get('in_stock'),
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'sort' => $request->get('sort', 'latest')
        ];

        $page = (int)$request->get('page', 1);
        $result = Product::filter($filters, null, $page, 12);
        $categories = Category::getActiveWithCounts();

        $selectedCategory = null;
        if (!empty($filters['category'])) {
            $selectedCategory = Category::findBySlug($filters['category']);
        }

        $metaTitle = __('catalog');
        if ($selectedCategory) {
            $catName = current_locale() === 'hi' ? $selectedCategory['name_hi'] : $selectedCategory['name_en'];
            $metaTitle = "{$catName} — " . __('catalog');
        } elseif (!empty($filters['q'])) {
            $metaTitle = "Search: " . e($filters['q']) . " — EveryVeda";
        }

        $this->view('catalog/index', [
            'meta_title' => $metaTitle,
            'meta_description' => $selectedCategory ? ($selectedCategory['description_en'] ?? '') : 'Browse our comprehensive collection of pure Ayurvedic herbal supplements.',
            'products' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'totalPages' => $result['total_pages'],
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'filters' => $filters
        ]);
    }
}
