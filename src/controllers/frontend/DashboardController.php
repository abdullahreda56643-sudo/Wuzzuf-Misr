<?php
class DashboardController extends Controller
{
    public function index()
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }

        $stats = [
            'jobs_applied' => 0,
            'saved_jobs' => 0,
            'notifications' => 0,
        ];

        try {
            $pdo = $this->db();

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM job_applications WHERE user_id = ?');
            $stmt->execute([(int) $user['id']]);
            $stats['jobs_applied'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM saved_jobs WHERE user_id = ?');
            $stmt->execute([(int) $user['id']]);
            $stats['saved_jobs'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
            $stmt->execute([(int) $user['id']]);
            $stats['notifications'] = (int) $stmt->fetchColumn();
        } catch (Throwable $e) {
            $stats = [
                'jobs_applied' => 0,
                'saved_jobs' => 0,
                'notifications' => 0,
            ];
        }

        $jobs = [];
        try {
            $pdo = $this->db();
            $stmt = $pdo->prepare(
                "SELECT j.*, c.name AS company_name, c.logo AS company_logo, g.name AS governorate_name
                 FROM jobs j
                 LEFT JOIN companies c ON c.id = j.company_id
                 LEFT JOIN governorates g ON g.id = j.governorate_id
                 WHERE j.status = 'published' AND c.status = 'active' AND c.verification_status = 'verified'
                 ORDER BY j.created_at DESC
                 LIMIT 3"
            );
            $stmt->execute();
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $jobs = [];
        }

        // Get recent notifications
        $recentNotifications = [];
        try {
            $pdo = $this->db();
            $stmt = $pdo->prepare(
                "SELECT n.id, n.title, n.message, n.type, n.created_at, n.is_read
                 FROM notifications n
                 WHERE n.user_id = ?
                 ORDER BY n.is_read ASC, n.created_at DESC
                 LIMIT 5"
            );
            $stmt->execute([(int) $user['id']]);
            $recentNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $recentNotifications = [];
        }

        $acceptanceNotifications = [];
        try {
            $pdo = $this->db();
            $stmt = $pdo->prepare(
                "SELECT n.id, n.title, n.message, n.created_at, n.is_read
                 FROM notifications n
                 WHERE n.user_id = ? AND n.type = 'job_accepted'
                 ORDER BY n.created_at DESC
                 LIMIT 5"
            );
            $stmt->execute([(int) $user['id']]);
            $acceptanceNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $acceptanceNotifications = [];
        }

        $this->view('dashboard.index', [
            'title' => 'لوحة التحكم',
            'user' => $user,
            'stats' => $stats,
            'jobs' => $jobs,
            'recentNotifications' => $recentNotifications,
            'acceptanceNotifications' => $acceptanceNotifications,
        ]);
    }
}

