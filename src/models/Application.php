<?php

class Application extends Model
{
    protected string $table = 'job_applications';
    protected string $primaryKey = 'id';

    public function byUser(int $userId): array
    {
        return $this->where('user_id', $userId, ['*']);
    }

    public function byJob(int $jobId): array
    {
        return $this->where('job_id', $jobId, ['*']);
    }

    public function byStatus(string $status): array
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE status = ? ORDER BY applied_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

