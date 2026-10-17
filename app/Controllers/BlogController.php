<?php
/**
 * Blog & Articles Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index(Request $request): void
    {
        $posts = BlogPost::getPublished(12);

        $this->view('blog/index', [
            'meta_title' => __('blog') . ' — EveryVeda',
            'meta_description' => 'Authentic classical Ayurvedic wisdom, herbal science, seasonal diet tips, and wellness guides.',
            'posts' => $posts
        ]);
    }

    public function show(Request $request, string $slug): void
    {
        $post = BlogPost::findBySlug($slug);
        if (!$post) {
            http_response_code(404);
            $this->view('pages/404', ['meta_title' => __('404_not_found')]);
            return;
        }

        $recentPosts = BlogPost::getPublished(3);

        $this->view('blog/show', [
            'meta_title' => $post['meta_title'] ?: ($post['title'] . ' — EveryVeda Journal'),
            'meta_description' => $post['meta_description'] ?: strip_tags((string)$post['summary']),
            'post' => $post,
            'recentPosts' => $recentPosts
        ]);
    }
}
