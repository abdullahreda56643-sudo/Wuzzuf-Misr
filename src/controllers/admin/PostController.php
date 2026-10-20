<?php

class PostController extends Controller
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
            $where[] = '(title LIKE ? OR slug LIKE ? OR category LIKE ? OR content LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $countSql = 'SELECT COUNT(*) FROM posts' . $whereSql;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalPosts = (int) $countStmt->fetchColumn();

        $totalPages = max(1, (int) ceil($totalPosts / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT * FROM posts' . $whereSql . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $pdo->prepare($sql);

        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.posts.index', [
            'title' => 'إدارة المقالات',
            'posts' => $posts,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalPosts' => $totalPosts,
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $title = trim((string) $this->input('title', ''));
            $slug = trim((string) $this->input('slug', ''));
            $content = trim((string) $this->input('content', ''));
            $excerpt = trim((string) $this->input('excerpt', ''));
            $category = trim((string) $this->input('category', ''));
            $status = trim((string) $this->input('status', 'draft'));
            $authorId = (int) (Auth::id() ?: 1);

            if ($title === '' || $content === '') {
                $this->setFlash('error', 'يرجى تعبئة عنوان المقال والمحتوى');
                $this->redirect('/admin/posts/create');
            }

            $slug = $slug !== '' ? $slug : strtolower(preg_replace('/[^a-zA-Z0-9\s-]/u', '', $title));
            $slug = preg_replace('/\s+/', '-', trim((string) $slug));
            $slug = preg_replace('/-+/', '-', trim((string) $slug, '-'));
            $slug = $slug === '' ? 'post-' . time() : $slug;

            $pdo = $this->db();
            $checkStmt = $pdo->prepare('SELECT id FROM posts WHERE slug = ? LIMIT 1');
            $checkStmt->execute([$slug]);

            if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
                $slug = $slug . '-' . time();
            }

            $stmt = $pdo->prepare('INSERT INTO posts (title, slug, content, excerpt, category, author_id, status, published_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, CASE WHEN ? = "published" THEN NOW() ELSE NULL END, NOW())');
            $stmt->execute([$title, $slug, $content, $excerpt, $category, $authorId, $status, $status]);

            $this->setFlash('success', 'تم إضافة المقال بنجاح');
            $this->redirect('/admin/posts');
        }

        $this->view('admin.posts.create', [
            'title' => 'إضافة مقال',
        ]);
    }

    public function edit(int $id)
    {
        $pdo = $this->db();

        if ($this->isPost()) {
            $title = trim((string) $this->input('title', ''));
            $slug = trim((string) $this->input('slug', ''));
            $content = trim((string) $this->input('content', ''));
            $excerpt = trim((string) $this->input('excerpt', ''));
            $category = trim((string) $this->input('category', ''));
            $status = trim((string) $this->input('status', 'draft'));

            if ($title === '' || $content === '') {
                $this->setFlash('error', 'يرجى تعبئة العنوان والمحتوى');
                $this->redirect('/admin/posts/' . $id . '/edit');
            }

            $slug = $slug !== '' ? $slug : strtolower(preg_replace('/[^a-zA-Z0-9\s-]/u', '', $title));
            $slug = preg_replace('/\s+/', '-', trim((string) $slug));
            $slug = preg_replace('/-+/', '-', trim((string) $slug, '-'));
            $slug = $slug === '' ? 'post-' . time() : $slug;

            $stmt = $pdo->prepare('UPDATE posts SET title = ?, slug = ?, content = ?, excerpt = ?, category = ?, status = ?, published_at = CASE WHEN ? = "published" THEN COALESCE(published_at, NOW()) ELSE NULL END, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$title, $slug, $content, $excerpt, $category, $status, $status, $id]);

            $this->setFlash('success', 'تم تحديث المقال بنجاح');
            $this->redirect('/admin/posts');
        }

        $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('admin.posts.edit', [
            'title' => 'تعديل المقال',
            'post' => $post,
        ]);
    }

    public function delete(int $id)
    {
        $this->db()->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
        $this->setFlash('success', 'تم حذف المقال بنجاح');
        $this->redirect('/admin/posts');
    }
}

