<?php

class Auth
{
    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function id()
    {
        $user = self::user();
        return $user['id'] ?? null;
    }

    public static function login(array $user): void
    {
        Session::flush();
        Session::set('user', $user);
    }

    public static function logout(): void
    {
        Session::flush();
    }

    public static function userType(): ?string
    {
        $user = self::user();
        return $user['type'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::userType() === 'admin';
    }

    public static function isEmployer(): bool
    {
        return self::userType() === 'employer';
    }

    public static function isJobSeeker(): bool
    {
        return self::userType() === 'job_seeker';
    }

    public static function attempt(string $email, string $password): bool
    {
        try {
            $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false;
            }

            if (!password_verify($password, $user['password'])) {
                return false;
            }

            if (($user['status'] ?? 'inactive') !== 'active') {
                return false;
            }

            self::login($user);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}

