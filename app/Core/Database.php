<?php
/**
 * EveryVeda Database PDO Singleton & Transaction Manager
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host = env('DB_HOST', '127.0.0.1');
        $port = (int)env('DB_PORT', 3306);
        $dbname = env('DB_NAME', 'everyveda_db');
        $user = env('DB_USER', 'root');
        $pass = env('DB_PASS', '');
        $charset = env('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$charset}_unicode_ci",
        ];

        // Automatic SSL mode for Cloud Databases (Aiven, TiDB, PlanetScale, etc.)
        if (env('DB_SSL', false) || $port !== 3306 || str_contains($host, 'aivencloud.com') || str_contains($host, 'tidbcloud.com')) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        try {
            $this->pdo = new PDO($dsn, (string)$user, (string)$pass, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());

            $isVercel = isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || !empty(getenv('VERCEL'));
            $isRemoteHost = !empty($_SERVER['HTTP_HOST']) && !in_array($_SERVER['HTTP_HOST'], ['localhost:8000', '127.0.0.1:8000', 'localhost', '127.0.0.1'], true);

            if ($isVercel || $isRemoteHost) {
                http_response_code(503);
                header('Content-Type: text/html; charset=utf-8');
                echo self::renderCloudDbNotice($e->getMessage(), $host, $port, $dbname, $user);
                exit;
            }

            throw new RuntimeException("Could not connect to the database. Please check your configuration: " . $e->getMessage());
        }
    }

    private static function renderCloudDbNotice(string $errorMsg, string $host, int $port, string $dbname, string $user): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup Required — EveryVeda</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #FAF7EF; color: #26332B; margin: 0; padding: 40px 20px; line-height: 1.6; }
        .container { max-width: 680px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5ded0; }
        .brand { font-size: 26px; font-weight: 800; color: #214D36; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .brand span { color: #C6A45C; }
        h1 { font-size: 22px; color: #163524; margin-top: 0; margin-bottom: 12px; }
        p { color: #55665b; margin-bottom: 16px; }
        .alert { background: #fdf5e6; border-left: 4px solid #C6A45C; padding: 14px 18px; border-radius: 6px; margin: 20px 0; font-size: 14px; }
        .step { background: #f9fbf9; border: 1px solid #e1ebe3; padding: 16px; border-radius: 8px; margin-bottom: 14px; }
        .step strong { color: #214D36; display: block; margin-bottom: 6px; }
        code { background: #ece7db; padding: 3px 6px; border-radius: 4px; font-size: 13px; color: #214D36; }
        ul { margin: 8px 0 0 20px; padding: 0; }
        li { margin-bottom: 6px; font-size: 14px; }
        .footer { margin-top: 30px; font-size: 13px; color: #88998d; text-align: center; border-top: 1px solid #f0eae0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">Every<span>Veda</span> 🌿</div>
        <h1>Cloud Database Connection Required</h1>
        <p>Your <strong>EveryVeda Core PHP</strong> application is live on Vercel! To connect your product catalogue, shopping cart, and order database, configure your cloud MySQL database credentials.</p>
        
        <div class="alert">
            <strong>Current Status:</strong> Could not connect to MySQL at <code>{$host}:{$port}</code> (Database: <code>{$dbname}</code>).<br>
            <small style="color:#777;">Error: {$errorMsg}</small>
        </div>

        <div class="step">
            <strong>Step 1: Get a Free Cloud MySQL Database</strong>
            You can provision a 100% free cloud MySQL database in 60 seconds on:
            <ul>
                <li><strong><a href="https://aiven.io" target="_blank" style="color:#214D36;">Aiven.io</a></strong> (Free MySQL Cloud Database)</li>
                <li><strong><a href="https://tidbcloud.com" target="_blank" style="color:#214D36;">TiDB Cloud</a></strong> (Free Serverless MySQL compatible tier)</li>
                <li><strong><a href="https://railway.app" target="_blank" style="color:#214D36;">Railway.app</a></strong> or standard cPanel Remote MySQL</li>
            </ul>
        </div>

        <div class="step">
            <strong>Step 2: Add Environment Variables in Vercel Dashboard</strong>
            Go to <strong>Vercel Dashboard &rarr; Project Settings &rarr; Environment Variables</strong> and add:
            <ul>
                <li><code>DB_HOST</code> = (Your cloud database hostname)</li>
                <li><code>DB_PORT</code> = <code>3306</code></li>
                <li><code>DB_NAME</code> = (Your database name)</li>
                <li><code>DB_USER</code> = (Your database user)</li>
                <li><code>DB_PASS</code> = (Your database password)</li>
            </ul>
        </div>

        <div class="step">
            <strong>Step 3: Import Schema & Seed Data</strong>
            Execute <code>database/schema.sql</code> and <code>database/seed.sql</code> in your cloud database (via phpMyAdmin or cloud console) to load the complete 8-product wellness catalog.
        </div>

        <div class="footer">
            EveryVeda.com • Classical Shastra Ayurveda E-Commerce • Production Vercel Serverless
        </div>
    </div>
</body>
</html>
HTML;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function pdo(): PDO
    {
        return self::getInstance()->pdo;
    }

    public static function getConnection(): PDO
    {
        return self::pdo();
    }

    /**
     * Execute a prepared statement with parameter binding
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch single row
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Fetch all matching rows
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch single scalar value (e.g. COUNT(*))
     */
    public static function fetchColumn(string $sql, array $params = [], int $column = 0): mixed
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchColumn($column);
    }

    /**
     * Insert record and return last insert ID
     */
    public static function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $fields = implode('`, `', $columns);
        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO `{$table}` (`{$fields}`) VALUES ({$placeholders})";
        self::query($sql, $data);

        return (int)self::pdo()->lastInsertId();
    }

    /**
     * Update table with conditions
     */
    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClauses = [];
        $params = [];

        foreach ($data as $col => $val) {
            $setClauses[] = "`{$col}` = :set_{$col}";
            $params["set_{$col}"] = $val;
        }

        $params = array_merge($params, $whereParams);
        $setString = implode(', ', $setClauses);
        $sql = "UPDATE `{$table}` SET {$setString} WHERE {$where}";

        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete from table
     */
    public static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::pdo()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::pdo()->commit();
    }

    /**
     * Rollback transaction
     */
    public static function rollBack(): bool
    {
        return self::pdo()->rollBack();
    }

    /**
     * Execute a callback inside an ACID transaction
     */
    public static function transaction(callable $callback): mixed
    {
        self::beginTransaction();
        try {
            $result = $callback(self::pdo());
            self::commit();
            return $result;
        } catch (\Throwable $e) {
            if (self::pdo()->inTransaction()) {
                self::rollBack();
            }
            throw $e;
        }
    }
}
