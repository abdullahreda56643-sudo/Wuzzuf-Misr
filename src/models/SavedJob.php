<?php

class SavedJob extends Model
{
    protected string $table = 'saved_jobs';
    protected string $primaryKey = 'id';

    public function byUser(int $userId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE user_id = ? ORDER BY saved_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byJob(int $jobId): ?array
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE job_id = ? LIMIT 1';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$jobId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    public function exists(int $userId, int $jobId): bool
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE user_id = ? AND job_id = ?';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$userId, $jobId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}

