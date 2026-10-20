<?php

class Company extends Model
{
    protected string $table = 'companies';
    protected string $primaryKey = 'id';

    public function findByUserId(int $userId): ?array
    {
        return $this->first(['user_id' => $userId]);
    }

    public function featured(array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE status = "active" AND is_featured = 1 ORDER BY created_at DESC';
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byGovernorate(int $governorateId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE governorate_id = ? AND status = "active" ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$governorateId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

