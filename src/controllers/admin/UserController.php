<?php

class UserController extends Controller
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
            $where[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR CONCAT(first_name, " ", last_name) LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $countSql = 'SELECT COUNT(*) FROM users' . $whereSql;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalUsers = (int) $countStmt->fetchColumn();

        $totalPages = max(1, (int) ceil($totalUsers / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT u.*, '
            . 'g.name AS governorate_name, city.name AS city_name, '
            . 'company_governorate.name AS company_governorate_name, company_city.name AS company_city_name, '
            . 'company.id AS company_id, company.name AS company_name, company.email AS company_email, '
            . 'company.phone AS company_phone, company.website AS company_website, company.description AS company_description, '
            . 'company.logo AS company_logo, company.cover_image AS company_cover_image, company.industry AS company_industry, '
            . 'company.company_size, company.founded_year, company.address AS company_address, '
            . 'company.verification_status AS company_verification_status, company.status AS company_status, '
            . 'resume.title AS resume_title, resume.file_path AS resume_file_path, resume.file_name AS resume_file_name '
            . 'FROM users u '
            . 'LEFT JOIN governorates g ON g.id = u.governorate_id '
            . 'LEFT JOIN cities city ON city.id = u.city_id '
            . 'LEFT JOIN companies company ON company.user_id = u.id '
            . 'LEFT JOIN governorates company_governorate ON company_governorate.id = company.governorate_id '
            . 'LEFT JOIN cities company_city ON company_city.id = company.city_id '
            . 'LEFT JOIN resumes resume ON resume.id = ('
            . 'SELECT default_resume.id FROM resumes default_resume '
            . 'WHERE default_resume.user_id = u.id '
            . 'ORDER BY default_resume.is_default DESC, default_resume.created_at DESC LIMIT 1'
            . ')' . str_replace('first_name', 'u.first_name', str_replace('last_name', 'u.last_name', str_replace('email', 'u.email', $whereSql)))
            . ' ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $pdo->prepare($sql);

        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.users.index', [
            'title' => 'إدارة المستخدمين',
            'users' => $users,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
        ]);
    }

    public function edit(int $id)
    {
        $pdo = $this->db();

        if ($this->isPost()) {
            $status = trim((string) $this->input('status', 'active'));
            $type = trim((string) $this->input('type', 'job_seeker'));

            $stmt = $pdo->prepare('UPDATE users SET status = ?, type = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$status, $type, $id]);

            $this->setFlash('success', 'تم تحديث المستخدم بنجاح');
            $this->redirect('/admin/users');
        }

        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('admin.users.edit', [
            'title' => 'تعديل المستخدم',
            'user' => $user,
        ]);
    }

    public function delete(int $id)
    {
        $this->db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
        $this->setFlash('success', 'تم حذف المستخدم بنجاح');
        $this->redirect('/admin/users');
    }
}

