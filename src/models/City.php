<?php

class City extends Model
{
    protected string $table = 'cities';
    protected string $primaryKey = 'id';

    public function byGovernorate(int $governorateId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE governorate_id = ? AND status = "active" ORDER BY name ASC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$governorateId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

