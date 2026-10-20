<?php

class AuthMiddleware
{
    public static function handle(string $redirectTo = '/login'): bool
    {
        if (!Auth::check()) {
            Session::start();
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'يجب تسجيل الدخول أولاً',
            ];

            header('Location: ' . $redirectTo);
            exit;
        }

        return true;
    }
}

