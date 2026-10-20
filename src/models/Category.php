<?php

class Category extends Model
{
    protected string $table = 'job_categories';
    protected string $primaryKey = 'id';

    public function active(array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . " WHERE status = 'active' ORDER BY name ASC";
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

