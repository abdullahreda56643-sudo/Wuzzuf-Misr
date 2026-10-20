<?php

class ReportController extends Controller
{
    public function index()
    {
        $pdo = $this->db();

        $status = $_GET['status'] ?? 'all';
        $type = $_GET['type'] ?? 'all';
        $search = trim((string) ($_GET['q'] ?? ''));

        $where = [];
        $params = [];

        if ($status !== 'all') {
            $where[] = 'r.status = ?';
            $params[] = $status;
        }

        if ($type !== 'all') {
            $where[] = 'r.reported_type = ?';
            $params[] = $type;
        }

        if ($search !== '') {
            $where[] = '(LOWER(r.description) LIKE ? OR LOWER(u.first_name) LIKE ? OR LOWER(u.last_name) LIKE ? OR LOWER(u.email) LIKE ?)';
            $term = '%' . strtolower($search) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql = 'SELECT r.*, u.first_name, u.last_name, u.email FROM reports r LEFT JOIN users u ON u.id = r.reporter_id';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY r.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $statsStmt = $pdo->query('SELECT status, COUNT(*) AS total FROM reports GROUP BY status');
        $statsRows = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

        $stats = [
            'pending' => 0,
            'reviewing' => 0,
            'resolved' => 0,
            'rejected' => 0,
        ];

        foreach ($statsRows as $row) {
            $statusKey = (string) ($row['status'] ?? '');
            if (isset($stats[$statusKey])) {
                $stats[$statusKey] = (int) $row['total'];
            }
        }

        $this->view('admin.reports.index', [
            'title' => 'التقارير',
            'reports' => $reports,
            'status' => $status,
            'type' => $type,
            'search' => $search,
            'stats' => $stats,
        ]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $allowedStatuses = ['reviewing', 'resolved', 'rejected', 'pending'];
        if (!in_array($status, $allowedStatuses, true)) {
            $this->setFlash('error', 'حالة البلاغ غير صالحة.');
            $this->redirect('/admin/reports');
            return;
        }

        $stmt = $this->db()->prepare('UPDATE reports SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        $this->setFlash('success', 'تم تحديث حالة البلاغ بنجاح.');
        $this->redirect('/admin/reports');
    }

    public function delete(int $id): void
    {
        $stmt = $this->db()->prepare('DELETE FROM reports WHERE id = ?');
        $stmt->execute([$id]);
        $this->setFlash('success', 'تم حذف البلاغ بنجاح.');
        $this->redirect('/admin/reports');
    }
}

