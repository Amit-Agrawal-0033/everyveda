<?php
/**
 * EveryVeda Database & Asset Backup CLI Utility
 * 
 * Usage:
 * php scripts/backup.php
 * Or via cPanel Cron:
 * 0 2 * * * /usr/local/bin/php /home/username/public_html/scripts/backup.php > /dev/null 2>&1
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

// Security check: Only allow CLI or admin secret token
if (php_sapi_name() !== 'cli') {
    $token = $_GET['token'] ?? '';
    $secret = env('CRON_TOKEN', '');
    if (empty($secret) || $token !== $secret) {
        http_response_code(403);
        die("Access denied. CLI execution or valid ?token= required.\n");
    }
}

echo "====================================================\n";
echo " EveryVeda.com Database Backup Utility\n";
echo " Time: " . date('Y-m-d H:i:s') . "\n";
echo "====================================================\n\n";

$backupDir = ROOT_PATH . '/storage/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$timestamp = date('Y-m-d_His');
$sqlFilename = "everyveda_backup_{$timestamp}.sql";
$sqlFilePath = $backupDir . '/' . $sqlFilename;

try {
    $pdo = Database::getConnection();
    $handle = fopen($sqlFilePath, 'w');
    if (!$handle) {
        throw new RuntimeException("Failed to create backup file at {$sqlFilePath}");
    }

    // SQL Header
    fwrite($handle, "-- ====================================================\n");
    fwrite($handle, "-- EveryVeda Database Backup\n");
    fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
    fwrite($handle, "-- Database: " . env('DB_NAME') . "\n");
    fwrite($handle, "-- ====================================================\n\n");
    fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
    fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
    fwrite($handle, "SET time_zone = \"+05:30\";\n\n");

    // Fetch all tables
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    echo "Found " . count($tables) . " tables in database.\n";

    foreach ($tables as $table) {
        echo " - Backing up `{$table}`...";

        // Table Structure
        fwrite($handle, "\n-- ----------------------------------------------------\n");
        fwrite($handle, "-- Table structure for `{$table}`\n");
        fwrite($handle, "-- ----------------------------------------------------\n");
        fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

        $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
        $createRow = $createStmt->fetch(\PDO::FETCH_ASSOC);
        fwrite($handle, $createRow['Create Table'] . ";\n\n");

        // Table Data
        $dataStmt = $pdo->query("SELECT * FROM `{$table}`");
        $rowCount = 0;
        $batch = [];
        $columns = '';

        while ($dataRow = $dataStmt->fetch(\PDO::FETCH_ASSOC)) {
            $rowCount++;
            if ($columns === '') {
                $columns = "`" . implode('`, `', array_keys($dataRow)) . "`";
            }
            $escapedValues = array_map(function ($val) use ($pdo) {
                if ($val === null) {
                    return 'NULL';
                }
                return $pdo->quote((string)$val);
            }, array_values($dataRow));

            $batch[] = "(" . implode(', ', $escapedValues) . ")";

            // Insert in batches of 100 rows
            if (count($batch) >= 100) {
                fwrite($handle, "INSERT INTO `{$table}` ({$columns}) VALUES \n" . implode(",\n", $batch) . ";\n");
                $batch = [];
            }
        }

        // Flush remaining batch
        if (!empty($batch) && $columns !== '') {
            fwrite($handle, "INSERT INTO `{$table}` ({$columns}) VALUES \n" . implode(",\n", $batch) . ";\n");
        }

        echo " ({$rowCount} rows)\n";
    }

    // SQL Footer
    fwrite($handle, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
    fwrite($handle, "-- End of backup\n");
    fclose($handle);

    $originalSize = filesize($sqlFilePath);
    echo "\nBackup written: {$sqlFilename} (" . round($originalSize / 1024, 2) . " KB)\n";

    // Optional GZIP Compression
    if (function_exists('gzopen')) {
        $gzFilePath = $sqlFilePath . '.gz';
        $gz = gzopen($gzFilePath, 'w9');
        if ($gz) {
            $src = fopen($sqlFilePath, 'r');
            while (!feof($src)) {
                gzwrite($gz, fread($src, 1024 * 512));
            }
            fclose($src);
            gzclose($gz);
            unlink($sqlFilePath); // Remove uncompressed SQL
            $compressedSize = filesize($gzFilePath);
            echo "Compressed to: {$sqlFilename}.gz (" . round($compressedSize / 1024, 2) . " KB)\n";
        }
    }

    // Retention Cleanup: Remove backups older than 14 days
    $retentionDays = 14;
    $cutoff = time() - ($retentionDays * 86400);
    $files = glob($backupDir . '/everyveda_backup_*');
    $purged = 0;
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff) {
            unlink($file);
            $purged++;
        }
    }
    if ($purged > 0) {
        echo "Purged {$purged} backup(s) older than {$retentionDays} days.\n";
    }

    echo "\nBackup completed successfully!\n";
    exit(0);
} catch (\Throwable $e) {
    echo "\n[ERROR] Backup failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
