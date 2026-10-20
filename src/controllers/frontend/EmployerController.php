<?php
class EmployerController extends Controller
{
    use UploadTrait;

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if (($user['type'] ?? '') !== 'employer') {
            $this->setFlash('error', 'هذه الصفحة خاصة بصاحب العمل');
            $this->redirect('/');
        }

        $company = $this->findCompanyByUserId((int) ($user['id'] ?? 0));
        $companyName = trim((string) (($user['company_name'] ?? '') ?: (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'شركتي'));
        $companyLocation = 'غير محدد';
        $companyIndustry = 'تكنولوجيا';
        $companyStatus = 'نشط';
        $jobCount = 0;
        $activeJobs = 0;
        $expiredJobs = 0;
        $totalViews = 0;
        $applications = 0;
        $pendingReview = 0;
        $recentJobs = [];
        $recentApplications = [];

        if ($company) {
            try {
                $companyStmt = $this->db()->prepare(
                    'SELECT c.*, g.name AS governorate_name, ci.name AS city_name
                     FROM companies c
                     LEFT JOIN governorates g ON g.id = c.governorate_id
                     LEFT JOIN cities ci ON ci.id = c.city_id
                     WHERE c.id = ? LIMIT 1'
                );
                $companyStmt->execute([(int) $company['id']]);
                $companyData = $companyStmt->fetch(PDO::FETCH_ASSOC);

                if ($companyData) {
                    $companyName = trim((string) ($companyData['name'] ?? $companyName));
                    $companyLocation = trim((string) (($companyData['city_name'] ?? '') ?: ($companyData['governorate_name'] ?? 'غير محدد')));
                    $companyIndustry = trim((string) ($companyData['industry'] ?? $companyIndustry));
                    $companyStatus = (($companyData['status'] ?? 'active') === 'active') ? 'نشط' : 'غير نشط';
                }

                $statsStmt = $this->db()->prepare(
                    'SELECT COUNT(*) AS job_count,
                            SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) AS active_jobs,
                            SUM(CASE WHEN status = "expired" THEN 1 ELSE 0 END) AS expired_jobs,
                            SUM(views_count) AS total_views
                     FROM jobs
                     WHERE company_id = ?'
                );
                $statsStmt->execute([(int) $company['id']]);
                $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

                $jobCount = (int) ($stats['job_count'] ?? 0);
                $activeJobs = (int) ($stats['active_jobs'] ?? 0);
                $expiredJobs = (int) ($stats['expired_jobs'] ?? 0);
                $totalViews = (int) ($stats['total_views'] ?? 0);

                $applicationsStmt = $this->db()->prepare(
                    'SELECT COUNT(*) AS total_apps
                     FROM job_applications ja
                     INNER JOIN jobs j ON j.id = ja.job_id
                     WHERE j.company_id = ?'
                );
                $applicationsStmt->execute([(int) $company['id']]);
                $applicationsData = $applicationsStmt->fetch(PDO::FETCH_ASSOC);
                $applications = (int) ($applicationsData['total_apps'] ?? 0);

                $pendingStmt = $this->db()->prepare(
                    'SELECT COUNT(*) AS pending_review
                     FROM job_applications ja
                     INNER JOIN jobs j ON j.id = ja.job_id
                     WHERE j.company_id = ? AND ja.status = "pending"'
                );
                $pendingStmt->execute([(int) $company['id']]);
                $pendingData = $pendingStmt->fetch(PDO::FETCH_ASSOC);
                $pendingReview = (int) ($pendingData['pending_review'] ?? 0);

                $jobRowsStmt = $this->db()->prepare(
                    'SELECT j.id, j.title, j.status, j.applications_count, j.views_count, j.job_type, j.expires_at
                     FROM jobs j
                     WHERE j.company_id = ?
                     ORDER BY j.created_at DESC
                     LIMIT 4'
                );
                $jobRowsStmt->execute([(int) $company['id']]);
                $jobRows = $jobRowsStmt->fetchAll(PDO::FETCH_ASSOC);

                $statusMap = [
                    'draft' => 'مسودة',
                    'published' => 'نشط',
                    'expired' => 'منتهي',
                    'closed' => 'مغلق',
                ];
                $typeMap = [
                    'full-time' => 'دوام كامل',
                    'part-time' => 'دوام جزئي',
                    'contract' => 'عقد',
                    'temporary' => 'مؤقت',
                    'internship' => 'تدريب',
                ];

                foreach ($jobRows as $job) {
                    $recentJobs[] = [
                        'id' => (int) ($job['id'] ?? 0),
                        'title' => (string) ($job['title'] ?? 'وظيفة'),
                        'status' => $statusMap[$job['status'] ?? 'draft'] ?? 'مسودة',
                        'applications' => (int) ($job['applications_count'] ?? 0),
                        'views' => (int) ($job['views_count'] ?? 0),
                        'type' => $typeMap[$job['job_type'] ?? 'full-time'] ?? 'دوام كامل',
                        'renew' => !empty($job['expires_at']) && strtotime($job['expires_at']) < time(),
                    ];
                }

                $appStmt = $this->db()->prepare(
                    'SELECT ja.status, j.title AS job_title, u.first_name, u.last_name
                     FROM job_applications ja
                     INNER JOIN jobs j ON j.id = ja.job_id
                     INNER JOIN users u ON u.id = ja.user_id
                     WHERE j.company_id = ?
                     ORDER BY ja.applied_at DESC
                     LIMIT 5'
                );
                $appStmt->execute([(int) $company['id']]);
                $appRows = $appStmt->fetchAll(PDO::FETCH_ASSOC);

                $appStatusMap = [
                    'pending' => 'قيد المراجعة',
                    'reviewed' => 'تمت المراجعة',
                    'shortlisted' => 'تمت المقابلة',
                    'rejected' => 'مرفوض',
                    'accepted' => 'تمت الموافقة',
                    'withdrawn' => 'ملغي',
                ];

                foreach ($appRows as $row) {
                    $recentApplications[] = [
                        'name' => trim((string) (($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))),
                        'job' => (string) ($row['job_title'] ?? 'وظيفة'),
                        'status' => $appStatusMap[$row['status'] ?? 'pending'] ?? 'قيد المراجعة',
                    ];
                }
            } catch (Throwable $e) {
                $recentJobs = [];
                $recentApplications = [];
            }
        }

        $this->view('employer.dashboard', [
            'title' => 'لوحة صاحب العمل',
            'user' => $user,
            'companyName' => $companyName,
            'companyLocation' => $companyLocation,
            'companyIndustry' => $companyIndustry,
            'companyStatus' => $companyStatus,
            'jobCount' => $jobCount,
            'activeJobs' => $activeJobs,
            'expiredJobs' => $expiredJobs,
            'totalViews' => $totalViews,
            'applications' => $applications,
            'pendingReview' => $pendingReview,
            'renewalNeeded' => max(0, $expiredJobs),
            'recentJobs' => $recentJobs ?: [
                ['title' => 'لا توجد وظائف بعد', 'status' => 'مسودة', 'applications' => 0, 'views' => 0, 'type' => 'دوام كامل', 'renew' => false],
            ],
            'recentApplications' => $recentApplications,
        ]);
    }

    public function jobs()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if (($user['type'] ?? '') !== 'employer') {
            $this->setFlash('error', 'هذه الصفحة خاصة بصاحب العمل');
            $this->redirect('/');
        }

        $company = $this->findCompanyByUserId((int) ($user['id'] ?? 0));
        $jobs = [];

        if ($company) {
            $stmt = $this->db()->prepare(
                'SELECT j.*, c.name AS city_name, g.name AS governorate_name
                 FROM jobs j
                 LEFT JOIN cities c ON c.id = j.city_id
                 LEFT JOIN governorates g ON g.id = j.governorate_id
                 WHERE j.company_id = ?
                 ORDER BY j.created_at DESC'
            );
            $stmt->execute([(int) $company['id']]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $statusMap = [
                'draft' => 'مسودة',
                'published' => 'نشط',
                'expired' => 'منتهي',
                'closed' => 'مغلق',
            ];
            $typeMap = [
                'full-time' => 'دوام كامل',
                'part-time' => 'دوام جزئي',
                'contract' => 'عقد',
                'temporary' => 'مؤقت',
                'internship' => 'تدريب',
            ];

            foreach ($rows as $job) {
                $jobs[] = [
                    'id' => (int) ($job['id'] ?? 0),
                    'title' => (string) ($job['title'] ?? 'بدون عنوان'),
                    'location' => trim((string) ($job['city_name'] ?? $job['governorate_name'] ?? 'غير محدد')),
                    'type' => $typeMap[$job['job_type'] ?? 'full-time'] ?? (string) ($job['job_type'] ?? 'دوام كامل'),
                    'status' => $statusMap[$job['status'] ?? 'draft'] ?? (string) ($job['status'] ?? 'مسودة'),
                    'applications' => (int) ($job['applications_count'] ?? 0),
                    'published' => !empty($job['published_at']) ? date('Y-m-d', strtotime($job['published_at'])) : 'غير محدد',
                ];
            }
        }

        $this->view('employer.jobs', [
            'title' => 'إدارة الوظائف',
            'user' => $user,
            'jobs' => $jobs,
        ]);
    }

    public function renewJob($id = null)
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if (($user['type'] ?? '') !== 'employer') {
            $this->setFlash('error', 'هذه الصفحة خاصة بصاحب العمل');
            $this->redirect('/');
        }

        $jobId = (int) $id;
        $company = $this->findCompanyByUserId((int) ($user['id'] ?? 0));

        if ($jobId <= 0 || !$company) {
            $this->setFlash('error', 'الوظيفة المطلوبة غير موجودة');
            $this->redirect('/employer/dashboard');
        }

        try {
            $jobStmt = $this->db()->prepare('SELECT * FROM jobs WHERE id = ? AND company_id = ? LIMIT 1');
            $jobStmt->execute([$jobId, (int) $company['id']]);
            $job = $jobStmt->fetch(PDO::FETCH_ASSOC);

            if (!$job) {
                $this->setFlash('error', 'لا توجد وظيفة مرتبطة بهذا الحساب');
                $this->redirect('/employer/dashboard');
            }

            $status = (string) ($job['status'] ?? 'draft');
            if ($status !== 'expired') {
                $this->setFlash('error', 'يمكن تجديد الإعلان فقط عندما تكون الوظيفة منتهية');
                $this->redirect('/employer/dashboard');
            }

            $advertDays = 30;
            if (!empty($job['published_at']) && !empty($job['expires_at'])) {
                $publishedAt = strtotime((string) $job['published_at']);
                $expiresAt = strtotime((string) $job['expires_at']);
                if ($publishedAt && $expiresAt && $expiresAt > $publishedAt) {
                    $diff = (int) floor(($expiresAt - $publishedAt) / 86400);
                    if ($diff > 0) {
                        $advertDays = $diff;
                    }
                }
            }

            $renewStmt = $this->db()->prepare(
                'UPDATE jobs
                 SET status = "draft",
                     published_at = NULL,
                     expires_at = NULL,
                     updated_at = NOW()
                 WHERE id = ? AND company_id = ? AND status = "expired"'
            );
            $renewStmt->execute([$jobId, (int) $company['id']]);

            $this->setFlash('success', 'تم إرسال الوظيفة للمراجعة، وستظهر بعد موافقة الإدارة');
            $this->redirect('/employer/dashboard');
        } catch (Throwable $e) {
            $this->setFlash('error', 'حدث خطأ أثناء تجديد الإعلان');
            $this->redirect('/employer/dashboard');
        }
    }

    public function applications()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if (($user['type'] ?? '') !== 'employer') {
            $this->setFlash('error', 'هذه الصفحة خاصة بصاحب العمل');
            $this->redirect('/');
        }

        $company = $this->findCompanyByUserId((int) ($user['id'] ?? 0));
        $applications = [];

        if ($company) {
            $stmt = $this->db()->prepare(
                'SELECT ja.id, ja.user_id, ja.job_id, ja.status, ja.applied_at, j.title AS job_title, u.first_name, u.last_name, u.phone, u.email,
                        r.id AS resume_id, r.file_path, r.file_name
                 FROM job_applications ja
                 INNER JOIN jobs j ON j.id = ja.job_id
                 INNER JOIN users u ON u.id = ja.user_id
                 LEFT JOIN resumes r ON r.user_id = ja.user_id AND r.is_default = 1
                 WHERE j.company_id = ?
                 ORDER BY ja.applied_at DESC'
            );
            $stmt->execute([(int) $company['id']]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $statusMap = [
                'pending' => 'قيد المراجعة',
                'reviewed' => 'تمت المراجعة',
                'shortlisted' => 'تمت المقابلة',
                'rejected' => 'مرفوض',
                'accepted' => 'تمت الموافقة',
                'withdrawn' => 'ملغي',
            ];

            foreach ($rows as $row) {
                $filePath = (string) ($row['file_path'] ?? '');
                $fileUrl = $filePath !== '' ? asset($filePath) : '';
                $applications[] = [
                    'id' => (int) ($row['id'] ?? 0),
                    'user_id' => (int) ($row['user_id'] ?? 0),
                    'job_id' => (int) ($row['job_id'] ?? 0),
                    'name' => trim((string) (($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))),
                    'job' => (string) ($row['job_title'] ?? 'وظيفة'),
                    'status' => $statusMap[$row['status'] ?? 'pending'] ?? 'قيد المراجعة',
                    'status_raw' => (string) ($row['status'] ?? 'pending'),
                    'date' => !empty($row['applied_at']) ? date('Y-m-d', strtotime($row['applied_at'])) : '',
                    'resume_id' => (int) ($row['resume_id'] ?? 0),
                    'file_path' => $fileUrl,
                    'file_name' => (string) ($row['file_name'] ?? 'CV'),
                    'phone' => (string) ($row['phone'] ?? ''),
                    'email' => (string) ($row['email'] ?? ''),
                ];
            }
        }

        $this->view('employer.applications', [
            'title' => 'المتقدمون للوظائف',
            'user' => $user,
            'applications' => $applications,
        ]);
    }

    public function updateApplicationStatus()
    {
        $user = $this->currentUser();
        if (!$user || ($user['type'] ?? '') !== 'employer') {
            http_response_code(403);
            exit(json_encode(['error' => 'غير مصرح']));
        }

        $appId = (int) ($_POST['application_id'] ?? 0);
        $status = trim((string) ($_POST['status'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));

        if ($appId <= 0 || !in_array($status, ['accepted', 'rejected', 'reviewed', 'shortlisted'], true)) {
            http_response_code(400);
            exit(json_encode(['error' => 'بيانات غير صحيحة']));
        }

        try {
            $pdo = $this->db();
            $company = $this->findCompanyByUserId((int) ($user['id'] ?? 0));

            $stmt = $pdo->prepare(
                'SELECT ja.user_id, j.title
                 FROM job_applications ja
                 INNER JOIN jobs j ON j.id = ja.job_id
                 WHERE ja.id = ? AND j.company_id = ?'
            );
            $stmt->execute([$appId, (int) ($company['id'] ?? 0)]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$app) {
                http_response_code(404);
                exit(json_encode(['error' => 'الطلب غير موجود']));
            }

            $updateStmt = $pdo->prepare('UPDATE job_applications SET status = ? WHERE id = ?');
            $updateStmt->execute([$status, $appId]);

            $statusMessages = [
                'accepted' => 'تم قبول طلب التقديم على وظيفة ' . ($app['title'] ?? 'الوظيفة'),
                'rejected' => 'تم رفض طلب التقديم على وظيفة ' . ($app['title'] ?? 'الوظيفة'),
                'reviewed' => 'تمت مراجعة طلب التقديم على وظيفة ' . ($app['title'] ?? 'الوظيفة'),
                'shortlisted' => 'تمت إضافتك للمتقدمين النهائيين لوظيفة ' . ($app['title'] ?? 'الوظيفة'),
            ];

            $notificationMsg = $statusMessages[$status] ?? 'تم تحديث حالة طلبك';
            if ($message) {
                $notificationMsg .= '\n' . $message;
            }

            $notifStmt = $pdo->prepare(
                'INSERT INTO notifications (user_id, type, title, message, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())'
            );
            $notifStmt->execute([
                (int) ($app['user_id'] ?? 0),
                'application_' . $status,
                $status === 'accepted' ? 'تمت الموافقة على طلبك' : ($status === 'rejected' ? 'تم رفض طلبك' : 'تحديث حالة الطلب'),
                $notificationMsg,
            ]);

            echo json_encode(['success' => true, 'message' => 'تم التحديث بنجاح']);
        } catch (Throwable $e) {
            http_response_code(500);
            exit(json_encode(['error' => 'خطأ في السيرفر']));
        }
    }

    public function settings()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if (($user['type'] ?? '') !== 'employer') {
            $this->setFlash('error', 'هذه الصفحة خاصة بصاحب العمل');
            $this->redirect('/');
        }

        $company = $this->findCompanyByUserId((int) ($user['id'] ?? 0));

        if ($this->isPost()) {
            $companyName = trim((string) $this->input('company_name', ''));
            $email = trim((string) $this->input('email', ''));
            $phone = trim((string) $this->input('phone', ''));
            $website = trim((string) $this->input('website', ''));
            $industry = trim((string) $this->input('industry', ''));
            $description = trim((string) $this->input('description', ''));
            $governorateId = (int) $this->input('governorate_id', 0);
            $cityId = (int) $this->input('city_id', 0);
            $logoPath = $company['logo'] ?? null;

            if ($companyName === '' || $email === '' || $phone === '' || $governorateId <= 0 || $cityId <= 0) {
                $this->setFlash('error', 'يرجى تعبئة بيانات الشركة والموقع');
                $this->redirect('/employer/settings');
            }

            if (isset($_FILES['logo']) && !empty($_FILES['logo']['name'])) {
                $upload = $this->handleFileUpload($_FILES['logo'], 'companies', ['jpg', 'jpeg', 'png', 'gif', 'webp'], (int) config('max_upload_size', 5242880), 'company_logo');
                if (!$upload['success']) {
                    $this->setFlash('error', $upload['message']);
                    $this->redirect('/employer/settings');
                }
                $logoPath = $upload['path'];
            }

            try {
                $pdo = $this->db();
                $stmt = $pdo->prepare(
                    'UPDATE companies SET name = ?, email = ?, phone = ?, website = ?, description = ?, logo = ?, industry = ?, governorate_id = ?, city_id = ?, updated_at = NOW() WHERE user_id = ?'
                );
                $stmt->execute([
                    $companyName,
                    $email,
                    $phone,
                    $website !== '' ? $website : null,
                    $description !== '' ? $description : null,
                    $logoPath,
                    $industry !== '' ? $industry : null,
                    $governorateId,
                    $cityId,
                    (int) $user['id'],
                ]);

                $userStmt = $pdo->prepare('UPDATE users SET email = ?, phone = ?, updated_at = NOW() WHERE id = ?');
                $userStmt->execute([$email, $phone, (int) $user['id']]);

                $this->setFlash('success', 'تم حفظ إعدادات الشركة بنجاح');
                $this->redirect('/employer/settings');
            } catch (Throwable $e) {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ إعدادات الشركة');
                $this->redirect('/employer/settings');
            }
        }

        $governorates = [];
        $cities = [];
        try {
            $governorates = $this->db()->query("SELECT id, name FROM governorates WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
            $cities = $this->db()->query("SELECT id, name, governorate_id FROM cities WHERE status = 'active' ORDER BY governorate_id, name ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $governorates = [];
            $cities = [];
        }

        $this->view('employer.settings', [
            'title' => 'إعدادات الشركة',
            'user' => $user,
            'company' => $company,
            'governorates' => $governorates,
            'cities' => $cities,
        ]);
    }

    public function createJob()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if (($user['type'] ?? '') !== 'employer') {
            $this->setFlash('error', 'يُسمح فقط لصاحب العمل بإضافة وظائف');
            $this->redirect('/');
        }

        $existingCompany = $this->findCompanyByUserId((int) $user['id']);
        if ($existingCompany && ($existingCompany['verification_status'] ?? 'pending') !== 'verified') {
            $this->setFlash('error', 'لا يمكنك إضافة وظيفة قبل اعتماد توثيق الشركة من الإدارة');
            $this->redirect('/employer/dashboard');
        }

        if ($this->isPost()) {
            $title = trim((string) $this->input('title', ''));
            $categoryId = (int) $this->input('category_id', 0);
            $description = trim((string) $this->input('description', ''));
            $requirements = trim((string) $this->input('requirements', ''));
            $benefits = trim((string) $this->input('benefits', ''));
            $jobType = trim((string) $this->input('type', 'full-time'));
            $salaryMin = (float) $this->input('salary_min', 0);
            $salaryMax = (float) $this->input('salary_max', 0);
            $experienceLevel = trim((string) $this->input('experience_level', 'junior'));
            $qualification = trim((string) $this->input('qualification', ''));
            $applicationMethod = trim((string) $this->input('application_method', 'internal'));
            $applicationWhatsapp = trim((string) $this->input('application_whatsapp', ''));
            $applicationPhone = trim((string) $this->input('application_phone', ''));
            $applicationEmail = trim((string) $this->input('application_email', ''));
            $applicationLink = trim((string) $this->input('application_link', ''));
            $advertDays = (int) $this->input('advert_days', 30);
            $governorateId = (int) $this->input('governorate_id', 1);
            $cityId = (int) $this->input('city_id', 1);

            if ($title === '' || $categoryId <= 0 || $description === '') {
                $this->setFlash('error', 'يرجى تعبئة جميع الحقول المطلوبة');
                $this->redirect('/employer/jobs/create');
            }

            try {
                $pdo = $this->db();
                $company = $this->findCompanyByUserId((int) $user['id']);
                if (!$company) {
                    $companyName = trim((string) (($user['company_name'] ?? '') ?: (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'الشركة'));
                    $companyName = $companyName !== '' ? $companyName : 'الشركة';
                    $slug = $this->slugify($companyName);

                    $companyStmt = $pdo->prepare('INSERT INTO companies (user_id, name, slug, email, phone, governorate_id, city_id, verification_status, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, "pending", "active", NOW())');
                    $companyStmt->execute([
                        (int) $user['id'],
                        $companyName,
                        $slug !== '' ? $slug : 'company',
                        $user['email'] ?? '',
                        $user['phone'] ?? '',
                        1,
                        1,
                    ]);

                    $company = ['id' => (int) $pdo->lastInsertId()];
                }

                if (($company['verification_status'] ?? 'pending') !== 'verified') {
                    $this->setFlash('error', 'لا يمكنك إضافة وظيفة قبل اعتماد توثيق الشركة من الإدارة');
                    $this->redirect('/employer/dashboard');
                }

                $governorateId = $governorateId > 0 ? $governorateId : 1;
                $cityId = $cityId > 0 ? $cityId : 1;
                $advertDays = max(1, $advertDays);

                $applicationMethod = in_array($applicationMethod, ['internal', 'whatsapp', 'phone', 'email', 'external_link'], true) ? $applicationMethod : 'internal';
                $applicationWhatsapp = $applicationMethod === 'whatsapp' ? $this->normalizeEgyptianPhone($applicationWhatsapp) : null;
                $applicationPhone = $applicationMethod === 'phone' ? $this->normalizeEgyptianPhone($applicationPhone) : null;
                $applicationEmail = $applicationMethod === 'email' ? $applicationEmail : null;
                $applicationLink = $applicationMethod === 'external_link' ? $applicationLink : null;

                $stmt = $pdo->prepare(
                    'INSERT INTO jobs (company_id, title, slug, category_id, description, requirements, benefits, job_type, experience_level, salary_min, salary_max, salary_currency, qualification, governorate_id, city_id, application_method, application_whatsapp, application_phone, application_email, application_link, published_at, expires_at, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, "draft", NOW())'
                );
                $stmt->execute([
                    (int) $company['id'],
                    $title,
                    $this->slugify($title),
                    $categoryId,
                    $description,
                    $requirements !== '' ? $requirements : null,
                    $benefits !== '' ? $benefits : null,
                    in_array($jobType, ['full-time', 'part-time', 'contract', 'temporary', 'internship'], true) ? $jobType : 'full-time',
                    in_array($experienceLevel, ['entry', 'junior', 'mid', 'senior', 'lead', 'executive'], true) ? $experienceLevel : 'junior',
                    $salaryMin > 0 ? $salaryMin : null,
                    $salaryMax > 0 ? $salaryMax : null,
                    'EGP',
                    $qualification !== '' ? $qualification : null,
                    $governorateId,
                    $cityId,
                    $applicationMethod,
                    $applicationWhatsapp,
                    $applicationPhone,
                    $applicationEmail,
                    $applicationLink,
                ]);

                $this->setFlash('success', 'تم إرسال الوظيفة للمراجعة، وستظهر بعد موافقة الإدارة');
                $this->redirect('/employer/dashboard');
            } catch (Throwable $e) {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الوظيفة');
                $this->redirect('/employer/jobs/create');
            }
        }

        $categories = [];
        $governorates = [];
        $cities = [];
        $durations = [];

        try {
            $categoriesStmt = $this->db()->query("SELECT * FROM job_categories WHERE status = 'active' ORDER BY name ASC");
            $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $categories = [
                ['id' => 2, 'name' => 'المبيعات والتسويق'],
                ['id' => 3, 'name' => 'الموارد البشرية'],
            ];
        }

        try {
            $governoratesStmt = $this->db()->query("SELECT * FROM governorates WHERE status = 'active' ORDER BY name ASC");
            $governorates = $governoratesStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $governorates = [
                ['id' => 1, 'name' => 'القاهرة'],
                ['id' => 2, 'name' => 'الإسكندرية'],
            ];
        }

        try {
            $citiesStmt = $this->db()->query("SELECT * FROM cities WHERE status = 'active' ORDER BY governorate_id, name ASC");
            $cities = $citiesStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $cities = [
                ['id' => 1, 'name' => 'القاهرة'],
                ['id' => 2, 'name' => 'الإسكندرية'],
            ];
        }

        try {
            $durations = $this->db()->query('SELECT id, name, days FROM ad_durations WHERE is_active = 1 ORDER BY sort_order ASC, days ASC')->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $durations = [];
        }

        $this->view('employer.add-job', [
            'title' => 'إضافة وظيفة',
            'user' => $user,
            'categories' => $categories,
            'governorates' => $governorates,
            'cities' => $cities,
            'durations' => $durations,
        ]);
    }

    public function editJob($id = null)
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if (($user['type'] ?? '') !== 'employer') {
            $this->setFlash('error', 'هذه الصفحة خاصة بصاحب العمل');
            $this->redirect('/');
        }

        $jobId = (int) $id;
        $company = $this->findCompanyByUserId((int) ($user['id'] ?? 0));

        if ($jobId <= 0 || !$company) {
            $this->setFlash('error', 'الوظيفة المطلوبة غير موجودة');
            $this->redirect('/employer/jobs');
        }

        if ($this->isPost()) {
            $title = trim((string) $this->input('title', ''));
            $categoryId = (int) $this->input('category_id', 0);
            $description = trim((string) $this->input('description', ''));
            $requirements = trim((string) $this->input('requirements', ''));
            $benefits = trim((string) $this->input('benefits', ''));
            $jobType = trim((string) $this->input('type', 'full-time'));
            $salaryMin = (float) $this->input('salary_min', 0);
            $salaryMax = (float) $this->input('salary_max', 0);
            $experienceLevel = trim((string) $this->input('experience_level', 'junior'));
            $qualification = trim((string) $this->input('qualification', ''));
            $applicationMethod = trim((string) $this->input('application_method', 'internal'));
            $applicationWhatsapp = trim((string) $this->input('application_whatsapp', ''));
            $applicationPhone = trim((string) $this->input('application_phone', ''));
            $applicationEmail = trim((string) $this->input('application_email', ''));
            $applicationLink = trim((string) $this->input('application_link', ''));
            $advertDays = max(1, (int) $this->input('advert_days', 30));
            $governorateId = (int) $this->input('governorate_id', 1);
            $cityId = (int) $this->input('city_id', 1);

            if ($title === '' || $categoryId <= 0 || $description === '') {
                $this->setFlash('error', 'يرجى تعبئة جميع الحقول المطلوبة');
                $this->redirect('/employer/jobs/' . $jobId . '/edit');
            }

            try {
                $pdo = $this->db();
                $existingJob = $pdo->prepare('SELECT * FROM jobs WHERE id = ? AND company_id = ? LIMIT 1');
                $existingJob->execute([$jobId, (int) $company['id']]);
                $jobRow = $existingJob->fetch(PDO::FETCH_ASSOC);

                if (!$jobRow) {
                    $this->setFlash('error', 'لا يمكنك تعديل هذه الوظيفة');
                    $this->redirect('/employer/jobs');
                }

                $applicationMethod = in_array($applicationMethod, ['internal', 'whatsapp', 'phone', 'email', 'external_link'], true) ? $applicationMethod : 'internal';
                $applicationWhatsapp = $applicationMethod === 'whatsapp' ? $this->normalizeEgyptianPhone($applicationWhatsapp) : null;
                $applicationPhone = $applicationMethod === 'phone' ? $this->normalizeEgyptianPhone($applicationPhone) : null;
                $applicationEmail = $applicationMethod === 'email' ? $applicationEmail : null;
                $applicationLink = $applicationMethod === 'external_link' ? $applicationLink : null;

                $publishedAt = !empty($jobRow['published_at']) ? $jobRow['published_at'] : date('Y-m-d H:i:s');
                $expiresAt = date('Y-m-d H:i:s', strtotime('+' . $advertDays . ' days', strtotime($publishedAt)));

                $stmt = $pdo->prepare(
                    'UPDATE jobs SET title = ?, category_id = ?, description = ?, requirements = ?, benefits = ?, job_type = ?, experience_level = ?, salary_min = ?, salary_max = ?, qualification = ?, governorate_id = ?, city_id = ?, application_method = ?, application_whatsapp = ?, application_phone = ?, application_email = ?, application_link = ?, expires_at = ?, updated_at = NOW() WHERE id = ? AND company_id = ?'
                );

                $stmt->execute([
                    $title,
                    $categoryId,
                    $description,
                    $requirements !== '' ? $requirements : null,
                    $benefits !== '' ? $benefits : null,
                    in_array($jobType, ['full-time', 'part-time', 'contract', 'temporary', 'internship'], true) ? $jobType : 'full-time',
                    in_array($experienceLevel, ['entry', 'junior', 'mid', 'senior', 'lead', 'executive'], true) ? $experienceLevel : 'junior',
                    $salaryMin > 0 ? $salaryMin : null,
                    $salaryMax > 0 ? $salaryMax : null,
                    $qualification !== '' ? $qualification : null,
                    $governorateId,
                    $cityId,
                    $applicationMethod,
                    $applicationWhatsapp,
                    $applicationPhone,
                    $applicationEmail,
                    $applicationLink,
                    $expiresAt,
                    $jobId,
                    (int) $company['id'],
                ]);

                $this->setFlash('success', 'تم تحديث الوظيفة بنجاح');
                $this->redirect('/employer/jobs');
            } catch (Throwable $e) {
                $this->setFlash('error', 'حدث خطأ أثناء تحديث الوظيفة');
                $this->redirect('/employer/jobs/' . $jobId . '/edit');
            }
        }

        $categories = [];
        $governorates = [];
        $cities = [];
        $durations = [];

        try {
            $categoriesStmt = $this->db()->query("SELECT * FROM job_categories WHERE status = 'active' ORDER BY name ASC");
            $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $categories = [
                ['id' => 2, 'name' => 'المبيعات والتسويق'],
                ['id' => 3, 'name' => 'الموارد البشرية'],
            ];
        }

        try {
            $governoratesStmt = $this->db()->query("SELECT * FROM governorates WHERE status = 'active' ORDER BY name ASC");
            $governorates = $governoratesStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $governorates = [
                ['id' => 1, 'name' => 'القاهرة'],
                ['id' => 2, 'name' => 'الإسكندرية'],
            ];
        }

        try {
            $citiesStmt = $this->db()->query("SELECT * FROM cities WHERE status = 'active' ORDER BY governorate_id, name ASC");
            $cities = $citiesStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $cities = [
                ['id' => 1, 'name' => 'القاهرة'],
                ['id' => 2, 'name' => 'الإسكندرية'],
            ];
        }

        try {
            $durations = $this->db()->query('SELECT id, name, days FROM ad_durations WHERE is_active = 1 ORDER BY sort_order ASC, days ASC')->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $durations = [];
        }

        try {
            $jobStmt = $this->db()->prepare('SELECT * FROM jobs WHERE id = ? AND company_id = ? LIMIT 1');
            $jobStmt->execute([$jobId, (int) $company['id']]);
            $job = $jobStmt->fetch(PDO::FETCH_ASSOC);

            if (!$job) {
                $this->setFlash('error', 'الوظيفة المطلوبة غير موجودة');
                $this->redirect('/employer/jobs');
            }

            $job['type'] = $job['job_type'] ?? 'full-time';
            $job['advert_days'] = 30;

            if (!empty($job['published_at']) && !empty($job['expires_at'])) {
                $publishedAt = strtotime((string) $job['published_at']);
                $expiresAt = strtotime((string) $job['expires_at']);
                if ($publishedAt && $expiresAt && $expiresAt > $publishedAt) {
                    $job['advert_days'] = max(1, (int) floor(($expiresAt - $publishedAt) / 86400));
                }
            }
        } catch (Throwable $e) {
            $job = [
                'id' => $jobId,
                'title' => '',
                'category_id' => 0,
                'job_type' => 'full-time',
                'type' => 'full-time',
                'governorate_id' => 1,
                'city_id' => 1,
                'salary_min' => '',
                'salary_max' => '',
                'experience_level' => 'junior',
                'qualification' => '',
                'description' => '',
                'requirements' => '',
                'benefits' => '',
                'application_method' => 'internal',
                'application_whatsapp' => '',
                'application_phone' => '',
                'application_email' => '',
                'application_link' => '',
                'advert_days' => 30,
            ];
        }

        $this->view('employer.edit-job', [
            'title' => 'تعديل الوظيفة',
            'user' => $user,
            'jobId' => $jobId,
            'job' => $job,
            'categories' => $categories,
            'governorates' => $governorates,
            'cities' => $cities,
            'durations' => $durations,
        ]);
    }

    protected function findCompanyByUserId(int $userId): ?array
    {
        try {
            $stmt = $this->db()->prepare('SELECT * FROM companies WHERE user_id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
            return $company ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    protected function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-_]+/', '', $text);
        $text = preg_replace('/\s+/', '-', $text);
        return trim($text, '-');
    }

    protected function normalizeEgyptianPhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $digits = ltrim((string) $digits, '0');
        if (str_starts_with($digits, '20')) {
            $digits = substr($digits, 2);
        }

        return $digits !== '' ? '20' . $digits : null;
    }
}

