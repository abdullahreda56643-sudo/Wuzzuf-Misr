<?php
class ProfileController extends Controller
{
    public function index()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        $userId = (int) ($user['id'] ?? 0);
        $freshUser = $this->db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $freshUser->execute([$userId]);
        $user = $freshUser->fetch(PDO::FETCH_ASSOC) ?: $user;

        $resumeModel = new Resume();
        $resumes = $resumeModel->byUser($userId);
        $defaultResume = $resumeModel->defaultResume($userId);
        $governorates = $this->db()->query("SELECT id, name FROM governorates WHERE status = 'active' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $cities = $this->db()->query("SELECT id, name, governorate_id FROM cities WHERE status = 'active' ORDER BY governorate_id, name")->fetchAll(PDO::FETCH_ASSOC);

        $skills = $user['skills'] ?? '[]';
        if (is_string($skills)) {
            $decodedSkills = json_decode($skills, true);
            $skills = is_array($decodedSkills) ? $decodedSkills : [];
        } elseif (!is_array($skills)) {
            $skills = [];
        }

        $this->view('profile.index', [
            'title' => 'الملف الشخصي',
            'user' => $user,
            'skills' => $skills,
            'resumes' => $resumes,
            'defaultResume' => $defaultResume,
            'governorates' => $governorates,
            'cities' => $cities,
        ]);
    }

    public function previewResume($resumeId): void
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        $id = (int) $resumeId;
        if ($id <= 0) {
            $this->setFlash('error', 'ملف السيرة الذاتية غير موجود');
            $this->redirect('/profile');
        }

        $stmt = $this->db()->prepare('SELECT * FROM resumes WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$id, (int) ($user['id'] ?? 0)]);
        $resume = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resume) {
            $this->setFlash('error', 'ملف السيرة الذاتية غير موجود أو لا يخص هذا الحساب');
            $this->redirect('/profile');
        }

        $filePath = trim((string) ($resume['file_path'] ?? ''));
        $absolutePath = $filePath !== '' ? ROOT_PATH . '/public' . $filePath : '';

        if ($filePath === '' || !file_exists($absolutePath)) {
            $this->setFlash('error', 'الملف غير موجود في الخادم');
            $this->redirect('/profile');
        }

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $fileUrl = asset($filePath);
        $previewUrl = in_array($extension, ['pdf'], true)
            ? $fileUrl
            : '';

        $this->view('profile.preview', [
            'title' => 'معاينة السيرة الذاتية',
            'resume' => $resume,
            'previewUrl' => $previewUrl,
            'fileUrl' => $fileUrl,
            'fileName' => $resume['file_name'] ?? 'CV',
            'isPdf' => $extension === 'pdf',
            'fileExtension' => $extension,
        ]);
    }

    public function savedJobs()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        $userId = (int) ($user['id'] ?? 0);
        $savedJobs = [];

        try {
            $stmt = $this->db()->prepare(
                'SELECT sj.id, sj.saved_at, j.id AS job_id, j.title, j.job_type, j.experience_level, j.salary_min, j.salary_max, j.salary_currency, c.name AS company_name
                 FROM saved_jobs sj
                 LEFT JOIN jobs j ON j.id = sj.job_id
                 LEFT JOIN companies c ON c.id = j.company_id
                 WHERE sj.user_id = ?
                 ORDER BY sj.saved_at DESC'
            );
            $stmt->execute([$userId]);
            $savedJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $savedJobs = [];
        }

        $this->view('profile.saved-jobs', [
            'title' => 'الوظائف المحفوظة',
            'user' => $user,
            'savedJobs' => $savedJobs,
        ]);
    }

    public function applications()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        $userId = (int) ($user['id'] ?? 0);
        $applications = [];

        try {
            $stmt = $this->db()->prepare(
                'SELECT ja.id, ja.status, ja.applied_at, j.id AS job_id, j.title, j.job_type, j.experience_level, j.salary_min, j.salary_max, j.salary_currency, c.name AS company_name
                 FROM job_applications ja
                 LEFT JOIN jobs j ON j.id = ja.job_id
                 LEFT JOIN companies c ON c.id = j.company_id
                 WHERE ja.user_id = ?
                 ORDER BY ja.applied_at DESC'
            );
            $stmt->execute([$userId]);
            $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $applications = [];
        }

        $this->view('profile.applications', [
            'title' => 'الوظائف التي تقدم إليها',
            'user' => $user,
            'applications' => $applications,
        ]);
    }

    public function notifications()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        $userId = (int) ($user['id'] ?? 0);
        $notifications = [];

        try {
            $stmt = $this->db()->prepare(
                'UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0'
            );
            $stmt->execute([$userId]);

            $stmt = $this->db()->prepare(
                'SELECT id, title, message, type, is_read, action_url, created_at
                 FROM notifications
                 WHERE user_id = ?
                 ORDER BY created_at DESC'
            );
            $stmt->execute([$userId]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $notifications = [];
        }

        $this->view('profile.notifications', [
            'title' => 'الإشعارات',
            'user' => $user,
            'notifications' => $notifications,
        ]);
    }

    public function update()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if (!$this->isPost()) {
            $this->redirect('/profile');
        }

        try {
            $userId = (int) ($user['id'] ?? 0);
            $firstName = trim((string) $this->input('first_name', ''));
            $lastName = trim((string) $this->input('last_name', ''));
            $phone = trim((string) $this->input('phone', ''));
            $bio = trim((string) $this->input('bio', ''));
            $qualification = trim((string) $this->input('qualification', ''));
            $experience = trim((string) $this->input('experience', ''));
            $governorateId = max(0, (int) $this->input('governorate_id', 0));
            $cityId = max(0, (int) $this->input('city_id', 0));
            if ($cityId > 0) {
                $cityStmt = $this->db()->prepare('SELECT id FROM cities WHERE id = ? AND governorate_id = ? AND status = "active" LIMIT 1');
                $cityStmt->execute([$cityId, $governorateId]);
                if (!$cityStmt->fetchColumn()) {
                    $cityId = 0;
                }
            }
            $skillsInput = $this->input('skills', '');
            $skills = [];

            if (is_array($skillsInput)) {
                $skills = $skillsInput;
            } elseif (is_string($skillsInput) && trim($skillsInput) !== '') {
                $decodedSkills = json_decode($skillsInput, true);
                $skills = is_array($decodedSkills) ? $decodedSkills : (preg_split('/[,،\n]+/', $skillsInput) ?: []);
            }

            $skills = array_values(array_filter(array_map(static function ($skill): string {
                return trim((string) $skill);
            }, $skills), static fn(string $skill): bool => $skill !== ''));

            $skillsJson = json_encode($skills, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($skillsJson === false) {
                throw new RuntimeException('تعذر تجهيز المهارات للحفظ');
            }

            $stmt = $this->db()->prepare(
                'UPDATE users SET first_name = ?, last_name = ?, phone = ?, bio = ?, qualification = ?, experience = ?, skills = ?, governorate_id = ?, city_id = ?, updated_at = NOW() WHERE id = ?'
            );
            $stmt->execute([$firstName, $lastName, $phone, $bio, $qualification, $experience, $skillsJson, $governorateId ?: null, $cityId ?: null, $userId]);

            if (isset($_FILES['cv']) && !empty($_FILES['cv']['name'])) {
                $upload = $this->handleCvUpload($userId, $_FILES['cv']);
                if (!$upload['success']) {
                    $this->setFlash('error', $upload['message']);
                    $this->redirect('/profile');
                }
            }

            $updatedUser = $this->db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
            $updatedUser->execute([$userId]);
            $freshUser = $updatedUser->fetch(PDO::FETCH_ASSOC);
            if ($freshUser) {
                $_SESSION['user'] = $freshUser;
            } else {
                $_SESSION['user']['first_name'] = $firstName;
                $_SESSION['user']['last_name'] = $lastName;
                $_SESSION['user']['phone'] = $phone;
                $_SESSION['user']['bio'] = $bio;
                $_SESSION['user']['qualification'] = $qualification;
                $_SESSION['user']['experience'] = $experience;
                $_SESSION['user']['skills'] = $skillsJson ?: '[]';
            }

            $this->setFlash('success', 'تم تحديث ملفك الشخصي بنجاح');
        } catch (Throwable $e) {
            error_log('Profile update failed: ' . $e->getMessage());
            $this->setFlash('error', DEBUG_MODE ? 'تعذر حفظ البيانات: ' . $e->getMessage() : 'حدث خطأ أثناء تحديث الملف الشخصي');
        }

        $this->redirect('/profile');
    }

    protected function handleCvUpload(int $userId, array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'حدثت مشكلة في رفع ملف السيرة الذاتية'];
        }

        $allowed = ['pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed, true)) {
            return ['success' => false, 'message' => 'نوع الملف غير مسموح. استخدم PDF أو Word فقط'];
        }

        $maxSize = (int) (config('max_upload_size', 5242880));
        if (($file['size'] ?? 0) > $maxSize) {
            return ['success' => false, 'message' => 'حجم ملف السيرة الذاتية كبير جداً'];
        }

        $uploadDirs = config('upload_directories', []);
        $directory = $uploadDirs['resumes'] ?? ROOT_PATH . '/public/assets/uploads/resumes';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = 'cv_' . $userId . '_' . time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($file['name']));
        $destination = $directory . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'تعذر حفظ ملف السيرة الذاتية'];
        }

        $relativePath = '/assets/uploads/resumes/' . $fileName;
        $resumeModel = new Resume();

        $this->db()->prepare('UPDATE resumes SET is_default = 0 WHERE user_id = ?')->execute([$userId]);

        $resumeModel->create([
            'user_id' => $userId,
            'title' => 'السيرة الذاتية',
            'file_path' => $relativePath,
            'file_name' => $fileName,
            'file_size' => (int) $file['size'],
            'is_default' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'تم رفع السيرة الذاتية بنجاح'];
    }
}

