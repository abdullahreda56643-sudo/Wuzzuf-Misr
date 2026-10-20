<?php

class Report extends Model
{
    protected string $table = 'reports';
    protected string $primaryKey = 'id';

    public function byReporter(int $reporterId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE reporter_id = ? ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$reporterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byStatus(string $status, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE status = ? ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function forType(string $type, int $id, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE reported_type = ? AND reported_id = ? ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$type, $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

