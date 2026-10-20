<?php
class SearchController extends Controller
{
    public function index()
    {
        $keyword = trim((string) $this->input('q', ''));
        $category = (int) $this->input('category', 0);
        $governorate = (int) $this->input('governorate', 0);
        $jobType = (string) $this->input('job_type', '');

        $conditions = ["status = 'published'", "company_id IN (SELECT id FROM companies WHERE status = 'active' AND verification_status = 'verified')"];
        $params = [];

        if ($keyword !== '') {
            $conditions[] = '(title LIKE ? OR description LIKE ? OR requirements LIKE ?)';
            $like = '%' . $keyword . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($category > 0) {
            $conditions[] = 'category_id = ?';
            $params[] = $category;
        }

        if ($governorate > 0) {
            $conditions[] = 'governorate_id = ?';
            $params[] = $governorate;
        }

        if ($jobType !== '') {
            $conditions[] = 'job_type = ?';
            $params[] = $jobType;
        }

        $sql = 'SELECT * FROM jobs WHERE ' . implode(' AND ', $conditions) . ' ORDER BY created_at DESC';

        try {
            $stmt = $this->db()->prepare($sql);
            $stmt->execute($params);
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $jobs = [];
        }

        $this->view('jobs.search', [
            'title' => 'البحث عن الوظائف',
            'keyword' => $keyword,
            'jobs' => $jobs,
        ]);
    }
}

