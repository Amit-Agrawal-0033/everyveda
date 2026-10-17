<?php
/**
 * Admin Product Management Controller (Bilingual, Variants, Inventory, Images)
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Category;
use App\Services\AdminAuthService;

class ProductController extends Controller
{
    /**
     * List Products
     */
    public function index(Request $request): void
    {
        $q = trim((string)$request->get('q', ''));
        $categoryId = $request->get('category');
        $status = $request->get('status');

        $where = ["1=1"];
        $params = [];

        if ($q !== '') {
            $where[] = "(p.sku LIKE :q OR p.slug LIKE :q OR pt_en.name LIKE :q OR pt_hi.name LIKE :q)";
            $params['q'] = '%' . $q . '%';
        }

        if ($categoryId && $categoryId !== 'all') {
            $where[] = "p.category_id = :cid";
            $params['cid'] = (int)$categoryId;
        }

        if ($status && $status !== 'all') {
            $where[] = "p.status = :status";
            $params['status'] = $status;
        }

        $whereClause = implode(' AND ', $where);

        $page = max(1, (int)$request->get('page', 1));
        $perPage = 15;
        $total = (int)Database::fetchColumn(
            "SELECT COUNT(DISTINCT p.id) 
             FROM products p
             LEFT JOIN product_translations pt_en ON (p.id = pt_en.product_id AND pt_en.locale = 'en')
             LEFT JOIN product_translations pt_hi ON (p.id = pt_hi.product_id AND pt_hi.locale = 'hi')
             WHERE {$whereClause}",
            $params
        );

        $totalPages = max(1, (int)ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT 
                    p.*,
                    COALESCE(pt_en.name, p.slug) AS name_en,
                    COALESCE(pt_hi.name, pt_en.name, p.slug) AS name_hi,
                    c.name_en AS category_name,
                    COUNT(pv.id) AS variant_count
                FROM products p
                LEFT JOIN product_translations pt_en ON (p.id = pt_en.product_id AND pt_en.locale = 'en')
                LEFT JOIN product_translations pt_hi ON (p.id = pt_hi.product_id AND pt_hi.locale = 'hi')
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_variants pv ON p.id = pv.product_id
                WHERE {$whereClause}
                GROUP BY p.id
                ORDER BY p.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $products = Database::fetchAll($sql, $params);
        $categories = Category::getActiveWithCounts();

        $this->adminView('admin/products/index', [
            'meta_title' => 'Products Management — EveryVeda Admin',
            'products' => $products,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'categories' => $categories,
            'filters' => [
                'q' => $q,
                'category' => $categoryId,
                'status' => $status
            ]
        ]);
    }

    /**
     * Create Product Form
     */
    public function create(Request $request): void
    {
        $categories = Database::fetchAll("SELECT id, name_en, name_hi FROM categories ORDER BY sort_order ASC");

        $this->adminView('admin/products/create', [
            'meta_title' => 'Add New Product — EveryVeda Admin',
            'categories' => $categories
        ]);
    }

    /**
     * Store New Product
     */
    public function store(Request $request): void
    {
        $this->validateCsrf($request);

        $errors = $this->validate($request->all(), [
            'sku' => 'required|min:3|max:100',
            'slug' => 'required|min:3|max:191',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric',
            'name_en' => 'required|min:2|max:255',
            'name_hi' => 'required|min:2|max:255'
        ]);

        if (!empty($errors)) {
            flash_input($request->all());
            $this->redirect(url('/admin/products/create'), 'error', reset($errors));
            return;
        }

        $sku = strtoupper(trim((string)$request->post('sku')));
        $slug = slugify((string)$request->post('slug'));

        // Check SKU uniqueness
        $existing = Database::fetchOne("SELECT id FROM products WHERE sku = :sku OR slug = :slug", ['sku' => $sku, 'slug' => $slug]);
        if ($existing) {
            flash_input($request->all());
            $this->redirect(url('/admin/products/create'), 'error', 'A product with this SKU or URL slug already exists.');
            return;
        }

        // Handle Image Upload if provided
        $primaryImage = 'placeholder.svg';
        $uploadedFile = $request->file('primary_image');
        if ($uploadedFile && !empty($uploadedFile['name']) && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
                $safeName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = PUBLIC_PATH . '/assets/images/products/' . $safeName;
                if (move_uploaded_file($uploadedFile['tmp_name'], $dest)) {
                    $primaryImage = $safeName;
                }
            }
        }

        $adminId = (int)admin_user()['id'];

        $productId = Database::transaction(function () use ($request, $sku, $slug, $primaryImage, $adminId) {
            $pid = Database::insert('products', [
                'category_id' => $request->post('category_id') ? (int)$request->post('category_id') : null,
                'sku' => $sku,
                'slug' => $slug,
                'price' => (float)$request->post('price'),
                'sale_price' => $request->post('sale_price') ? (float)$request->post('sale_price') : null,
                'stock_quantity' => (int)$request->post('stock_quantity', 0),
                'low_stock_threshold' => (int)$request->post('low_stock_threshold', 5),
                'manage_stock' => !empty($request->post('manage_stock')) ? 1 : 0,
                'tax_rate' => (float)$request->post('tax_rate', 12.00),
                'weight_grams' => (int)$request->post('weight_grams', 250),
                'is_featured' => !empty($request->post('is_featured')) ? 1 : 0,
                'is_best_seller' => !empty($request->post('is_best_seller')) ? 1 : 0,
                'is_new_arrival' => !empty($request->post('is_new_arrival')) ? 1 : 0,
                'status' => (string)$request->post('status', 'published'),
                'primary_image' => $primaryImage,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Insert English translation
            Database::insert('product_translations', [
                'product_id' => $pid,
                'locale' => 'en',
                'name' => trim((string)$request->post('name_en')),
                'short_description' => trim((string)$request->post('short_description_en', '')),
                'description' => trim((string)$request->post('description_en', '')),
                'ingredients' => trim((string)$request->post('ingredients_en', '')),
                'directions' => trim((string)$request->post('directions_en', '')),
                'storage_info' => trim((string)$request->post('storage_info_en', '')),
                'warnings' => trim((string)$request->post('warnings_en', '')),
                'meta_title' => trim((string)$request->post('meta_title_en', '')),
                'meta_description' => trim((string)$request->post('meta_description_en', ''))
            ]);

            // Insert Hindi translation
            Database::insert('product_translations', [
                'product_id' => $pid,
                'locale' => 'hi',
                'name' => trim((string)$request->post('name_hi')),
                'short_description' => trim((string)$request->post('short_description_hi', '')),
                'description' => trim((string)$request->post('description_hi', '')),
                'ingredients' => trim((string)$request->post('ingredients_hi', '')),
                'directions' => trim((string)$request->post('directions_hi', '')),
                'storage_info' => trim((string)$request->post('storage_info_hi', '')),
                'warnings' => trim((string)$request->post('warnings_hi', '')),
                'meta_title' => trim((string)$request->post('meta_title_hi', '')),
                'meta_description' => trim((string)$request->post('meta_description_hi', ''))
            ]);

            AdminAuthService::logAudit($adminId, 'create_product', 'products', $pid, "Created product SKU {$sku}");

            return $pid;
        });

        $this->redirect(url('/admin/products/' . $productId . '/edit'), 'success', 'Product created successfully. You may now configure pack variants or upload gallery photos.');
    }

    /**
     * Edit Product Form
     */
    public function edit(Request $request, string $id): void
    {
        $productId = (int)$id;
        $product = Database::fetchOne("SELECT * FROM products WHERE id = :id", ['id' => $productId]);

        if (!$product) {
            $this->redirect(url('/admin/products'), 'error', 'Product not found.');
            return;
        }

        $transEn = Database::fetchOne("SELECT * FROM product_translations WHERE product_id = :id AND locale = 'en'", ['id' => $productId]) ?: [];
        $transHi = Database::fetchOne("SELECT * FROM product_translations WHERE product_id = :id AND locale = 'hi'", ['id' => $productId]) ?: [];

        $variants = Database::fetchAll("SELECT * FROM product_variants WHERE product_id = :id ORDER BY id ASC", ['id' => $productId]);
        $categories = Database::fetchAll("SELECT id, name_en, name_hi FROM categories ORDER BY sort_order ASC");

        $this->adminView('admin/products/edit', [
            'meta_title' => 'Edit Product #' . $productId . ' — EveryVeda Admin',
            'product' => $product,
            'transEn' => $transEn,
            'transHi' => $transHi,
            'variants' => $variants,
            'categories' => $categories
        ]);
    }

    /**
     * Update Product
     */
    public function update(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $productId = (int)$id;

        $errors = $this->validate($request->all(), [
            'sku' => 'required|min:3|max:100',
            'slug' => 'required|min:3|max:191',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric',
            'name_en' => 'required|min:2|max:255',
            'name_hi' => 'required|min:2|max:255'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/admin/products/' . $productId . '/edit'), 'error', reset($errors));
            return;
        }

        $sku = strtoupper(trim((string)$request->post('sku')));
        $slug = slugify((string)$request->post('slug'));

        // Handle Image upload if new one selected
        $updateFields = [
            'category_id' => $request->post('category_id') ? (int)$request->post('category_id') : null,
            'sku' => $sku,
            'slug' => $slug,
            'price' => (float)$request->post('price'),
            'sale_price' => $request->post('sale_price') ? (float)$request->post('sale_price') : null,
            'stock_quantity' => (int)$request->post('stock_quantity', 0),
            'low_stock_threshold' => (int)$request->post('low_stock_threshold', 5),
            'manage_stock' => !empty($request->post('manage_stock')) ? 1 : 0,
            'tax_rate' => (float)$request->post('tax_rate', 12.00),
            'weight_grams' => (int)$request->post('weight_grams', 250),
            'is_featured' => !empty($request->post('is_featured')) ? 1 : 0,
            'is_best_seller' => !empty($request->post('is_best_seller')) ? 1 : 0,
            'is_new_arrival' => !empty($request->post('is_new_arrival')) ? 1 : 0,
            'status' => (string)$request->post('status', 'published'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $uploadedFile = $request->file('primary_image');
        if ($uploadedFile && !empty($uploadedFile['name']) && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
                $safeName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = PUBLIC_PATH . '/assets/images/products/' . $safeName;
                if (move_uploaded_file($uploadedFile['tmp_name'], $dest)) {
                    $updateFields['primary_image'] = $safeName;
                }
            }
        }

        Database::transaction(function () use ($productId, $updateFields, $request) {
            Database::update('products', $updateFields, "id = :id", ['id' => $productId]);

            // Update or Insert Translations
            Database::query(
                "INSERT INTO product_translations (product_id, locale, name, short_description, description, ingredients, directions, storage_info, warnings, meta_title, meta_description)
                 VALUES (:pid, 'en', :name, :short_desc, :desc, :ingredients, :directions, :storage, :warnings, :mtitle, :mdesc)
                 ON DUPLICATE KEY UPDATE name = VALUES(name), short_description = VALUES(short_description), description = VALUES(description), ingredients = VALUES(ingredients), directions = VALUES(directions), storage_info = VALUES(storage_info), warnings = VALUES(warnings), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description)",
                [
                    'pid' => $productId,
                    'name' => trim((string)$request->post('name_en')),
                    'short_desc' => trim((string)$request->post('short_description_en', '')),
                    'desc' => trim((string)$request->post('description_en', '')),
                    'ingredients' => trim((string)$request->post('ingredients_en', '')),
                    'directions' => trim((string)$request->post('directions_en', '')),
                    'storage' => trim((string)$request->post('storage_info_en', '')),
                    'warnings' => trim((string)$request->post('warnings_en', '')),
                    'mtitle' => trim((string)$request->post('meta_title_en', '')),
                    'mdesc' => trim((string)$request->post('meta_description_en', ''))
                ]
            );

            Database::query(
                "INSERT INTO product_translations (product_id, locale, name, short_description, description, ingredients, directions, storage_info, warnings, meta_title, meta_description)
                 VALUES (:pid, 'hi', :name, :short_desc, :desc, :ingredients, :directions, :storage, :warnings, :mtitle, :mdesc)
                 ON DUPLICATE KEY UPDATE name = VALUES(name), short_description = VALUES(short_description), description = VALUES(description), ingredients = VALUES(ingredients), directions = VALUES(directions), storage_info = VALUES(storage_info), warnings = VALUES(warnings), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description)",
                [
                    'pid' => $productId,
                    'name' => trim((string)$request->post('name_hi')),
                    'short_desc' => trim((string)$request->post('short_description_hi', '')),
                    'desc' => trim((string)$request->post('description_hi', '')),
                    'ingredients' => trim((string)$request->post('ingredients_hi', '')),
                    'directions' => trim((string)$request->post('directions_hi', '')),
                    'storage' => trim((string)$request->post('storage_info_hi', '')),
                    'warnings' => trim((string)$request->post('warnings_hi', '')),
                    'mtitle' => trim((string)$request->post('meta_title_hi', '')),
                    'mdesc' => trim((string)$request->post('meta_description_hi', ''))
                ]
            );

            AdminAuthService::logAudit((int)admin_user()['id'], 'update_product', 'products', $productId, "Updated product #{$productId}");
        });

        $this->redirect(url('/admin/products/' . $productId . '/edit'), 'success', 'Product updated successfully.');
    }

    /**
     * Save Variant (Add or Update)
     */
    public function saveVariant(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $productId = (int)$id;

        $errors = $this->validate($request->all(), [
            'sku' => 'required',
            'variant_name_en' => 'required',
            'variant_name_hi' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/admin/products/' . $productId . '/edit#variants'), 'error', reset($errors));
            return;
        }

        $variantId = $request->post('variant_id') ? (int)$request->post('variant_id') : null;

        $data = [
            'product_id' => $productId,
            'sku' => strtoupper(trim((string)$request->post('sku'))),
            'variant_name_en' => trim((string)$request->post('variant_name_en')),
            'variant_name_hi' => trim((string)$request->post('variant_name_hi')),
            'price' => (float)$request->post('price'),
            'sale_price' => $request->post('sale_price') ? (float)$request->post('sale_price') : null,
            'stock_quantity' => (int)$request->post('stock_quantity'),
            'weight_grams' => (int)$request->post('weight_grams', 250),
            'is_default' => !empty($request->post('is_default')) ? 1 : 0,
            'is_active' => 1
        ];

        if ($variantId) {
            Database::update('product_variants', $data, "id = :id AND product_id = :pid", ['id' => $variantId, 'pid' => $productId]);
        } else {
            Database::insert('product_variants', $data);
        }

        $this->redirect(url('/admin/products/' . $productId . '/edit#variants'), 'success', 'Variant saved successfully.');
    }

    /**
     * Delete Variant
     */
    public function deleteVariant(Request $request, string $id, string $variantId): void
    {
        $this->validateCsrf($request);
        Database::delete('product_variants', "id = :vid AND product_id = :pid", ['vid' => (int)$variantId, 'pid' => (int)$id]);
        $this->redirect(url('/admin/products/' . $id . '/edit#variants'), 'info', 'Variant deleted.');
    }

    /**
     * Delete Product (Soft archive or delete)
     */
    public function delete(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $productId = (int)$id;

        // Archive product to preserve historical orders
        Database::update('products', ['status' => 'archived'], "id = :id", ['id' => $productId]);
        AdminAuthService::logAudit((int)admin_user()['id'], 'archive_product', 'products', $productId, "Archived product #{$productId}");

        $this->redirect(url('/admin/products'), 'info', 'Product moved to archive.');
    }
}
