<?php
class HomeController extends Controller
{
    public function index()
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, (int) (config('jobs_per_page', 6) ?: 6));
        $currentUser = $this->currentUser();

        $keyword = trim((string) ($_GET['q'] ?? $_GET['title'] ?? ''));
        $categoryId = (int) ($_GET['category'] ?? 0);
        $governorateId = (int) ($_GET['location'] ?? $_GET['governorate'] ?? 0);
        $jobType = trim((string) ($_GET['job_type'] ?? ''));
        $jobType = str_replace('_', '-', $jobType);

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

        if ($jobType !== '') {
            $where[] = 'j.job_type = ?';
            $params[] = $jobType;
        }

        $savedJobIds = [];

        try {
            $pdo = $this->db();

            if ($currentUser) {
                $savedStmt = $pdo->prepare('SELECT job_id FROM saved_jobs WHERE user_id = ?');
                $savedStmt->execute([(int) $currentUser['id']]);
                $savedJobIds = array_map(static fn ($row) => (int) ($row['job_id'] ?? 0), $savedStmt->fetchAll(PDO::FETCH_ASSOC));
            }

            $countSql = 'SELECT COUNT(*) FROM jobs j LEFT JOIN companies c ON c.id = j.company_id WHERE ' . implode(' AND ', $where);
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($params);
            $totalJobs = (int) $countStmt->fetchColumn();
            $totalPages = max(1, (int) ceil($totalJobs / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;

            $sql = "SELECT j.*, c.name AS company_name, c.logo AS company_logo, c.slug AS company_slug, g.name AS governorate_name
                    FROM jobs j
                    LEFT JOIN companies c ON c.id = j.company_id
                    LEFT JOIN governorates g ON g.id = j.governorate_id
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY j.created_at DESC
                    LIMIT ? OFFSET ?";

            $stmt = $pdo->prepare($sql);
            $paramIndex = 1;
            foreach ($params as $value) {
                $stmt->bindValue($paramIndex++, $value);
            }
            $stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
            $stmt->bindValue($paramIndex, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $jobs = [];
            $totalJobs = 0;
            $totalPages = 1;
            $page = 1;
        }

        try {
            $pdo = $this->db();
            $stmt = $pdo->query(
                "SELECT c.*, g.name AS governorate_name, ci.name AS city_name
                 FROM companies c
                 LEFT JOIN governorates g ON g.id = c.governorate_id
                 LEFT JOIN cities ci ON ci.id = c.city_id
                 WHERE c.status = 'active' AND c.verification_status = 'verified'
                 ORDER BY c.created_at DESC
                 LIMIT 4"
            );
            $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($companies as &$company) {
                $companyId = (int) ($company['id'] ?? 0);
                if ($companyId > 0) {
                    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM jobs WHERE company_id = ? AND status = ?');
                    $countStmt->execute([$companyId, 'published']);
                    $company['jobs_count'] = (int) $countStmt->fetchColumn();
                } else {
                    $company['jobs_count'] = 0;
                }
            }
            unset($company);
        } catch (Throwable $e) {
            $companies = [];
        }

        try {
            $pdo = $this->db();
            $stmt = $pdo->query("SELECT * FROM job_categories WHERE status = 'active' ORDER BY name ASC");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $categories = [];
        }

        $popularSearches = [
            ['label' => 'مصممون', 'query' => 'design'],
            ['label' => 'مهندسون', 'query' => 'engineering'],
            ['label' => 'تسويق', 'query' => 'marketing'],
            ['label' => 'إدارة', 'query' => 'management'],
        ];

        if (!empty($categories)) {
            $categoryMap = [
                'التصميم' => ['label' => 'مصممون', 'query' => 'design'],
                'تصميم' => ['label' => 'مصممون', 'query' => 'design'],
                'الهندسة' => ['label' => 'مهندسون', 'query' => 'engineering'],
                'هندسة' => ['label' => 'مهندسون', 'query' => 'engineering'],
                'المبيعات والتسويق' => ['label' => 'تسويق', 'query' => 'marketing'],
                'التسويق' => ['label' => 'تسويق', 'query' => 'marketing'],
                'الإدارة والعمليات' => ['label' => 'إدارة', 'query' => 'management'],
                'الإدارة' => ['label' => 'إدارة', 'query' => 'management'],
            ];

            $matched = [];
            foreach ($categories as $category) {
                $name = trim((string) ($category['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                if (isset($categoryMap[$name])) {
                    $matched[$name] = $categoryMap[$name];
                }
            }

            if (!empty($matched)) {
                $popularSearches = array_values($matched);
            }
        }

        try {
            $pdo = $this->db();
            $stmt = $pdo->query("SELECT * FROM governorates WHERE status = 'active' ORDER BY name ASC");
            $governorates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $governorates = [];
        }

        $homeAdSlots = [
            'header-home' => null,
            'middle-jobs' => null,
            'sidebar' => null,
            'footer' => null,
            'company-header' => null,
        ];

        try {
            $pdo = $this->db();
            $locationRows = $pdo->query("SELECT id, slug FROM ad_locations WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
            $adService = new AdService();

            foreach ($locationRows as $location) {
                $slug = trim((string) ($location['slug'] ?? ''));
                $locationId = (int) ($location['id'] ?? 0);

                if ($slug !== '' && $locationId > 0) {
                    $adsForLocation = $adService->getActiveAdsForLocation($locationId);
                    if (!empty($adsForLocation)) {
                        $this->recordAdImpression($adsForLocation[0]);
                        $homeAdSlots[$slug] = $adsForLocation[0];
                    }
                }
            }
        } catch (Throwable $e) {
            $homeAdSlots = [
                'header-home' => null,
                'middle-jobs' => null,
                'sidebar' => null,
                'footer' => null,
                'company-header' => null,
            ];
        }

        try {
            $pdo = $this->db();
            foreach ($categories as &$cat) {
                $catId = (int) ($cat['id'] ?? 0);
                $countStmt = $pdo->prepare('SELECT COUNT(*) FROM jobs WHERE category_id = ? AND status = ?');
                $countStmt->execute([$catId, 'published']);
                $cat['jobs_count'] = (int) $countStmt->fetchColumn();
            }
        } catch (Throwable $e) {
            foreach ($categories as &$cat) {
                $cat['jobs_count'] = 0;
            }
        }

        $this->view('home', [
            'jobs' => $jobs,
            'companies' => $companies,
            'categories' => $categories,
            'governorates' => $governorates,
            'keyword' => $keyword,
            'selectedCategoryId' => $categoryId,
            'selectedGovernorateId' => $governorateId,
            'selectedJobType' => $jobType,
            'savedJobIds' => $savedJobIds,
            'page' => $page,
            'perPage' => $perPage,
            'totalJobs' => $totalJobs ?? 0,
            'totalPages' => $totalPages ?? 1,
            'popularSearches' => $popularSearches,
            'homeAds' => $homeAdSlots,
            'title' => 'الرئيسية',
        ]);
    }

    public function about()
    {
        $adSlots = $this->loadAdSlots([
            'header-home',
            'middle-jobs',
            'sidebar',
            'footer',
            'company-header',
        ]);

        $this->view('pages.about', [
            'title' => 'من نحن',
            'homeAds' => $adSlots,
        ]);
    }

    public function contact()
    {
        if ($this->isPost()) {
            $name = trim((string) $this->input('name', ''));
            $email = trim((string) $this->input('email', ''));
            $message = trim((string) $this->input('message', ''));

            if ($name !== '' && $email !== '' && $message !== '') {
                $this->setFlash('success', 'تم إرسال الرسالة بنجاح');
                $this->redirect('/contact');
            }

            $this->setFlash('error', 'يرجى تعبئة جميع الحقول');
            $this->redirect('/contact');
        }

        $adSlots = $this->loadAdSlots([
            'header-home',
            'middle-jobs',
            'sidebar',
            'footer',
            'company-header',
        ]);

        $this->view('pages.contact', [
            'title' => 'تواصل معنا',
            'homeAds' => $adSlots,
        ]);
    }
}

