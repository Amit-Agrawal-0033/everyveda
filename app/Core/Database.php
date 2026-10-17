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

        try {
            $this->pdo = new PDO($dsn, (string)$user, (string)$pass, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new RuntimeException("Could not connect to the database. Please check your configuration.");
        }
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
