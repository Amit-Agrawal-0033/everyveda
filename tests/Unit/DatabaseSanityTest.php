<?php
/**
 * Database Integrity and Seed Data Sanity Tests
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database;

class DatabaseSanityTest
{
    public function testDatabaseConnection(): void
    {
        $pdo = Database::getConnection();
        assert($pdo instanceof \PDO, "Database connection must return a valid PDO instance");
        
        $version = $pdo->query("SELECT VERSION()")->fetchColumn();
        assert(!empty($version), "Database version query must return non-empty string");
    }

    public function testSeedProductsAndVariants(): void
    {
        $productCount = (int)Database::fetchColumn("SELECT COUNT(*) FROM products WHERE status = 'published'");
        assert($productCount >= 8, "Expected at least 8 active seed products, got {$productCount}");

        $variantCount = (int)Database::fetchColumn("SELECT COUNT(*) FROM product_variants WHERE is_active = 1");
        assert($variantCount >= 8, "Expected at least 8 active product variants, got {$variantCount}");

        // Verify translations exist
        $transEn = (int)Database::fetchColumn("SELECT COUNT(*) FROM product_translations WHERE locale = 'en'");
        $transHi = (int)Database::fetchColumn("SELECT COUNT(*) FROM product_translations WHERE locale = 'hi'");
        assert($transEn >= 8, "Expected at least 8 English product translations, got {$transEn}");
        assert($transHi >= 8, "Expected at least 8 Hindi product translations, got {$transHi}");
    }

    public function testSeedCategories(): void
    {
        $catCount = (int)Database::fetchColumn("SELECT COUNT(*) FROM categories WHERE is_active = 1");
        assert($catCount >= 6, "Expected at least 6 active categories, got {$catCount}");
    }

    public function testSeedCoupons(): void
    {
        $couponCount = (int)Database::fetchColumn("SELECT COUNT(*) FROM coupons WHERE is_active = 1");
        assert($couponCount >= 3, "Expected at least 3 active promotional coupons, got {$couponCount}");
    }

    public function testSeedAdminUser(): void
    {
        $admin = Database::fetchOne("SELECT * FROM admin_users WHERE email = :email", ['email' => 'admin@everyveda.com']);
        assert(!empty($admin), "Default super admin admin@everyveda.com must exist");
        assert(!empty($admin['password_hash']), "Admin must have a hashed password");
        assert(password_verify('Admin@12345', $admin['password_hash']), "Default seed admin password must verify");
    }

    public function testSettingsTable(): void
    {
        $settingsCount = (int)Database::fetchColumn("SELECT COUNT(*) FROM settings");
        assert($settingsCount >= 20, "Expected at least 20 system settings rows, got {$settingsCount}");
    }
}
