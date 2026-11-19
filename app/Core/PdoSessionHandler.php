<?php
/**
 * Database Session Handler for EveryVeda.com
 * Persists PHP Sessions in MySQL (Aiven Cloud) for Seamless Serverless Support
 */

declare(strict_types=1);

namespace App\Core;

use SessionHandlerInterface;
use Throwable;

class PdoSessionHandler implements SessionHandlerInterface
{
    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        try {
            $stmt = Database::pdo()->prepare(
                "SELECT `data` FROM `sessions` WHERE `id` = :id AND `last_activity` >= :cutoff LIMIT 1"
            );
            $cutoff = time() - (int)env('SESSION_LIFETIME', 7200);
            $stmt->execute(['id' => $id, 'cutoff' => $cutoff]);
            $data = $stmt->fetchColumn();

            return is_string($data) ? $data : '';
        } catch (Throwable $e) {
            error_log("PdoSessionHandler read error: " . $e->getMessage());
            return '';
        }
    }

    public function write(string $id, string $data): bool
    {
        try {
            $stmt = Database::pdo()->prepare(
                "INSERT INTO `sessions` (`id`, `last_activity`, `data`) VALUES (:id, :now, :data)
                 ON DUPLICATE KEY UPDATE `last_activity` = VALUES(`last_activity`), `data` = VALUES(`data`)"
            );
            return $stmt->execute([
                'id' => $id,
                'now' => time(),
                'data' => $data
            ]);
        } catch (Throwable $e) {
            error_log("PdoSessionHandler write error: " . $e->getMessage());
            return false;
        }
    }

    public function destroy(string $id): bool
    {
        try {
            $stmt = Database::pdo()->prepare("DELETE FROM `sessions` WHERE `id` = :id");
            return $stmt->execute(['id' => $id]);
        } catch (Throwable $e) {
            error_log("PdoSessionHandler destroy error: " . $e->getMessage());
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false
    {
        try {
            $stmt = Database::pdo()->prepare("DELETE FROM `sessions` WHERE `last_activity` < :cutoff");
            $stmt->execute(['cutoff' => time() - $max_lifetime]);
            return $stmt->rowCount();
        } catch (Throwable $e) {
            error_log("PdoSessionHandler gc error: " . $e->getMessage());
            return 0;
        }
    }
}
