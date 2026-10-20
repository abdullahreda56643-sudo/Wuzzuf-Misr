<?php

class Notification extends Model
{
    protected string $table = 'notifications';
    protected string $primaryKey = 'id';

    public function byUser(int $userId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE user_id = ? ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function unreadByUser(int $userId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAllRead(int $userId): bool
    {
        $sql = 'UPDATE ' . $this->table . ' SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0';
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute([$userId]);
    }
}

