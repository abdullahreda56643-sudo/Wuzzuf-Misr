<?php

class EmployerMiddleware
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

        if (!Auth::isEmployer() && !Auth::isAdmin()) {
            Session::start();
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'هذه الصفحة مخصصة لأصحاب الشركات فقط',
            ];

            header('Location: /dashboard');
            exit;
        }

        return true;
    }
}

