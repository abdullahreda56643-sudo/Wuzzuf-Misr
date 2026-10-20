<?php

class CompanyRating extends Model
{
    protected string $table = 'company_ratings';
    protected string $primaryKey = 'id';

    public function averageByCompany(int $companyId): float
    {
        $stmt = $this->db()->prepare('SELECT ROUND(COALESCE(AVG(rating), 0), 1) FROM company_ratings WHERE company_id = ?');
        $stmt->execute([$companyId]);

        return (float) ($stmt->fetchColumn() ?: 0);
    }

    public function countByCompany(int $companyId): int
    {
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM company_ratings WHERE company_id = ?');
        $stmt->execute([$companyId]);

        return (int) $stmt->fetchColumn();
    }

    public function latestByCompany(int $companyId, int $limit = 5): array
    {
        $stmt = $this->db()->prepare(
            'SELECT cr.*, u.first_name, u.last_name
             FROM company_ratings cr
             LEFT JOIN users u ON u.id = cr.user_id
             WHERE cr.company_id = ?
             ORDER BY cr.created_at DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $companyId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ratingForUser(int $companyId, int $userId): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM company_ratings WHERE company_id = ? AND user_id = ? LIMIT 1'
        );
        $stmt->execute([$companyId, $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    public function upsert(int $companyId, int $userId, int $rating, string $review = ''): bool
    {
        $normalizedRating = max(1, min(5, (int) $rating));
        $cleanReview = trim($review);

        $existing = $this->ratingForUser($companyId, $userId);
        if ($existing) {
            $stmt = $this->db()->prepare(
                'UPDATE company_ratings
                 SET rating = ?, review = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE id = ?'
            );

            return $stmt->execute([$normalizedRating, $cleanReview, (int) $existing['id']]);
        }

        $stmt = $this->db()->prepare(
            'INSERT INTO company_ratings (company_id, user_id, rating, review, created_at, updated_at)
             VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)'
        );

        return $stmt->execute([$companyId, $userId, $normalizedRating, $cleanReview]);
    }
}
