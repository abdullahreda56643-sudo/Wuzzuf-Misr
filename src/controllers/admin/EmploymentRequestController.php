<?php

class EmploymentRequestController extends Controller
{
    public function index(): void
    {
        $pdo = $this->db();
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS employment_requests (
                id INT PRIMARY KEY AUTO_INCREMENT,
                requester_id INT NOT NULL,
                seeker_id INT NOT NULL,
                message TEXT,
                status ENUM("pending", "contacted", "closed") DEFAULT "pending",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_requester_id (requester_id), INDEX idx_seeker_id (seeker_id), INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
        $stmt = $pdo->query(
            'SELECT er.*, requester.first_name AS requester_first_name, requester.last_name AS requester_last_name,
                    requester.email AS requester_email, requester.phone AS requester_phone,
                    seeker.first_name AS seeker_first_name, seeker.last_name AS seeker_last_name,
                    seeker.phone AS seeker_phone, seeker.qualification, seeker.experience
             FROM employment_requests er
             INNER JOIN users requester ON requester.id = er.requester_id
             INNER JOIN users seeker ON seeker.id = er.seeker_id
             ORDER BY er.created_at DESC'
        );
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('admin.employment-requests.index', ['title' => 'طلبات توظيف الباحثين', 'requests' => $requests]);
    }

    public function updateStatus(int $id, string $status): void
    {
        if (!in_array($status, ['pending', 'contacted', 'closed'], true)) {
            $this->setFlash('error', 'الحالة غير صالحة.');
            $this->redirect('/admin/employment-requests');
        }
        $stmt = $this->db()->prepare('UPDATE employment_requests SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
        $this->setFlash('success', 'تم تحديث حالة الطلب.');
        $this->redirect('/admin/employment-requests');
    }
}
