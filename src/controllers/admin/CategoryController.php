<?php

class CategoryController extends Controller
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
            $where[] = '(name LIKE ? OR description LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $countSql = 'SELECT COUNT(*) FROM job_categories' . $whereSql;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalCategories = (int) $countStmt->fetchColumn();

        $totalPages = max(1, (int) ceil($totalCategories / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT * FROM job_categories' . $whereSql . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $pdo->prepare($sql);

        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.categories.index', [
            'title' => 'إدارة التصنيفات',
            'categories' => $categories,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalCategories' => $totalCategories,
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $name = trim((string) $this->input('name', ''));
            $description = trim((string) $this->input('description', ''));
            $status = $this->input('status', 'active');

            if ($name === '') {
                $this->setFlash('error', 'يرجى إدخال اسم التصنيف');
                $this->redirect('/admin/categories/create');
            }

            $slug = strtolower(str_replace(' ', '-', trim($name)));
            $pdo = $this->db();
            $stmt = $pdo->prepare('INSERT INTO job_categories (name, slug, description, status, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $slug, $description, $status]);

            $this->setFlash('success', 'تم إضافة التصنيف بنجاح');
            $this->redirect('/admin/categories');
        }

        $this->view('admin.categories.add', [
            'title' => 'إضافة تصنيف',
        ]);
    }

    public function edit(int $id)
    {
        $pdo = $this->db();

        if ($this->isPost()) {
            $name = trim((string) $this->input('name', ''));
            $description = trim((string) $this->input('description', ''));
            $status = $this->input('status', 'active');

            if ($name === '') {
                $this->setFlash('error', 'يرجى إدخال اسم التصنيف');
                $this->redirect('/admin/categories/' . $id . '/edit');
            }

            $slug = strtolower(str_replace(' ', '-', trim($name)));
            $stmt = $pdo->prepare('UPDATE job_categories SET name = ?, slug = ?, description = ?, status = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$name, $slug, $description, $status, $id]);

            $this->setFlash('success', 'تم تحديث التصنيف بنجاح');
            $this->redirect('/admin/categories');
        }

        $category = $pdo->prepare('SELECT * FROM job_categories WHERE id = ? LIMIT 1');
        $category->execute([$id]);
        $item = $category->fetch(PDO::FETCH_ASSOC);

        $this->view('admin.categories.edit', [
            'title' => 'تعديل التصنيف',
            'category' => $item,
        ]);
    }

    public function delete(int $id)
    {
        $pdo = $this->db();
        $pdo->prepare('DELETE FROM job_categories WHERE id = ?')->execute([$id]);
        $this->setFlash('success', 'تم حذف التصنيف بنجاح');
        $this->redirect('/admin/categories');
    }
}

