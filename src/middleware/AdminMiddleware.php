<?php

class AdminMiddleware
{
    public static function handle(string $redirectTo = '/admin/login'): bool
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

        if (!Auth::isAdmin()) {
            Session::start();
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'ليس لديك صلاحية الوصول إلى لوحة الإدارة',
            ];

            header('Location: /');
            exit;
        }

        return true;
    }
}

