<?php

class PageController extends Controller
{
    public function index()
    {
        $pdo = $this->db();
        $stmt = $pdo->query('SELECT * FROM pages ORDER BY created_at DESC');
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.pages.index', [
            'title' => 'إدارة الصفحات',
            'pages' => $pages,
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $title = trim((string) $this->input('title', ''));
            $slug = trim((string) $this->input('slug', ''));
            $content = trim((string) $this->input('content', ''));
            $status = trim((string) $this->input('status', 'published'));

            if ($title === '' || $content === '') {
                $this->setFlash('error', 'يرجى تعبئة العنوان والمحتوى');
                $this->redirect('/admin/pages/create');
            }

            $slug = $slug !== '' ? $slug : strtolower(str_replace(' ', '-', $title));
            $pdo = $this->db();
            $stmt = $pdo->prepare('INSERT INTO pages (title, slug, content, status, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$title, $slug, $content, $status]);

            $this->setFlash('success', 'تم إضافة الصفحة بنجاح');
            $this->redirect('/admin/pages');
        }

        $this->view('admin.pages.create', [
            'title' => 'إضافة صفحة',
        ]);
    }

    public function edit(int $id)
    {
        $pdo = $this->db();

        if ($this->isPost()) {
            $title = trim((string) $this->input('title', ''));
            $slug = trim((string) $this->input('slug', ''));
            $content = trim((string) $this->input('content', ''));
            $status = trim((string) $this->input('status', 'published'));

            if ($title === '' || $content === '') {
                $this->setFlash('error', 'يرجى تعبئة العنوان والمحتوى');
                $this->redirect('/admin/pages/' . $id . '/edit');
            }

            $slug = $slug !== '' ? $slug : strtolower(str_replace(' ', '-', $title));
            $stmt = $pdo->prepare('UPDATE pages SET title = ?, slug = ?, content = ?, status = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$title, $slug, $content, $status, $id]);

            $this->setFlash('success', 'تم تحديث الصفحة بنجاح');
            $this->redirect('/admin/pages');
        }

        $stmt = $pdo->prepare('SELECT * FROM pages WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('admin.pages.edit', [
            'title' => 'تعديل الصفحة',
            'page' => $page,
        ]);
    }

    public function delete(int $id)
    {
        $this->db()->prepare('DELETE FROM pages WHERE id = ?')->execute([$id]);
        $this->setFlash('success', 'تم حذف الصفحة بنجاح');
        $this->redirect('/admin/pages');
    }
}

