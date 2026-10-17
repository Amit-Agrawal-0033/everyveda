<?php
/**
 * Customer Authentication & Profile Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class AuthService
{
    /**
     * Register new customer
     */
    public static function register(array $data): array
    {
        $existing = Database::fetchOne("SELECT id FROM users WHERE email = :email", ['email' => $data['email']]);
        if ($existing) {
            throw new \InvalidArgumentException("An account with this email address already exists.");
        }

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $userId = Database::insert('users', [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => preg_replace('/[^\d]/', '', $data['phone'] ?? ''),
            'password_hash' => $passwordHash,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $user = Database::fetchOne("SELECT id, name, email, phone, status, created_at FROM users WHERE id = :id", ['id' => $userId]);

        self::loginUser($user);
        return $user;
    }

    /**
     * Authenticate customer credentials
     */
    public static function attempt(string $email, string $password): ?array
    {
        $email = strtolower(trim($email));
        $user = Database::fetchOne(
            "SELECT id, name, email, phone, password_hash, status, created_at FROM users WHERE email = :email",
            ['email' => $email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        if ($user['status'] !== 'active') {
            throw new \RuntimeException("This account is currently {$user['status']}. Please contact customer support.");
        }

        unset($user['password_hash']);
        self::loginUser($user);

        return $user;
    }

    /**
     * Set user in session and regenerate session ID
     */
    public static function loginUser(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'created_at' => $user['created_at']
        ];

        // Merge guest cart into customer cart
        CartService::mergeGuestCartIntoUser((int)$user['id']);
    }

    /**
     * Log user out
     */
    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    /**
     * Update user profile
     */
    public static function updateProfile(int $userId, array $data): void
    {
        Database::update('users', [
            'name' => trim($data['name']),
            'phone' => preg_replace('/[^\d]/', '', $data['phone'] ?? '')
        ], "id = :id", ['id' => $userId]);

        $_SESSION['user']['name'] = trim($data['name']);
        $_SESSION['user']['phone'] = preg_replace('/[^\d]/', '', $data['phone'] ?? '');
    }

    /**
     * Change user password
     */
    public static function changePassword(int $userId, string $oldPassword, string $newPassword): void
    {
        $user = Database::fetchOne("SELECT password_hash FROM users WHERE id = :id", ['id' => $userId]);
        if (!$user || !password_verify($oldPassword, $user['password_hash'])) {
            throw new \InvalidArgumentException("Current password is incorrect.");
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        Database::update('users', ['password_hash' => $hash], "id = :id", ['id' => $userId]);
    }

    /**
     * Generate password reset token
     */
    public static function createPasswordResetToken(string $email): ?string
    {
        $user = Database::fetchOne("SELECT id, name FROM users WHERE email = :email AND status = 'active'", ['email' => $email]);
        if (!$user) {
            return null; // Return null to prevent email enumeration or return fake success
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour validity

        Database::insert('password_reset_tokens', [
            'email' => $email,
            'token' => hash('sha256', $token),
            'expires_at' => $expires,
            'is_used' => 0
        ]);

        return $token;
    }

    /**
     * Reset password using token
     */
    public static function resetPassword(string $token, string $newPassword): bool
    {
        $hashedToken = hash('sha256', $token);
        $record = Database::fetchOne(
            "SELECT * FROM password_reset_tokens WHERE token = :token AND is_used = 0 AND expires_at > NOW()",
            ['token' => $hashedToken]
        );

        if (!$record) {
            return false;
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        Database::update('users', ['password_hash' => $newHash], "email = :email", ['email' => $record['email']]);
        Database::update('password_reset_tokens', ['is_used' => 1], "id = :id", ['id' => $record['id']]);

        return true;
    }
}
