<?php
/**
 * Admin Category Management Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\AdminAuthService;

class CategoryController extends Controller
{
    public function index(Request $request): void
    {
        $categories = Database::fetchAll(
            "SELECT c.*, COUNT(p.id) AS product_count 
             FROM categories c
             LEFT JOIN products p ON c.id = p.category_id
             GROUP BY c.id
             ORDER BY c.sort_order ASC, c.id ASC"
        );

        $this->adminView('admin/categories/index', [
            'meta_title' => 'Categories Management — EveryVeda Admin',
            'categories' => $categories
        ]);
    }

    public function create(Request $request): void
    {
        $this->adminView('admin/categories/create', [
            'meta_title' => 'Add New Category — EveryVeda Admin'
        ]);
    }

    public function store(Request $request): void
    {
        $this->validateCsrf($request);

        $errors = $this->validate($request->all(), [
            'name_en' => 'required|min:2|max:150',
            'name_hi' => 'required|min:2|max:150',
            'slug' => 'required|min:2|max:150'
        ]);

        if (!empty($errors)) {
            flash_input($request->all());
            $this->redirect(url('/admin/categories/create'), 'error', reset($errors));
            return;
        }

        $slug = slugify((string)$request->post('slug'));
        $existing = Database::fetchOne("SELECT id FROM categories WHERE slug = :slug", ['slug' => $slug]);
        if ($existing) {
            flash_input($request->all());
            $this->redirect(url('/admin/categories/create'), 'error', 'A category with this URL slug already exists.');
            return;
        }

        $image = 'placeholder.svg';
        $uploaded = $request->file('image');
        if ($uploaded && !empty($uploaded['name']) && $uploaded['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($uploaded['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
                $safeName = 'cat_' . time() . '.' . $ext;
                if (move_uploaded_file($uploaded['tmp_name'], PUBLIC_PATH . '/assets/images/categories/' . $safeName)) {
                    $image = $safeName;
                }
            }
        }

        $cid = Database::insert('categories', [
            'slug' => $slug,
            'name_en' => trim((string)$request->post('name_en')),
            'name_hi' => trim((string)$request->post('name_hi')),
            'description_en' => trim((string)$request->post('description_en', '')),
            'description_hi' => trim((string)$request->post('description_hi', '')),
            'image' => $image,
            'sort_order' => (int)$request->post('sort_order', 0),
            'is_featured' => !empty($request->post('is_featured')) ? 1 : 0,
            'is_active' => !empty($request->post('is_active')) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        AdminAuthService::logAudit((int)admin_user()['id'], 'create_category', 'categories', $cid, "Created category {$slug}");

        $this->redirect(url('/admin/categories'), 'success', 'Category created successfully.');
    }

    public function edit(Request $request, string $id): void
    {
        $category = Database::fetchOne("SELECT * FROM categories WHERE id = :id", ['id' => (int)$id]);
        if (!$category) {
            $this->redirect(url('/admin/categories'), 'error', 'Category not found.');
            return;
        }

        $this->adminView('admin/categories/edit', [
            'meta_title' => 'Edit Category — EveryVeda Admin',
            'category' => $category
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $cid = (int)$id;

        $errors = $this->validate($request->all(), [
            'name_en' => 'required|min:2|max:150',
            'name_hi' => 'required|min:2|max:150',
            'slug' => 'required|min:2|max:150'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/admin/categories/' . $cid . '/edit'), 'error', reset($errors));
            return;
        }

        $updateData = [
            'slug' => slugify((string)$request->post('slug')),
            'name_en' => trim((string)$request->post('name_en')),
            'name_hi' => trim((string)$request->post('name_hi')),
            'description_en' => trim((string)$request->post('description_en', '')),
            'description_hi' => trim((string)$request->post('description_hi', '')),
            'sort_order' => (int)$request->post('sort_order', 0),
            'is_featured' => !empty($request->post('is_featured')) ? 1 : 0,
            'is_active' => !empty($request->post('is_active')) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $uploaded = $request->file('image');
        if ($uploaded && !empty($uploaded['name']) && $uploaded['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($uploaded['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
                $safeName = 'cat_' . time() . '.' . $ext;
                if (move_uploaded_file($uploaded['tmp_name'], PUBLIC_PATH . '/assets/images/categories/' . $safeName)) {
                    $updateData['image'] = $safeName;
                }
            }
        }

        Database::update('categories', $updateData, "id = :id", ['id' => $cid]);
        AdminAuthService::logAudit((int)admin_user()['id'], 'update_category', 'categories', $cid, "Updated category #{$cid}");

        $this->redirect(url('/admin/categories'), 'success', 'Category updated successfully.');
    }

    public function delete(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $cid = (int)$id;

        // Check if products exist in category
        $count = (int)Database::fetchColumn("SELECT COUNT(*) FROM products WHERE category_id = :cid", ['cid' => $cid]);
        if ($count > 0) {
            $this->redirect(url('/admin/categories'), 'error', "Cannot delete category containing {$count} product(s). Please reassign products first.");
            return;
        }

        Database::delete('categories', "id = :id", ['id' => $cid]);
        AdminAuthService::logAudit((int)admin_user()['id'], 'delete_category', 'categories', $cid, "Deleted category #{$cid}");

        $this->redirect(url('/admin/categories'), 'info', 'Category deleted.');
    }
}
