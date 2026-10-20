<?php

class AdImpression extends Model
{
    protected string $table = 'ad_impressions';
    protected string $primaryKey = 'id';

    public function byAd(int $adId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE ad_id = ? ORDER BY viewed_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$adId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byUser(int $userId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE user_id = ? ORDER BY viewed_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

