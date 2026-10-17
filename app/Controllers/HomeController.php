<?php
/**
 * Homepage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\BlogPost;

class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $banners = Banner::getActive();
        $categories = Category::getActiveWithCounts();
        $featuredProducts = Product::getFeatured(8);
        $bestSellers = Product::getBestSellers(8);
        $newArrivals = Product::getNewArrivals(8);
        $recentBlogPosts = BlogPost::getPublished(3);

        $this->view('home/index', [
            'meta_title' => __('site_title'),
            'meta_description' => 'Authentic Ayurvedic wellness products, classical rasayanas, cold-pressed juices, pure Shilajit, and herbal pain relief oils. Fast shipping across India.',
            'banners' => $banners,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals,
            'blogPosts' => $recentBlogPosts
        ]);
    }
}
