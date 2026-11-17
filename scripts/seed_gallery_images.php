<?php
/**
 * Seed product gallery images into product_images table
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$productBackImages = [
    1 => 'product-chyawanprash-back.svg',
    2 => 'product-shilajit-back.svg',
    3 => 'product-amla-juice-back.svg',
    4 => 'product-pain-oil-back.svg',
    5 => 'product-hair-oil-back.svg',
    6 => 'product-triphala-back.svg',
    7 => 'product-ashwagandha-back.svg',
    8 => 'product-kumkumadi-back.svg',
];

foreach ($productBackImages as $productId => $imagePath) {
    $exists = Database::fetchOne(
        "SELECT id FROM product_images WHERE product_id = :pid AND image_path = :path",
        ['pid' => $productId, 'path' => $imagePath]
    );

    if (!$exists) {
        Database::insert('product_images', [
            'product_id' => $productId,
            'image_path' => $imagePath,
            'alt_text_en' => 'Product Specifications & Directions Back View',
            'alt_text_hi' => 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग',
            'sort_order' => 2,
            'is_primary' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        echo "Inserted secondary gallery image for Product #{$productId} ({$imagePath})\n";
    }
}

echo "Product gallery images seeded successfully!\n";
