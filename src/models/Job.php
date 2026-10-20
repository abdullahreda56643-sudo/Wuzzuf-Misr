<?php

class Job extends Model
{
    protected string $table = 'jobs';
    protected string $primaryKey = 'id';

    public function published(array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . " WHERE status = 'published' ORDER BY created_at DESC";
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byCompany(int $companyId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE company_id = ? ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byCategory(int $categoryId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE category_id = ? AND status = "published" ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search(string $keyword = '', int $categoryId = 0, int $governorateId = 0, string $jobType = ''): array
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE status = "published"';
        $params = [];

        if ($keyword !== '') {
            $sql .= ' AND (title LIKE ? OR description LIKE ? OR requirements LIKE ?)';
            $like = '%' . $keyword . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($categoryId > 0) {
            $sql .= ' AND category_id = ?';
            $params[] = $categoryId;
        }

        if ($governorateId > 0) {
            $sql .= ' AND governorate_id = ?';
            $params[] = $governorateId;
        }

        if ($jobType !== '') {
            $sql .= ' AND job_type = ?';
            $params[] = $jobType;
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

