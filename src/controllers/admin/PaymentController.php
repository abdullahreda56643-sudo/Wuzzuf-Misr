<?php

class PaymentController extends Controller
{
    public function index()
    {
        $pdo = $this->db();
        $stmt = $pdo->query('SELECT p.*, u.first_name, u.last_name FROM payments p LEFT JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC');
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.payments.index', [
            'title' => 'إدارة المدفوعات',
            'payments' => $payments,
        ]);
    }
}

