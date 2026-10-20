<?php

class JobSeekerController extends Controller
{
    public function index(): void
    {
        $this->ensureRequestsTable();
        $search = trim((string) ($_GET['q'] ?? ''));
        $governorateId = (int) ($_GET['governorate'] ?? 0);
        $conditions = ["u.type = 'job_seeker'", "u.status = 'active'"];
        $params = [];

        if ($search !== '') {
            $conditions[] = '(CONCAT(u.first_name, " ", u.last_name) LIKE ? OR u.bio LIKE ? OR u.qualification LIKE ? OR u.experience LIKE ? OR u.skills LIKE ?)';
            $like = '%' . $search . '%';
            $params = array_fill(0, 5, $like);
        }
        if ($governorateId > 0) {
            $conditions[] = 'u.governorate_id = ?';
            $params[] = $governorateId;
        }

        $stmt = $this->db()->prepare(
                'SELECT u.id, u.first_name, u.last_name, u.email, u.avatar, u.bio, u.date_of_birth, u.gender,
                    u.qualification, u.experience, u.skills, u.address,
                    g.name AS governorate_name, c.name AS city_name,
                    r.title AS resume_title
             FROM users u
             LEFT JOIN governorates g ON g.id = u.governorate_id
             LEFT JOIN cities c ON c.id = u.city_id
             LEFT JOIN resumes r ON r.id = (
                 SELECT r2.id FROM resumes r2 WHERE r2.user_id = u.id
                 ORDER BY r2.is_default DESC, r2.created_at DESC LIMIT 1
             )
             WHERE ' . implode(' AND ', $conditions) . '
             ORDER BY u.created_at DESC'
        );
        $stmt->execute($params);
        $seekers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $governorates = $this->db()->query('SELECT id, name FROM governorates ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('job-seekers.index', [
            'title' => 'الباحثون عن عمل',
            'seekers' => $seekers,
            'governorates' => $governorates,
            'search' => $search,
            'governorateId' => $governorateId,
        ]);
    }

    public function request(int $seekerId): void
    {
        $requester = current_user();
        if (!$requester) {
            $this->setFlash('error', 'سجل الدخول كصاحب عمل لإرسال طلب توظيف.');
            $this->redirect('/login');
        }
        if (($requester['type'] ?? '') !== 'employer') {
            $this->setFlash('error', 'إرسال طلبات التوظيف متاح لأصحاب العمل فقط.');
            $this->redirect('/job-seekers');
        }

        $pdo = $this->db();
        $seekerStmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE id = ? AND type = 'job_seeker' AND status = 'active' LIMIT 1");
        $seekerStmt->execute([$seekerId]);
        $seeker = $seekerStmt->fetch(PDO::FETCH_ASSOC);
        if (!$seeker) {
            $this->setFlash('error', 'الباحث عن العمل غير موجود.');
            $this->redirect('/job-seekers');
        }

        $message = trim((string) ($_POST['message'] ?? ''));
        if (mb_strlen($message, 'UTF-8') > 2000) {
            $this->setFlash('error', 'الرسالة طويلة جداً.');
            $this->redirect('/job-seekers');
        }

        $this->ensureRequestsTable();
        $duplicateStmt = $pdo->prepare("SELECT id FROM employment_requests WHERE requester_id = ? AND seeker_id = ? AND status = 'pending' LIMIT 1");
        $duplicateStmt->execute([(int) $requester['id'], $seekerId]);
        if ($duplicateStmt->fetchColumn()) {
            $this->setFlash('error', 'لديك طلب قيد المراجعة لهذا الباحث بالفعل.');
            $this->redirect('/job-seekers');
        }

        $insert = $pdo->prepare('INSERT INTO employment_requests (requester_id, seeker_id, message, status, created_at, updated_at) VALUES (?, ?, ?, "pending", NOW(), NOW())');
        $insert->execute([(int) $requester['id'], $seekerId, $message]);
        $requestId = (int) $pdo->lastInsertId();

        $admins = $pdo->query("SELECT id FROM users WHERE type = 'admin' AND status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
        $requesterName = trim(($requester['first_name'] ?? '') . ' ' . ($requester['last_name'] ?? ''));
        $seekerName = trim(($seeker['first_name'] ?? '') . ' ' . ($seeker['last_name'] ?? ''));
        $notification = $pdo->prepare('INSERT INTO notifications (user_id, type, title, message, related_type, related_id, action_url, is_read, created_at, updated_at) VALUES (?, "system", ?, ?, "employment_request", ?, ?, 0, NOW(), NOW())');
        foreach ($admins as $adminId) {
            $notification->execute([(int) $adminId, 'طلب توظيف باحث عن عمل', 'أرسل ' . $requesterName . ' طلباً للتواصل مع الباحث ' . $seekerName . '.', $requestId, '/admin/employment-requests']);
        }

        $this->setFlash('success', 'تم إرسال طلب التوظيف إلى الإدارة بنجاح.');
        $this->redirect('/job-seekers');
    }

    private function ensureRequestsTable(): void
    {
        $this->db()->exec(
            'CREATE TABLE IF NOT EXISTS employment_requests (
                id INT PRIMARY KEY AUTO_INCREMENT,
                requester_id INT NOT NULL,
                seeker_id INT NOT NULL,
                message TEXT,
                status ENUM("pending", "contacted", "closed") DEFAULT "pending",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_requester_id (requester_id),
                INDEX idx_seeker_id (seeker_id),
                INDEX idx_status (status),
                CONSTRAINT fk_employment_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT fk_employment_seeker FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}
