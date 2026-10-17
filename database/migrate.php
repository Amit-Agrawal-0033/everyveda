<?php
/**
 * EveryVeda Database Migration & Seeder CLI Script
 * Can be run via CLI: php database/migrate.php
 */

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = (int)(getenv('DB_PORT') ?: 3306);
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$dbname = getenv('DB_NAME') ?: 'everyveda_db';

echo "====================================================\n";
echo " EveryVeda Database Setup & Migration Script\n";
echo "====================================================\n";
echo "Connecting to MySQL server at {$host}:{$port} as '{$user}'...\n";

try {
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    echo "[✓] Connected successfully.\n";

    echo "Ensuring database `{$dbname}` exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");
    echo "[✓] Database `{$dbname}` selected.\n";

    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new RuntimeException("Schema file not found: {$schemaFile}");
    }

    echo "Executing schema.sql...\n";
    $schemaSql = file_get_contents($schemaFile);
    $pdo->exec($schemaSql);
    echo "[✓] Schema tables created successfully.\n";

    $seedFile = __DIR__ . '/seed.sql';
    if (file_exists($seedFile)) {
        echo "Executing seed.sql...\n";
        $seedSql = file_get_contents($seedFile);
        $pdo->exec($seedSql);
        echo "[✓] Seed data inserted successfully.\n";
    }

    // Verify table counts
    $tablesStmt = $pdo->query("SHOW TABLES");
    $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Total tables in `{$dbname}`: " . count($tables) . "\n";
    foreach ($tables as $t) {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        echo " - {$t}: {$count} records\n";
    }

    echo "\n[✓] Database setup completed successfully!\n";
} catch (PDOException $e) {
    fwrite(STDERR, "[✗] Database Error: " . $e->getMessage() . "\n");
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, "[✗] Error: " . $e->getMessage() . "\n");
    exit(1);
}
