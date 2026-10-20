<?php
class AuthController extends Controller
{
    use UploadTrait;

    public function login()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $redirect = (($user['type'] ?? 'job_seeker') === 'employer') ? '/employer/dashboard' : '/';
            $this->redirect($redirect);
        }

        if ($this->isPost()) {
            $email = trim((string) $this->input('email', ''));
            $password = (string) $this->input('password', '');

            if ($email === '' || $password === '') {
                $this->setFlash('error', 'يرجى إدخال البريد الإلكتروني وكلمة المرور');
                $this->redirect('/login');
            }

            try {
                $pdo = $this->db();
                $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user || !password_verify($password, $user['password'])) {
                    $this->setFlash('error', 'بيانات الدخول غير صحيحة');
                    $this->redirect('/login');
                }

                if (($user['status'] ?? 'inactive') !== 'active') {
                    $this->setFlash('error', 'حسابك غير نشط حالياً');
                    $this->redirect('/login');
                }

                Auth::login($user);
                $this->setFlash('success', 'تم تسجيل الدخول بنجاح');
                $redirect = (($user['type'] ?? 'job_seeker') === 'employer') ? '/employer/dashboard' : '/';
                $this->redirect($redirect);
            } catch (Throwable $e) {
                $this->setFlash('error', 'حدث خطأ أثناء تسجيل الدخول');
                $this->redirect('/login');
            }
        }

        $this->view('auth.login', [
            'title' => 'تسجيل الدخول',
        ]);
    }

    public function register()
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        if ($this->isPost()) {
            $firstName = trim((string) $this->input('first_name', ''));
            $lastName = trim((string) $this->input('last_name', ''));
            $email = trim((string) $this->input('email', ''));
            $phone = trim((string) $this->input('phone', ''));
            $password = (string) $this->input('password', '');
            $type = (string) $this->input('type', 'job_seeker');

            if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
                $this->setFlash('error', 'يرجى تعبئة جميع الحقول المطلوبة');
                $this->redirect('/register');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('error', 'يرجى إدخال بريد إلكتروني صحيح');
                $this->redirect('/register');
            }

            try {
                $pdo = $this->db();
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $this->setFlash('error', 'هذا البريد الإلكتروني مسجل بالفعل');
                    $this->redirect('/register');
                }

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $query = 'INSERT INTO users (first_name, last_name, email, phone, password, type, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())';
                $pdo->prepare($query)->execute([
                    $firstName,
                    $lastName,
                    $email,
                    $phone,
                    $hashedPassword,
                    $type,
                    'active',
                ]);

                $this->setFlash('success', 'تم إنشاء الحساب بنجاح');
                $this->redirect('/login');
            } catch (Throwable $e) {
                $this->setFlash('error', 'حدث خطأ أثناء إنشاء الحساب');
                $this->redirect('/register');
            }
        }

        $this->view('auth.register', [
            'title' => 'إنشاء حساب',
        ]);
    }

    public function employerRegister()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $redirect = (($user['type'] ?? 'job_seeker') === 'employer') ? '/employer/dashboard' : '/';
            $this->redirect($redirect);
        }

        if ($this->isPost()) {
            $companyName = trim((string) $this->input('company_name', ''));
            $managerName = trim((string) $this->input('manager_name', ''));
            $email = trim((string) $this->input('email', ''));
            $phone = trim((string) $this->input('phone', ''));
            $password = (string) $this->input('password', '');
            $website = trim((string) $this->input('website', ''));
            $industry = trim((string) $this->input('industry', ''));
            $description = trim((string) $this->input('description', ''));
            $logoPath = null;

            if (isset($_FILES['logo']) && !empty($_FILES['logo']['name'])) {
                $upload = $this->handleFileUpload($_FILES['logo'], 'companies', ['jpg', 'jpeg', 'png', 'gif', 'webp'], (int) config('max_upload_size', 5242880), 'company_logo');
                if (!$upload['success']) {
                    $this->setFlash('error', $upload['message']);
                    $this->redirect('/employer/register');
                }
                $logoPath = $upload['path'];
            }

            if ($companyName === '' || $managerName === '' || $email === '' || $phone === '' || $password === '') {
                $this->setFlash('error', 'يرجى تعبئة جميع بيانات الشركة المطلوبة');
                $this->redirect('/employer/register');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('error', 'يرجى إدخال بريد إلكتروني صحيح');
                $this->redirect('/employer/register');
            }

            try {
                $pdo = $this->db();
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $this->setFlash('error', 'هذا البريد الإلكتروني مسجل بالفعل');
                    $this->redirect('/employer/register');
                }

                $companyCheck = $pdo->prepare('SELECT id FROM companies WHERE name = ? LIMIT 1');
                $companyCheck->execute([$companyName]);
                if ($companyCheck->fetch()) {
                    $this->setFlash('error', 'اسم الشركة مسجل بالفعل في النظام');
                    $this->redirect('/employer/register');
                }

                $names = preg_split('/\s+/', trim((string) $managerName), 2);
                $firstName = trim((string) ($names[0] ?? ''));
                $lastName = trim((string) ($names[1] ?? ''));

                if ($firstName === '') {
                    $firstName = 'مسؤول';
                }

                if ($lastName === '') {
                    $lastName = 'غير محدد';
                }

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $userStmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, phone, password, type, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
                $userStmt->execute([
                    $firstName,
                    $lastName,
                    $email,
                    $phone,
                    $hashedPassword,
                    'employer',
                    'active',
                ]);

                $userId = (int) $pdo->lastInsertId();
                $baseSlug = $this->slugify($companyName) ?: 'company';
                $slug = $baseSlug;
                $counter = 1;

                while (true) {
                    $slugCheck = $pdo->prepare('SELECT id FROM companies WHERE slug = ? LIMIT 1');
                    $slugCheck->execute([$slug]);
                    if (!$slugCheck->fetch()) {
                        break;
                    }
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $companyStmt = $pdo->prepare('INSERT INTO companies (user_id, name, slug, email, phone, website, description, logo, industry, governorate_id, city_id, verification_status, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, "pending", "active", NOW())');
                $companyStmt->execute([
                    $userId,
                    $companyName,
                    $slug,
                    $email,
                    $phone,
                    $website !== '' ? $website : null,
                    $description !== '' ? $description : null,
                    $logoPath,
                    $industry !== '' ? $industry : null,
                ]);

                $this->setFlash('success', 'تم إنشاء حساب صاحب العمل بنجاح');
                $this->redirect('/login');
            } catch (Throwable $e) {
                $this->setFlash('error', 'حدث خطأ أثناء إنشاء حساب صاحب العمل. تأكد من صحة البيانات ولاحظ أن اسم المسؤول يجب أن يكون اسمًا صحيحًا.');
                $this->redirect('/employer/register');
            }
        }

        $this->view('auth.employer-register', [
            'title' => 'تسجيل صاحب العمل',
        ]);
    }

    public function forgotPassword()
    {
        if ($this->isPost()) {
            $email = trim((string) $this->input('email', ''));
            if ($email === '') {
                $this->setFlash('error', 'يرجى إدخال البريد الإلكتروني');
                $this->redirect('/forgot-password');
            }

            $this->setFlash('success', 'تم إرسال رابط استعادة كلمة المرور إذا كان الحساب موجوداً');
            $this->redirect('/forgot-password');
        }

        $this->view('auth.forgot-password', [
            'title' => 'استعادة كلمة المرور',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        $this->setFlash('success', 'تم تسجيل الخروج بنجاح');
        $this->redirect('/');
    }
}

