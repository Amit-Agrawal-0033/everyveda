<?php
/**
 * Admin Authentication & RBAC Service with Brute-Force Throttling
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class AdminAuthService
{
    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    /**
     * Authenticate admin user
     */
    public static function attempt(string $email, string $password, string $ip): ?array
    {
        $email = strtolower(trim($email));

        $admin = Database::fetchOne(
            "SELECT u.*, r.slug AS role_slug, r.name AS role_name 
             FROM admin_users u
             JOIN admin_roles r ON u.role_id = r.id
             WHERE u.email = :email",
            ['email' => $email]
        );

        if (!$admin) {
            return null;
        }

        // Check if account is temporarily locked due to excessive failed attempts
        if (!empty($admin['locked_until']) && strtotime($admin['locked_until']) > time()) {
            $remaining = ceil((strtotime($admin['locked_until']) - time()) / 60);
            throw new \RuntimeException("Too many failed attempts. Account locked for {$remaining} more minutes.");
        }

        // Verify password
        if (!password_verify($password, $admin['password_hash'])) {
            $failed = (int)$admin['failed_login_attempts'] + 1;
            $lockedUntil = null;

            if ($failed >= self::MAX_FAILED_ATTEMPTS) {
                $lockedUntil = date('Y-m-d H:i:s', time() + (self::LOCKOUT_MINUTES * 60));
            }

            Database::update('admin_users', [
                'failed_login_attempts' => $failed,
                'locked_until' => $lockedUntil
            ], "id = :id", ['id' => $admin['id']]);

            self::logAudit((int)$admin['id'], 'admin_login_failed', 'admin_users', (int)$admin['id'], "Failed login from IP {$ip}", $ip);

            return null;
        }

        if ($admin['status'] !== 'active') {
            throw new \RuntimeException("This administrative account is inactive.");
        }

        // Reset failed login attempts and update last login metadata
        Database::update('admin_users', [
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip
        ], "id = :id", ['id' => $admin['id']]);

        // Load permissions
        $perms = Database::fetchAll(
            "SELECT permission_key FROM admin_permissions WHERE role_id = :role_id",
            ['role_id' => $admin['role_id']]
        );
        $permissions = array_column($perms, 'permission_key');

        // Regenerate session ID for session fixation prevention
        session_regenerate_id(true);

        $_SESSION['admin'] = [
            'id' => (int)$admin['id'],
            'role_id' => (int)$admin['role_id'],
            'role_slug' => $admin['role_slug'],
            'role_name' => $admin['role_name'],
            'name' => $admin['name'],
            'email' => $admin['email'],
            'permissions' => $permissions,
            'last_active' => time()
        ];

        self::logAudit((int)$admin['id'], 'admin_login_success', 'admin_users', (int)$admin['id'], "Successful login from IP {$ip}", $ip);

        return $_SESSION['admin'];
    }

    /**
     * Admin Logout
     */
    public static function logout(?string $ip = null): void
    {
        if (isset($_SESSION['admin']['id'])) {
            self::logAudit((int)$_SESSION['admin']['id'], 'admin_logout', 'admin_users', (int)$_SESSION['admin']['id'], 'Admin logged out', $ip);
        }
        unset($_SESSION['admin']);
        session_regenerate_id(true);
    }

    /**
     * Log Administrative Action for Audit Trail
     */
    public static function logAudit(
        ?int $adminId,
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $details = null,
        ?string $ip = null
    ): void {
        try {
            Database::insert('admin_audit_logs', [
                'admin_id' => $adminId,
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'details' => $details,
                'ip_address' => $ip ?: ($_SERVER['REMOTE_ADDR'] ?? null),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            error_log("Failed to write admin audit log: " . $e->getMessage());
        }
    }
}
