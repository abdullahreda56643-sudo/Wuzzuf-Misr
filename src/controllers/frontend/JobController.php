<?php
class JobController extends Controller
{
    public function index()
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, (int) (config('jobs_per_page', 20) ?: 20));
        $currentUser = $this->currentUser();

        $keyword = trim((string) ($_GET['q'] ?? $_GET['title'] ?? ''));
        $categoryId = (int) ($_GET['category'] ?? 0);
        $governorateId = (int) ($_GET['location'] ?? $_GET['governorate'] ?? 0);
        $cityId = (int) ($_GET['city'] ?? 0);
        $jobType = trim((string) ($_GET['job_type'] ?? ''));
        $jobType = str_replace('_', '-', $jobType);
        $salaryMin = (int) ($_GET['salary_min'] ?? 0);
        $salaryMax = (int) ($_GET['salary_max'] ?? 0);
        $experience = trim((string) ($_GET['experience'] ?? ''));

        $experienceMap = [
            '0-1' => 'entry',
            '1-2' => 'junior',
            '2-3' => 'mid',
            '3-5' => 'senior',
            '5+' => 'lead',
        ];

        if (isset($experienceMap[$experience])) {
            $experience = $experienceMap[$experience];
        }

        $where = ["j.status = 'published'", "c.status = 'active'", "c.verification_status = 'verified'"];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(j.title LIKE ? OR j.description LIKE ? OR j.requirements LIKE ?)';
            $like = '%' . $keyword . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($categoryId > 0) {
            $where[] = 'j.category_id = ?';
            $params[] = $categoryId;
        }

        if ($governorateId > 0) {
            $where[] = 'j.governorate_id = ?';
            $params[] = $governorateId;
        }

        if ($cityId > 0) {
            $where[] = 'j.city_id = ?';
            $params[] = $cityId;
        }

        if ($jobType !== '') {
            $where[] = 'j.job_type = ?';
            $params[] = $jobType;
        }

        if ($experience !== '') {
            $where[] = 'j.experience_level = ?';
            $params[] = $experience;
        }

        if ($salaryMin > 0) {
            $where[] = '(j.salary_min >= ? OR j.salary_max >= ?)';
            $params[] = $salaryMin;
            $params[] = $salaryMin;
        }

        if ($salaryMax > 0) {
            $where[] = '(j.salary_max <= ? OR j.salary_min <= ?)';
            $params[] = $salaryMax;
            $params[] = $salaryMax;
        }

        $jobs = [];
        $totalJobs = 0;
        $totalPages = 1;
        $categories = [];
        $governorates = [];
        $cities = [];
        $savedJobIds = [];

        try {
            $pdo = $this->db();

            if ($currentUser) {
                $savedStmt = $pdo->prepare('SELECT job_id FROM saved_jobs WHERE user_id = ?');
                $savedStmt->execute([(int) $currentUser['id']]);
                $savedJobIds = array_map(static fn ($row) => (int) ($row['job_id'] ?? 0), $savedStmt->fetchAll(PDO::FETCH_ASSOC));
            }

            $categoryStmt = $pdo->query("SELECT jc.*, COALESCE((SELECT COUNT(*) FROM jobs j2 WHERE j2.category_id = jc.id AND j2.status = 'published'), 0) AS jobs_count FROM job_categories jc WHERE jc.status = 'active' ORDER BY jc.name ASC");
            $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

            $governorateStmt = $pdo->query("SELECT * FROM governorates WHERE status = 'active' ORDER BY name ASC");
            $governorates = $governorateStmt->fetchAll(PDO::FETCH_ASSOC);

            $cityStmt = $pdo->query("SELECT * FROM cities WHERE status = 'active' ORDER BY name ASC");
            $cities = $cityStmt->fetchAll(PDO::FETCH_ASSOC);

            $countSql = 'SELECT COUNT(*) FROM jobs j LEFT JOIN companies c ON c.id = j.company_id WHERE ' . implode(' AND ', $where);
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($params);
            $totalJobs = (int) $countStmt->fetchColumn();
            $totalPages = max(1, (int) ceil($totalJobs / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;

            $sql = 'SELECT j.id, j.title, j.company_id, j.category_id, j.governorate_id, j.city_id, j.job_type, j.experience_level,
                    j.salary_min, j.salary_max, j.salary_currency, j.salary_negotiable, j.description, j.requirements, j.benefits,
                    j.views_count, j.applications_count, j.application_method, j.application_whatsapp, j.application_phone,
                    j.application_email, j.application_link, j.created_at, j.published_at, j.expires_at, j.status,
                    COALESCE((SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = j.id), 0) AS applications_count_dynamic,
                    c.name AS company_name, c.logo AS company_logo, c.slug AS company_slug, g.name AS governorate_name, ci.name AS city_name,
                    COALESCE(ci.name, g.name, "غير محدد") AS location
                    FROM jobs j
                    LEFT JOIN companies c ON c.id = j.company_id
                    LEFT JOIN governorates g ON g.id = j.governorate_id
                    LEFT JOIN cities ci ON ci.id = j.city_id
                    WHERE ' . implode(' AND ', $where) . '
                    ORDER BY j.created_at DESC
                    LIMIT ? OFFSET ?';

            $stmt = $pdo->prepare($sql);
            $paramIndex = 1;
            foreach ($params as $value) {
                $stmt->bindValue($paramIndex++, $value);
            }
            $stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
            $stmt->bindValue($paramIndex, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($jobs as &$jobRow) {
                $jobRow['applications_count'] = (int) ($jobRow['applications_count_dynamic'] ?? (int) ($jobRow['applications_count'] ?? 0));
                unset($jobRow['applications_count_dynamic']);
            }
            unset($jobRow);
        } catch (Throwable $e) {
            $jobs = [];
            $totalJobs = 0;
            $totalPages = 1;
            $page = 1;
            $categories = [];
            $governorates = [];
            $cities = [];
        }

        $adSlots = $this->loadAdSlots([
            'header-home',
            'middle-jobs',
            'sidebar',
            'footer',
            'company-header',
        ]);

        $this->view('jobs.index', [
            'title' => 'الوظائف',
            'jobs' => $jobs,
            'categories' => $categories,
            'governorates' => $governorates,
            'cities' => $cities,
            'keyword' => $keyword,
            'selectedCategoryId' => $categoryId,
            'selectedGovernorateId' => $governorateId,
            'selectedCityId' => $cityId,
            'selectedJobType' => $jobType,
            'selectedExperience' => $experience,
            'salaryMin' => $salaryMin,
            'salaryMax' => $salaryMax,
            'savedJobIds' => $savedJobIds,
            'page' => $page,
            'totalPages' => $totalPages,
            'homeAds' => $adSlots,
        ]);
    }

    public function show(int $id)
    {
        try {
            $pdo = $this->db();
            $pdo->prepare('UPDATE jobs SET views_count = views_count + 1 WHERE id = ?')->execute([$id]);

            $stmt = $pdo->prepare(
                'SELECT j.id, j.title, j.company_id, j.category_id, j.governorate_id, j.city_id, j.job_type, j.experience_level,
                        j.salary_min, j.salary_max, j.salary_currency, j.salary_negotiable, j.description, j.requirements,
                        j.benefits, j.application_method, j.application_whatsapp, j.application_phone, j.application_email,
                        j.application_link, j.created_at, j.views_count, j.applications_count,
                        COALESCE((SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = j.id), 0) AS applications_count_dynamic,
                        c.name AS company_name, c.logo AS company_logo, c.slug AS company_slug,
                        g.name AS governorate_name, ci.name AS city_name,
                        COALESCE(ci.name, g.name, "غير محدد") AS location
                 FROM jobs j
                 LEFT JOIN companies c ON c.id = j.company_id
                 LEFT JOIN governorates g ON g.id = j.governorate_id
                 LEFT JOIN cities ci ON ci.id = j.city_id
                 WHERE j.id = ? AND j.status = \'published\' AND c.status = \'active\' AND c.verification_status = \'verified\' LIMIT 1'
            );
            $stmt->execute([$id]);
            $job = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($job) {
                $job['views_count'] = (int) ($job['views_count'] ?? 0) + 0;
                $job['applications_count'] = (int) ($job['applications_count_dynamic'] ?? (int) ($job['applications_count'] ?? 0));
                unset($job['applications_count_dynamic']);
            }
        } catch (Throwable $e) {
            $job = null;
        }

        if (!$job) {
            $this->setFlash('error', 'لم يتم العثور على الوظيفة');
            $this->redirect('/jobs');
        }

        $adSlots = $this->loadAdSlots([
            'header-home',
            'middle-jobs',
            'sidebar',
            'footer',
            'company-header',
        ]);

        $this->view('jobs.show', [
            'title' => $job['title'] ?? 'تفاصيل الوظيفة',
            'job' => $job,
            'homeAds' => $adSlots,
        ]);
    }

    public function apply(int $id)
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        if ($this->isPost()) {
            $coverLetter = trim((string) $this->input('cover_letter', ''));
            $resumeId = (int) $this->input('resume_id', 0);

            try {
                $pdo = $this->db();
                $stmt = $pdo->prepare(
                    'INSERT INTO job_applications (job_id, user_id, resume_id, cover_letter, status, applied_at) VALUES (?, ?, ?, ?, "pending", NOW())'
                );
                $stmt->execute([$id, (int) $user['id'], $resumeId ?: null, $coverLetter ?: null]);

                $pdo->prepare('UPDATE jobs SET applications_count = applications_count + 1 WHERE id = ?')->execute([$id]);

                $this->setFlash('success', 'تم تقديم طلبك بنجاح');
                $this->redirect('/jobs/' . $id);
            } catch (Throwable $e) {
                $this->setFlash('error', 'حدث خطأ أثناء تقديم الطلب');
                $this->redirect('/jobs/' . $id);
            }
        }

        $this->redirect('/jobs/' . $id);
    }

    public function save(int $id)
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        try {
            $pdo = $this->db();
            $checkStmt = $pdo->prepare('SELECT id FROM saved_jobs WHERE user_id = ? AND job_id = ? LIMIT 1');
            $checkStmt->execute([(int) $user['id'], $id]);

            if ($checkStmt->fetch()) {
                $deleteStmt = $pdo->prepare('DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?');
                $deleteStmt->execute([(int) $user['id'], $id]);
                $this->setFlash('success', 'تم إلغاء حفظ الوظيفة');
            } else {
                $insertStmt = $pdo->prepare('INSERT INTO saved_jobs (user_id, job_id, saved_at) VALUES (?, ?, NOW())');
                $insertStmt->execute([(int) $user['id'], $id]);
                $this->setFlash('success', 'تم حفظ الوظيفة في قائمة المفضلة');
            }
        } catch (Throwable $e) {
            $this->setFlash('error', 'تعذر تحديث حالة حفظ الوظيفة');
        }

        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $redirectPath = '/jobs';

        if ($referrer !== '') {
            $referrerParts = parse_url($referrer);
            $referrerPath = $referrerParts['path'] ?? '/';
            $siteRoot = parse_url(config('site_url', '/'), PHP_URL_PATH) ?: '';
            $siteRoot = rtrim((string) $siteRoot, '/');

            if ($siteRoot !== '' && str_starts_with($referrerPath, $siteRoot)) {
                $referrerPath = substr($referrerPath, strlen($siteRoot));
            }

            $referrerPath = '/' . ltrim($referrerPath, '/');
            if ($referrerPath === '') {
                $referrerPath = '/';
            }

            $redirectPath = $referrerPath;
            if (($referrerParts['query'] ?? '') !== '') {
                $redirectPath .= '?' . $referrerParts['query'];
            }
        }

        $this->redirect($redirectPath);
    }
}

