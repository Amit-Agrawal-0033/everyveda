<?php
/**
 * Database Upgrade: Adds HSN Codes and Granular Tax Columns (CGST, SGST, IGST, Taxable Amount)
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$pdo = Database::pdo();

echo "Running EveryVeda Tax & HSN Schema Upgrade...\n";

// 1. Products: HSN Code
$cols = $pdo->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('hsn_code', $cols, true)) {
    $pdo->exec("ALTER TABLE products ADD COLUMN hsn_code VARCHAR(20) NOT NULL DEFAULT '30049011' AFTER tax_rate");
    echo "[✓] Added `hsn_code` to products table.\n";
} else {
    echo "[i] `hsn_code` already exists in products.\n";
}

// 2. Orders: Tax Breakdown
$ordCols = $pdo->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('taxable_amount', $ordCols, true)) {
    $pdo->exec("ALTER TABLE orders ADD COLUMN taxable_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER shipping_fee");
    $pdo->exec("ALTER TABLE orders ADD COLUMN cgst_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER taxable_amount");
    $pdo->exec("ALTER TABLE orders ADD COLUMN sgst_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER cgst_amount");
    $pdo->exec("ALTER TABLE orders ADD COLUMN igst_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER sgst_amount");
    $pdo->exec("ALTER TABLE orders ADD COLUMN prices_include_tax TINYINT(1) NOT NULL DEFAULT 1 AFTER igst_amount");
    echo "[✓] Added granular tax columns to orders table.\n";
} else {
    echo "[i] Tax columns already exist in orders.\n";
}

// 3. Order Items: Tax Breakdown & HSN
$itemCols = $pdo->query("SHOW COLUMNS FROM order_items")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('hsn_code', $itemCols, true)) {
    $pdo->exec("ALTER TABLE order_items ADD COLUMN hsn_code VARCHAR(20) NOT NULL DEFAULT '30049011' AFTER sku");
    $pdo->exec("ALTER TABLE order_items ADD COLUMN taxable_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER tax_amount");
    $pdo->exec("ALTER TABLE order_items ADD COLUMN cgst_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER taxable_amount");
    $pdo->exec("ALTER TABLE order_items ADD COLUMN sgst_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER cgst_amount");
    $pdo->exec("ALTER TABLE order_items ADD COLUMN igst_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER sgst_amount");
    echo "[✓] Added granular tax columns to order_items table.\n";
} else {
    echo "[i] Tax columns already exist in order_items.\n";
}

// 4. Update seed products with accurate GST classification and HSN codes
$pdo->exec("UPDATE products SET tax_rate = 12.00, hsn_code = '30049011' WHERE slug = 'everyveda-royal-chyawanprash-gold'");
$pdo->exec("UPDATE products SET tax_rate = 12.00, hsn_code = '30049011' WHERE slug = 'everyveda-pure-himalayan-shilajit-resin'");
$pdo->exec("UPDATE products SET tax_rate = 12.00, hsn_code = '20098990' WHERE slug = 'organic-wild-amla-juice'");
$pdo->exec("UPDATE products SET tax_rate = 12.00, hsn_code = '30049011' WHERE slug = 'maha-narayan-joint-relief-oil'");
$pdo->exec("UPDATE products SET tax_rate = 18.00, hsn_code = '33059010' WHERE slug = 'bhringraj-brahmi-herbal-hair-oil'");
$pdo->exec("UPDATE products SET tax_rate = 5.00,  hsn_code = '12119029' WHERE slug = 'triphala-churna-digestive-cleanse'");
$pdo->exec("UPDATE products SET tax_rate = 12.00, hsn_code = '30049011' WHERE slug = 'ashwagandha-ksheerpak-vitality-capsules'");
$pdo->exec("UPDATE products SET tax_rate = 18.00, hsn_code = '33049990' WHERE slug = 'pure-kumkumadi-radiance-tailam'");
echo "[✓] Updated catalog products with category-specific GST rates (5%, 12%, 18%) and HSN codes.\n";

// 5. Add store tax configuration in settings
$existingSetting = Database::fetchOne("SELECT `key` FROM settings WHERE `key` = 'prices_include_tax'");
if (!$existingSetting) {
    Database::insert('settings', [
        'key' => 'prices_include_tax',
        'value' => '1',
        'group_name' => 'tax',
        'description' => 'Whether catalog product prices include GST (1 for Inclusive, 0 for Exclusive)'
    ]);
    echo "[✓] Added `prices_include_tax` setting.\n";
}

echo "\nUpgrade completed successfully!\n";
