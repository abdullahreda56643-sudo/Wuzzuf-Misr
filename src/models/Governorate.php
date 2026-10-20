<?php

class Governorate extends Model
{
    protected string $table = 'governorates';
    protected string $primaryKey = 'id';

    public function active(array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . " WHERE status = 'active' ORDER BY name ASC";
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

