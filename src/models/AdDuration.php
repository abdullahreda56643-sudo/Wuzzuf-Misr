<?php

class AdDuration extends Model
{
    protected string $table = 'ad_durations';
    protected string $primaryKey = 'id';

    public function active(array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . " WHERE is_active = 1 ORDER BY sort_order ASC, days ASC";
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByDays(int $days, array $columns = ['*']): ?array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE days = ? AND is_active = 1 LIMIT 1';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$days]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }
}

