<?php

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';

    public function findByEmail(string $email): ?array
    {
        return $this->first(['email' => $email]);
    }

    public function findByType(string $type, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE type = ? ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveUsers(): array
    {
        return $this->where('status', 'active');
    }
}

