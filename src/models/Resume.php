<?php

class Resume extends Model
{
    protected string $table = 'resumes';
    protected string $primaryKey = 'id';

    public function byUser(int $userId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE user_id = ? ORDER BY is_default DESC, created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function defaultResume(int $userId): ?array
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE user_id = ? AND is_default = 1 LIMIT 1';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }
}

