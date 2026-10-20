<?php

class AdController extends Controller
{
    public function index()
    {
        $this->requireAuth();
        
        $pdo = $this->db();
        $search = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(a.title LIKE ? OR a.ad_code LIKE ? OR a.ad_type LIKE ? OR a.description LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $countSql = 'SELECT COUNT(*) FROM ads a' . $whereSql;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalAds = (int) $countStmt->fetchColumn();

        $totalPages = max(1, (int) ceil($totalAds / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT a.*, l.name AS location_name, d.days AS duration_days
                FROM ads a
                LEFT JOIN ad_locations l ON l.id = a.ad_location_id
                LEFT JOIN ad_durations d ON d.id = a.ad_duration_id
                ' . $whereSql . '
                ORDER BY a.created_at DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $pdo->prepare($sql);
        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.ads.index', [
            'title' => 'إدارة الإعلانات',
            'ads' => $ads,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalAds' => $totalAds,
        ]);
    }

    public function create()
    {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $adCode = normalizeAdCode((string) $this->input('ad_code', ''));
            $title = trim((string) $this->input('title', ''));
            $type = trim((string) $this->input('ad_type', 'banner'));
            $locationInput = $this->input('ad_location_id', 'sidebar');
            $durationInput = $this->input('ad_duration_id', 30);
            $status = trim((string) $this->input('status', 'active'));
            $startDate = trim((string) $this->input('start_date', ''));
            $endDate = trim((string) $this->input('end_date', ''));
            $description = trim((string) $this->input('description', ''));

            // التحقق من صحة البيانات
            if ($title === '') {
                $this->setFlash('error', 'يرجى إدخال عنوان الإعلان');
                $this->redirect('/admin/ads/create');
                return;
            }

            if ($adCode === '') {
                $this->setFlash('error', 'لا يمكن حفظ إعلان فارغ. الرجاء إدخال كود Google AdSense صحيح.');
                $this->redirect('/admin/ads/create');
                return;
            }

            // استخدام دالة isValidAdCode للتحقق
            if (!function_exists('isValidAdCode') || !isValidAdCode($adCode)) {
                $this->setFlash('error', 'كود الإعلان غير صحيح. يجب أن يحتوي على adsbygoogle أو data-ad-client');
                $this->redirect('/admin/ads/create');
                return;
            }

            if ($startDate === '' || $endDate === '') {
                $this->setFlash('error', 'يرجى تحديد تاريخ البداية والنهاية');
                $this->redirect('/admin/ads/create');
                return;
            }

            $startTimestamp = strtotime($startDate);
            $endTimestamp = strtotime($endDate);
            if ($startTimestamp === false || $endTimestamp === false || $endTimestamp <= $startTimestamp) {
                $this->setFlash('error', 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية');
                $this->redirect('/admin/ads/create');
                return;
            }

            $userId = (int) ($this->currentUser()['id'] ?? 0);
            if ($userId <= 0) {
                $userId = 1;
            }

            $adType = $this->normalizeAdType($type);
            $locationId = $this->resolveLocationIdValue($locationInput);
            $durationId = $this->resolveDurationIdValue((int) $durationInput);
            $dbStatus = $this->normalizeStatus($status);

            $pdo = $this->db();
            $stmt = $pdo->prepare('
                INSERT INTO ads (user_id, title, ad_code, description, ad_location_id, ad_duration_id, ad_type, start_date, end_date, status, views_count, clicks_count, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW())
            ');
            $stmt->execute([
                $userId,
                $title,
                $adCode,
                $description,
                $locationId,
                $durationId,
                $adType,
                $startDate,
                $endDate,
                $dbStatus,
            ]);

            $this->setFlash('success', 'تم إضافة الإعلان بنجاح');
            $this->redirect('/admin/ads');
            return;
        }

        // جلب البيانات للعرض
        $pdo = $this->db();
        $locations = $pdo->query('SELECT id, name, slug FROM ad_locations WHERE is_active = 1 ORDER BY sort_order ASC, name ASC')->fetchAll(PDO::FETCH_ASSOC);
        $durations = $pdo->query('SELECT id, name, days FROM ad_durations WHERE is_active = 1 ORDER BY sort_order ASC, days ASC')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.ads.create', [
            'title' => 'إضافة إعلان',
            'locations' => $locations,
            'durations' => $durations,
        ]);
    }

    public function edit(int $id)
    {
        $this->requireAuth();
        
        $pdo = $this->db();

        if ($this->isPost()) {
            $adCode = normalizeAdCode((string) $this->input('ad_code', ''));
            $title = trim((string) $this->input('title', ''));
            $type = trim((string) $this->input('ad_type', 'banner'));
            $locationInput = $this->input('ad_location_id', 'sidebar');
            $durationInput = $this->input('ad_duration_id', 30);
            $status = trim((string) $this->input('status', 'active'));
            $startDate = trim((string) $this->input('start_date', ''));
            $endDate = trim((string) $this->input('end_date', ''));
            $description = trim((string) $this->input('description', ''));

            if ($title === '') {
                $this->setFlash('error', 'يرجى إدخال عنوان الإعلان');
                $this->redirect('/admin/ads/' . $id . '/edit');
                return;
            }

            if ($adCode === '') {
                $this->setFlash('error', 'لا يمكن حفظ إعلان فارغ. الرجاء إدخال كود Google AdSense صحيح.');
                $this->redirect('/admin/ads/' . $id . '/edit');
                return;
            }

            if (!function_exists('isValidAdCode') || !isValidAdCode($adCode)) {
                $this->setFlash('error', 'كود الإعلان غير صحيح. يجب أن يحتوي على adsbygoogle أو data-ad-client');
                $this->redirect('/admin/ads/' . $id . '/edit');
                return;
            }

            if ($startDate === '' || $endDate === '') {
                $this->setFlash('error', 'يرجى تحديد تاريخ البداية والنهاية');
                $this->redirect('/admin/ads/' . $id . '/edit');
                return;
            }

            $startTimestamp = strtotime($startDate);
            $endTimestamp = strtotime($endDate);
            if ($startTimestamp === false || $endTimestamp === false || $endTimestamp <= $startTimestamp) {
                $this->setFlash('error', 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية');
                $this->redirect('/admin/ads/' . $id . '/edit');
                return;
            }

            $adType = $this->normalizeAdType($type);
            $locationId = $this->resolveLocationIdValue($locationInput);
            $durationId = $this->resolveDurationIdValue((int) $durationInput);
            $dbStatus = $this->normalizeStatus($status);

            $stmt = $pdo->prepare('
                UPDATE ads SET 
                    title = ?,
                    ad_code = ?,
                    description = ?,
                    ad_location_id = ?,
                    ad_duration_id = ?,
                    ad_type = ?,
                    start_date = ?,
                    end_date = ?,
                    status = ?,
                    updated_at = NOW()
                WHERE id = ?
            ');
            $stmt->execute([$title, $adCode, $description, $locationId, $durationId, $adType, $startDate, $endDate, $dbStatus, $id]);

            $this->setFlash('success', 'تم تحديث الإعلان بنجاح');
            $this->redirect('/admin/ads');
            return;
        }

        // جلب بيانات الإعلان
        $stmt = $pdo->prepare('SELECT a.*, l.name AS location_name, d.days AS duration_days FROM ads a LEFT JOIN ad_locations l ON l.id = a.ad_location_id LEFT JOIN ad_durations d ON d.id = a.ad_duration_id WHERE a.id = ? LIMIT 1');
        $stmt->execute([$id]);
        $ad = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ad) {
            $this->setFlash('error', 'الإعلان غير موجود');
            $this->redirect('/admin/ads');
            return;
        }

        $locations = $this->db()->query('SELECT id, name, slug FROM ad_locations WHERE is_active = 1 ORDER BY sort_order ASC, name ASC')->fetchAll(PDO::FETCH_ASSOC);
        $durations = $this->db()->query('SELECT id, name, days FROM ad_durations WHERE is_active = 1 ORDER BY sort_order ASC, days ASC')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.ads.edit', [
            'title' => 'تعديل الإعلان',
            'ad' => $ad,
            'locations' => $locations,
            'durations' => $durations,
        ]);
    }

    public function delete(int $id)
    {
        $this->requireAuth();
        
        $this->db()->prepare('DELETE FROM ads WHERE id = ?')->execute([$id]);
        $this->setFlash('success', 'تم حذف الإعلان بنجاح');
        $this->redirect('/admin/ads');
    }

    protected function normalizeAdType(string $type): string
    {
        $mapped = [
            'company' => 'company_promo',
            'company_promo' => 'company_promo',
            'featured_job' => 'featured_job',
            'banner' => 'banner',
            'other' => 'other',
        ];

        return $mapped[$type] ?? 'banner';
    }

    protected function normalizeStatus(string $status): string
    {
        $mapped = [
            'active' => 'active',
            'inactive' => 'draft',
            'draft' => 'draft',
            'pending' => 'pending',
            'expired' => 'expired',
            'rejected' => 'rejected',
        ];

        return $mapped[$status] ?? 'draft';
    }

    protected function resolveLocationIdValue($location): int
    {
        if (is_numeric($location)) {
            return (int) $location;
        }

        $slugMap = [
            'sidebar' => 'sidebar',
            'homepage' => 'header-home',
            'jobs_page' => 'middle-jobs',
            'top' => 'header-home',
            'header-home' => 'header-home',
            'middle-jobs' => 'middle-jobs',
            'footer' => 'footer',
            'company-header' => 'company-header',
        ];

        $slug = $slugMap[(string) $location] ?? (string) $location;
        $stmt = $this->db()->prepare('SELECT id FROM ad_locations WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['id'] ?? 1);
    }

    protected function resolveDurationIdValue(int $duration): int
    {
        if ($duration > 0) {
            $stmt = $this->db()->prepare('SELECT id FROM ad_durations WHERE days = ? LIMIT 1');
            $stmt->execute([$duration]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return (int) $row['id'];
            }
        }

        $stmt = $this->db()->prepare('SELECT id FROM ad_durations ORDER BY days ASC LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['id'] ?? 1);
    }
}