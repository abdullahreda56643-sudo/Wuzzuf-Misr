<?php
class PageController extends Controller
{
    public function posts()
    {
        try {
            $stmt = $this->db()->query("SELECT * FROM posts WHERE status = 'published' ORDER BY published_at DESC, created_at DESC LIMIT 12");
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $posts = [];
        }

        $adSlots = $this->loadAdSlots([
            'header-home',
            'middle-jobs',
            'sidebar',
            'footer',
            'company-header',
        ]);

        $this->view('posts.index', [
            'title' => 'نصائح مهنية',
            'posts' => $posts,
            'homeAds' => $adSlots,
        ]);
    }

    public function show(string $slug)
    {
        try {
            $stmt = $this->db()->prepare('SELECT * FROM pages WHERE slug = ? AND status = "published" LIMIT 1');
            $stmt->execute([$slug]);
            $page = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $page = null;
        }

        if (!$page) {
            $this->setFlash('error', 'الصفحة غير موجودة');
            $this->redirect('/');
        }

        $this->view('pages.show', [
            'title' => $page['title'] ?? 'الصفحة',
            'page' => $page,
        ]);
    }
}

