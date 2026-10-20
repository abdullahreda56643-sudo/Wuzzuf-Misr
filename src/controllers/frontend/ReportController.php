<?php

class ReportController extends Controller
{
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/');
            return;
        }

        $type = trim((string) $this->input('reported_type', 'other'));
        $reason = trim((string) $this->input('reason', 'other'));
        $description = trim((string) $this->input('description', ''));
        $reportedId = max(0, (int) $this->input('reported_id', 0));
        $returnTo = trim((string) $this->input('return_to', '/'));

        $allowedTypes = ['job', 'company', 'user', 'ad', 'other'];
        $allowedReasons = ['spam', 'inappropriate', 'fraud', 'harassment', 'copyright', 'other'];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'other';
        }
        if (!in_array($reason, $allowedReasons, true)) {
            $reason = 'other';
        }
        if ($description === '' || mb_strlen($description, 'UTF-8') < 10) {
            $this->setFlash('error', 'يرجى كتابة تفاصيل البلاغ (10 أحرف على الأقل).');
            $this->redirect($this->safeReturnPath($returnTo));
            return;
        }

        try {
            $pdo = $this->db();
            $pdo->exec("CREATE TABLE IF NOT EXISTS reports (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                reporter_id INT UNSIGNED NULL,
                reported_type VARCHAR(30) NOT NULL,
                reported_id INT UNSIGNED NOT NULL DEFAULT 0,
                reason VARCHAR(30) NOT NULL DEFAULT 'other',
                description TEXT NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'pending',
                ip_address VARCHAR(45) NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL,
                PRIMARY KEY (id),
                INDEX idx_reports_status (status),
                INDEX idx_reports_target (reported_type, reported_id),
                INDEX idx_reports_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $user = $this->currentUser();
            $reporterId = (int) ($user['id'] ?? 0);
            $stmt = $pdo->prepare('INSERT INTO reports (reporter_id, reported_type, reported_id, reason, description, status) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $reporterId > 0 ? $reporterId : null,
                $type,
                $reportedId,
                $reason,
                $description,
                'pending',
            ]);

            $this->setFlash('success', 'تم إرسال البلاغ بنجاح، وسيتم مراجعته من الإدارة.');
        } catch (Throwable $e) {
            error_log('Report submission failed: ' . $e->getMessage());
            $this->setFlash('error', 'تعذر إرسال البلاغ حاليًا. حاول مرة أخرى لاحقًا.');
        }

        $this->redirect($this->safeReturnPath($returnTo));
    }

    private function safeReturnPath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';
        $basePath = parse_url((string) config('site_url', '/'), PHP_URL_PATH) ?: '';
        $basePath = '/' . trim($basePath, '/');

        if ($basePath !== '/' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) {
            return '/';
        }

        return $path;
    }
}
