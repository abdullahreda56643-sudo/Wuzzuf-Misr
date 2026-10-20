<?php

class Ad extends Model
{
    protected string $table = 'ads';
    protected string $primaryKey = 'id';

    public function active(array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . " WHERE status = 'active' ORDER BY created_at DESC";
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byLocation(int $locationId): array
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ad_location_id = ? AND status = "active" ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$locationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

