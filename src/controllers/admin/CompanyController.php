<?php

class CompanyController extends Controller
{
    use UploadTrait;

    public function create()
    {
        $pdo = $this->db();
        $categories = $pdo->query("SELECT id, name FROM job_categories WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        if ($this->isPost()) {
            $companyName = trim((string) $this->input('company_name', ''));
            $managerName = trim((string) $this->input('manager_name', ''));
            $email = trim((string) $this->input('email', ''));
            $phone = trim((string) $this->input('phone', ''));
            $password = (string) $this->input('password', '');
            $website = trim((string) $this->input('website', ''));
            $industry = trim((string) $this->input('industry', ''));
            $description = trim((string) $this->input('description', ''));
            $governorateId = (int) $this->input('governorate_id', 0);
            $cityId = (int) $this->input('city_id', 0);

            $categoryNames = array_map(static fn(array $category): string => (string) ($category['name'] ?? ''), $categories);

            if ($companyName === '' || $managerName === '' || $email === '' || $phone === '' || $password === '' || $industry === '' || $governorateId <= 0 || $cityId <= 0) {
                $this->setFlash('error', 'يرجى تعبئة بيانات الشركة الأساسية');
                $this->redirect('/admin/companies/create');
            }

            if (!in_array($industry, $categoryNames, true)) {
                $this->setFlash('error', 'يرجى اختيار نشاط تجاري صحيح من التصنيفات المتاحة');
                $this->redirect('/admin/companies/create');
            }

            $cityCheck = $pdo->prepare('SELECT id FROM cities WHERE id = ? AND governorate_id = ? LIMIT 1');
            $cityCheck->execute([$cityId, $governorateId]);
            if (!$cityCheck->fetchColumn()) {
                $this->setFlash('error', 'يرجى اختيار مدينة تابعة للمحافظة المحددة');
                $this->redirect('/admin/companies/create');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('error', 'يرجى إدخال بريد إلكتروني صحيح');
                $this->redirect('/admin/companies/create');
            }

            try {
                $pdo->beginTransaction();
                $emailCheck = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $emailCheck->execute([$email]);
                if ($emailCheck->fetch()) {
                    throw new RuntimeException('هذا البريد الإلكتروني مسجل بالفعل');
                }

                $companyCheck = $pdo->prepare('SELECT id FROM companies WHERE name = ? LIMIT 1');
                $companyCheck->execute([$companyName]);
                if ($companyCheck->fetch()) {
                    throw new RuntimeException('اسم الشركة مسجل بالفعل في النظام');
                }

                $names = preg_split('/\s+/', $managerName, 2);
                $userStmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, phone, password, type, status, created_at) VALUES (?, ?, ?, ?, ?, "employer", "active", NOW())');
                $userStmt->execute([
                    trim((string) ($names[0] ?? 'مسؤول')),
                    trim((string) ($names[1] ?? '')) ?: 'غير محدد',
                    $email,
                    $phone,
                    password_hash($password, PASSWORD_DEFAULT),
                ]);
                $userId = (int) $pdo->lastInsertId();

                $slug = 'company-' . $userId;
                $logoPath = null;
                if (isset($_FILES['logo']) && !empty($_FILES['logo']['name'])) {
                    $upload = $this->handleFileUpload($_FILES['logo'], 'companies', ['jpg', 'jpeg', 'png', 'gif', 'webp'], (int) config('max_upload_size', 5242880), 'company_logo');
                    if (!$upload['success']) {
                        throw new RuntimeException($upload['message']);
                    }
                    $logoPath = $upload['path'];
                }

                $stmt = $pdo->prepare(
                    'INSERT INTO companies (user_id, name, slug, email, phone, website, description, logo, industry, governorate_id, city_id, verification_status, status, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "pending", "active", NOW())'
                );
                $stmt->execute([$userId, $companyName, $slug, $email, $phone, $website !== '' ? $website : null, $description !== '' ? $description : null, $logoPath, $industry, $governorateId, $cityId]);
                $pdo->commit();
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/admin/companies/create');
            }

            $this->setFlash('success', 'تم إنشاء حساب صاحب العمل وإضافة الشركة بانتظار التوثيق');
            $this->redirect('/admin/companies');
        }

        $governorates = $pdo->query('SELECT id, name FROM governorates ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $cities = $pdo->query('SELECT id, name, governorate_id FROM cities ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.companies.create', [
            'title' => 'إضافة شركة',
            'categories' => $categories,
            'governorates' => $governorates,
            'cities' => $cities,
        ]);
    }

    public function verify(int $id)
    {
        $this->db()->prepare('UPDATE companies SET verification_status = "verified", status = "active", updated_at = NOW() WHERE id = ?')->execute([$id]);
        $this->setFlash('success', 'تم اعتماد توثيق الشركة');
        $this->redirect('/admin/companies');
    }

    public function rejectVerification(int $id)
    {
        $this->db()->prepare('UPDATE companies SET verification_status = "rejected", updated_at = NOW() WHERE id = ?')->execute([$id]);
        $this->setFlash('success', 'تم رفض توثيق الشركة');
        $this->redirect('/admin/companies');
    }

    public function index()
    {
        $pdo = $this->db();
        $search = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(c.name LIKE ? OR c.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $countSql = 'SELECT COUNT(*) FROM companies c LEFT JOIN users u ON u.id = c.user_id' . $whereSql;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalCompanies = (int) $countStmt->fetchColumn();

        $totalPages = max(1, (int) ceil($totalCompanies / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT c.*, u.first_name, u.last_name FROM companies c LEFT JOIN users u ON u.id = c.user_id' . $whereSql . ' ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $pdo->prepare($sql);

        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.companies.index', [
            'title' => 'إدارة الشركات',
            'companies' => $companies,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalCompanies' => $totalCompanies,
        ]);
    }

    public function edit(int $id)
    {
        $pdo = $this->db();

        if ($this->isPost()) {
            $status = trim((string) $this->input('status', 'active'));
            $verificationStatus = trim((string) $this->input('verification_status', 'pending'));

            $stmt = $pdo->prepare('UPDATE companies SET status = ?, verification_status = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$status, $verificationStatus, $id]);

            $this->setFlash('success', 'تم تحديث حالة الشركة بنجاح');
            $this->redirect('/admin/companies');
        }

        $stmt = $pdo->prepare('SELECT * FROM companies WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('admin.companies.edit', [
            'title' => 'تعديل الشركة',
            'company' => $company,
        ]);
    }

    public function delete(int $id)
    {
        $this->db()->prepare('DELETE FROM companies WHERE id = ?')->execute([$id]);
        $this->setFlash('success', 'تم حذف الشركة بنجاح');
        $this->redirect('/admin/companies');
    }
}

