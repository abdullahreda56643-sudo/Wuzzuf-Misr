<?php

class JobController extends Controller
{
    public function index()
    {
        $pdo = $this->db();
        $search = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(j.title LIKE ? OR j.description LIKE ? OR c.name LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $countSql = 'SELECT COUNT(*) FROM jobs j LEFT JOIN companies c ON c.id = j.company_id' . $whereSql;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalJobs = (int) $countStmt->fetchColumn();

        $totalPages = max(1, (int) ceil($totalJobs / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT j.*, c.name AS company_name, u.first_name, u.last_name
                 FROM jobs j
                 LEFT JOIN companies c ON c.id = j.company_id
                 LEFT JOIN users u ON u.id = c.user_id' . $whereSql . '
                 ORDER BY j.created_at DESC
                 LIMIT :limit OFFSET :offset';
        $stmt = $pdo->prepare($sql);

        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.jobs.index', [
            'title' => 'إدارة الوظائف',
            'jobs' => $jobs,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalJobs' => $totalJobs,
        ]);
    }

    public function create()
    {
        $pdo = $this->db();

        if ($this->isPost()) {
            $companyId = (int) $this->input('company_id', 0);
            $categoryId = (int) $this->input('category_id', 0);
            $governorateId = (int) $this->input('governorate_id', 0);
            $cityId = (int) $this->input('city_id', 0);
            $title = trim((string) $this->input('title', ''));
            $slug = trim((string) $this->input('slug', ''));
            $description = trim((string) $this->input('description', ''));
            $requirements = trim((string) $this->input('requirements', ''));
            $benefits = trim((string) $this->input('benefits', ''));
            $salaryMin = (float) $this->input('salary_min', 0);
            $salaryMax = (float) $this->input('salary_max', 0);
            $applicationMethod = trim((string) $this->input('application_method', 'internal'));
            $applicationWhatsapp = trim((string) $this->input('application_whatsapp', ''));
            $applicationPhone = trim((string) $this->input('application_phone', ''));
            $applicationEmail = trim((string) $this->input('application_email', ''));
            $applicationLink = trim((string) $this->input('application_link', ''));
            $status = trim((string) $this->input('status', 'draft'));
            $status = in_array($status, ['draft', 'published', 'closed'], true) ? $status : 'draft';

            if ($companyId <= 0 || $categoryId <= 0 || $governorateId <= 0 || $cityId <= 0 || $title === '' || $description === '') {
                $this->setFlash('error', 'يرجى تعبئة بيانات الوظيفة الأساسية');
                $this->redirect('/admin/jobs/create');
            }

            if ($salaryMin > 0 && $salaryMax > 0 && $salaryMax < $salaryMin) {
                $this->setFlash('error', 'يجب أن يكون الراتب إلى أكبر من أو يساوي الراتب من');
                $this->redirect('/admin/jobs/create');
            }

            $cityCheck = $pdo->prepare('SELECT id FROM cities WHERE id = ? AND governorate_id = ? LIMIT 1');
            $cityCheck->execute([$cityId, $governorateId]);
            if (!$cityCheck->fetchColumn()) {
                $this->setFlash('error', 'يرجى اختيار مدينة تابعة للمحافظة المحددة');
                $this->redirect('/admin/jobs/create');
            }

            $applicationMethod = in_array($applicationMethod, ['internal', 'whatsapp', 'phone', 'email', 'external_link'], true) ? $applicationMethod : 'internal';
            $applicationWhatsapp = $applicationMethod === 'whatsapp' ? $this->normalizeEgyptianPhone($applicationWhatsapp) : null;
            $applicationPhone = $applicationMethod === 'phone' ? $this->normalizeEgyptianPhone($applicationPhone) : null;
            $applicationEmail = $applicationMethod === 'email' ? $applicationEmail : null;
            $applicationLink = $applicationMethod === 'external_link' ? $applicationLink : null;

            $slug = $slug !== '' ? $slug : strtolower(str_replace(' ', '-', $title));
            $slug = preg_replace('/[^a-z0-9\-_]+/', '-', $slug);
            $slug = preg_replace('/-+/', '-', trim((string) $slug, '-'));

            $stmt = $pdo->prepare(
                'INSERT INTO jobs (company_id, title, slug, category_id, description, requirements, benefits, salary_min, salary_max, application_method, application_whatsapp, application_phone, application_email, application_link, governorate_id, city_id, published_at, expires_at, status, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CASE WHEN ? = "published" THEN NOW() ELSE NULL END, CASE WHEN ? = "published" THEN DATE_ADD(NOW(), INTERVAL 30 DAY) ELSE NULL END, ?, NOW())'
            );
            $stmt->execute([
                $companyId,
                $title,
                $slug,
                $categoryId,
                $description,
                $requirements !== '' ? $requirements : null,
                $benefits !== '' ? $benefits : null,
                $salaryMin > 0 ? $salaryMin : null,
                $salaryMax > 0 ? $salaryMax : null,
                $applicationMethod,
                $applicationWhatsapp,
                $applicationPhone,
                $applicationEmail,
                $applicationLink,
                $governorateId,
                $cityId,
                $status,
                $status,
                $status,
            ]);

            $this->setFlash('success', 'تمت إضافة الوظيفة بنجاح');
            $this->redirect('/admin/jobs');
        }

        $companies = $pdo->query('SELECT id, name FROM companies ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
        $categories = $pdo->query("SELECT id, name FROM job_categories WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $governorates = $pdo->query('SELECT id, name FROM governorates ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
        $cities = $pdo->query('SELECT id, name, governorate_id FROM cities ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.jobs.create', [
            'title' => 'إضافة وظيفة',
            'companies' => $companies,
            'categories' => $categories,
            'governorates' => $governorates,
            'cities' => $cities,
        ]);
    }

    public function details(int $id)
    {
        $stmt = $this->db()->prepare(
            'SELECT j.*, c.name AS company_name, jc.name AS category_name,
                    g.name AS governorate_name, ci.name AS city_name
             FROM jobs j
             LEFT JOIN companies c ON c.id = j.company_id
             LEFT JOIN job_categories jc ON jc.id = j.category_id
             LEFT JOIN governorates g ON g.id = j.governorate_id
             LEFT JOIN cities ci ON ci.id = j.city_id
             WHERE j.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            $this->setFlash('error', 'الوظيفة غير موجودة');
            $this->redirect('/admin/jobs');
        }

        $this->view('admin.jobs.view', [
            'title' => 'تفاصيل الوظيفة',
            'job' => $job,
        ]);
    }

    public function approve(int $id)
    {
        $stmt = $this->db()->prepare(
            'UPDATE jobs
             SET status = "published",
                 published_at = COALESCE(published_at, NOW()),
                 expires_at = COALESCE(expires_at, DATE_ADD(NOW(), INTERVAL 30 DAY)),
                 updated_at = NOW()
             WHERE id = ? AND status <> "published"'
        );
        $stmt->execute([$id]);

        $this->setFlash('success', 'تم اعتماد الوظيفة ونشرها بنجاح');
        $this->redirect('/admin/jobs');
    }

    public function reject(int $id)
    {
        $stmt = $this->db()->prepare(
            'UPDATE jobs
             SET status = "draft", published_at = NULL, expires_at = NULL, updated_at = NOW()
             WHERE id = ? AND status <> "published"'
        );
        $stmt->execute([$id]);

        $this->setFlash('success', 'تم رفض الوظيفة وإعادتها للمراجعة');
        $this->redirect('/admin/jobs');
    }

    public function edit(int $id)
    {
        $pdo = $this->db();

        $stmt = $pdo->prepare('SELECT * FROM jobs WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            $this->setFlash('error', 'الوظيفة غير موجودة');
            $this->redirect('/admin/jobs');
        }

        if ($this->isPost()) {
            $companyId = (int) $this->input('company_id', (int) ($job['company_id'] ?? 0));
            $title = trim((string) $this->input('title', (string) ($job['title'] ?? '')));
            $slug = trim((string) $this->input('slug', (string) ($job['slug'] ?? '')));
            $description = trim((string) $this->input('description', (string) ($job['description'] ?? '')));
            $requirements = trim((string) $this->input('requirements', (string) ($job['requirements'] ?? '')));
            $status = trim((string) $this->input('status', (string) ($job['status'] ?? 'draft')));
            $status = in_array($status, ['draft', 'published', 'closed'], true) ? $status : 'draft';

            if ($companyId <= 0 || $title === '' || $description === '') {
                $this->setFlash('error', 'يرجى تعبئة بيانات الوظيفة الأساسية');
                $this->redirect('/admin/jobs/' . $id . '/edit');
            }

            $slug = $slug !== '' ? $slug : strtolower(str_replace(' ', '-', $title));
            $slug = preg_replace('/[^a-z0-9\-_]+/', '-', $slug);
            $slug = preg_replace('/-+/', '-', trim((string) $slug, '-'));

            $update = $pdo->prepare(
                'UPDATE jobs
                 SET company_id = ?, title = ?, slug = ?, description = ?, requirements = ?, status = ?,
                     published_at = CASE WHEN ? = "published" THEN COALESCE(published_at, NOW()) ELSE published_at END,
                     expires_at = CASE WHEN ? = "published" THEN COALESCE(expires_at, DATE_ADD(NOW(), INTERVAL 30 DAY)) ELSE expires_at END,
                     updated_at = NOW()
                 WHERE id = ?'
            );
            $update->execute([
                $companyId,
                $title,
                $slug,
                $description,
                $requirements !== '' ? $requirements : null,
                $status,
                $status,
                $status,
                $id,
            ]);

            $this->setFlash('success', 'تم تحديث الوظيفة بنجاح');
            $this->redirect('/admin/jobs');
        }

        $companies = $pdo->query('SELECT id, name FROM companies ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.jobs.edit', [
            'title' => 'تعديل الوظيفة',
            'job' => $job,
            'companies' => $companies,
        ]);
    }

    public function delete(int $id)
    {
        $pdo = $this->db();
        $pdo->prepare('DELETE FROM jobs WHERE id = ?')->execute([$id]);

        $this->setFlash('success', 'تم حذف الوظيفة بنجاح');
        $this->redirect('/admin/jobs');
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

