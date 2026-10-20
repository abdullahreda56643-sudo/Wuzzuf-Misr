<?php

class GuestMiddleware
{
    public static function handle(string $redirectTo = '/dashboard'): bool
    {
        if (Auth::check()) {
            Session::start();
            $_SESSION['flash'] = [
                'type' => 'info',
                'message' => 'أنت مسجل الدخول بالفعل',
            ];

            header('Location: ' . $redirectTo);
            exit;
        }

        return true;
    }
}

