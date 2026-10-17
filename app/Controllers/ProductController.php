<?php
/**
 * Product Detail, Variants & Customer Reviews Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Services\ShippingService;

class ProductController extends Controller
{
    /**
     * Show Product Detail Page
     */
    public function show(Request $request, string $slug): void
    {
        $product = Product::findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $this->view('pages/404', ['meta_title' => __('404_not_found')]);
            return;
        }

        $variants = Product::getVariants((int)$product['id']);
        $images = Product::getImages((int)$product['id']);
        $reviews = Product::getReviews((int)$product['id']);
        $ratingStats = Product::getRatingStats((int)$product['id']);
        $relatedProducts = Product::getRelated((int)$product['id'], $product['category_id'] ? (int)$product['category_id'] : null, 4);

        // Schema.org Product Structured Data (JSON-LD)
        $schemaData = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product['name'],
            'image' => [asset('assets/images/products/' . ($product['primary_image'] ?: 'placeholder.svg'))],
            'description' => strip_tags((string)($product['short_description'] ?? $product['name'])),
            'sku' => $product['sku'],
            'offers' => [
                '@type' => 'Offer',
                'url' => url('/product/' . $product['slug']),
                'priceCurrency' => 'INR',
                'price' => !empty($product['sale_price']) ? (float)$product['sale_price'] : (float)$product['price'],
                'availability' => (int)$product['stock_quantity'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition'
            ]
        ];

        if ($ratingStats['count'] > 0) {
            $schemaData['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $ratingStats['average'],
                'reviewCount' => $ratingStats['count']
            ];
        }

        $this->view('product/show', [
            'meta_title' => $product['meta_title'] ?: ($product['name'] . ' | EveryVeda'),
            'meta_description' => $product['meta_description'] ?: strip_tags((string)$product['short_description']),
            'product' => $product,
            'variants' => $variants,
            'images' => $images,
            'reviews' => $reviews,
            'ratingStats' => $ratingStats,
            'relatedProducts' => $relatedProducts,
            'schemaJson' => json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * AJAX Check PIN code serviceability
     */
    public function checkPincode(Request $request): void
    {
        $pincode = (string)$request->input('pincode', '');
        $result = ShippingService::checkPincode($pincode);
        $this->json($result);
    }

    /**
     * Submit Customer Review
     */
    public function submitReview(Request $request, string $slug): void
    {
        $this->validateCsrf($request);

        $product = Product::findBySlug($slug);
        if (!$product) {
            $this->redirect(url('/catalog'), 'error', 'Product not found.');
            return;
        }

        $errors = $this->validate($request->all(), [
            'reviewer_name' => 'required|min:2|max:100',
            'reviewer_email' => 'required|email',
            'rating' => 'required|numeric',
            'comment' => 'required|min:5|max:1000'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/product/' . $slug . '#reviews'), 'error', reset($errors));
            return;
        }

        $rating = max(1, min(5, (int)$request->post('rating')));
        $userId = auth_user()['id'] ?? null;
        $isVerifiedPurchase = 0;

        // Check if authenticated user actually purchased this product
        if ($userId) {
            $hasPurchased = Database::fetchOne(
                "SELECT oi.id FROM order_items oi
                 JOIN orders o ON oi.order_id = o.id
                 WHERE o.user_id = :uid AND oi.product_id = :pid AND o.status IN ('delivered', 'shipped')",
                ['uid' => $userId, 'pid' => $product['id']]
            );
            if ($hasPurchased) {
                $isVerifiedPurchase = 1;
            }
        }

        Database::insert('product_reviews', [
            'product_id' => $product['id'],
            'user_id' => $userId,
            'reviewer_name' => trim((string)$request->post('reviewer_name')),
            'reviewer_email' => strtolower(trim((string)$request->post('reviewer_email'))),
            'rating' => $rating,
            'title' => trim((string)$request->post('title', '')),
            'comment' => trim((string)$request->post('comment')),
            'is_verified_purchase' => $isVerifiedPurchase,
            'status' => 'pending', // Moderation required
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->redirect(url('/product/' . $slug . '#reviews'), 'success', 'Thank you! Your review has been submitted for verification.');
    }
}
