<?php

class AdminAuthController extends Controller
{
    public function login()
    {
        if (Auth::check() && Auth::isAdmin()) {
            $this->redirect('/admin');
        }

        if ($this->isPost()) {
            $email = trim((string) $this->input('email', ''));
            $password = (string) $this->input('password', '');

            if ($email === '' || $password === '') {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'يرجى إدخال البريد الإلكتروني وكلمة المرور'];
                $this->redirect('/admin/login');
            }

            try {
                $pdo = $this->db();
                $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND type = ? LIMIT 1');
                $stmt->execute([$email, 'admin']);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user || !password_verify($password, $user['password'])) {
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'بيانات الدخول غير صحيحة'];
                    $this->redirect('/admin/login');
                }

                Auth::login($user);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'تم تسجيل الدخول بنجاح'];
                $this->redirect('/admin');
            } catch (Throwable $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'حدث خطأ أثناء تسجيل الدخول'];
                $this->redirect('/admin/login');
            }
        }

        echo '<!doctype html><html lang="ar" dir="rtl"><head>';
        echo '<meta charset="utf-8"><title>تسجيل دخول المدير</title>';
        echo '<style>body{font-family:Tahoma,Arial,sans-serif;background:#f5f7fb;padding:40px;} .box{max-width:420px;margin:80px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,.08);} input{width:100%;padding:12px;margin:10px 0;border:1px solid #ddd;border-radius:8px;} button{width:100%;padding:12px;background:#0d6efd;color:#fff;border:none;border-radius:8px;cursor:pointer;} h2{margin-bottom:20px;text-align:center;} .error{color:#b00020;margin:10px 0;}</style>';
        echo '</head><body>';
        echo '<div class="box"><h2>تسجيل دخول المدير</h2>';
        if (!empty($_SESSION['flash']['message'])) {
            echo '<div class="error">' . htmlspecialchars($_SESSION['flash']['message']) . '</div>';
            unset($_SESSION['flash']);
        }
        echo '<form method="post"><input type="email" name="email" placeholder="البريد الإلكتروني" required><input type="password" name="password" placeholder="كلمة المرور" required><button type="submit">دخول</button></form>';
        echo '</div></body></html>';
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'تم تسجيل الخروج بنجاح'];
        $this->redirect('/admin/login');
    }
}

