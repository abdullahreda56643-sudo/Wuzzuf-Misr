<?php

class AdLocation extends Model
{
    protected string $table = 'ad_locations';
    protected string $primaryKey = 'id';

    public function active(array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . " WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBySlug(string $slug, array $columns = ['*']): ?array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE slug = ? AND is_active = 1 LIMIT 1';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }
}

