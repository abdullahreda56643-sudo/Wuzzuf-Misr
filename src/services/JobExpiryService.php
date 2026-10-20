<?php

class JobExpiryService
{
    protected function db(): PDO
    {
        return Database::connection();
    }

    public function expireJobs(): int
    {
        try {
            $defaultDays = (int) config('default_job_duration', 30);

            $sql = "
                UPDATE jobs
                SET status = 'expired', updated_at = NOW()
                WHERE status = 'published'
                  AND (
                    (expires_at IS NOT NULL AND expires_at <= NOW())
                    OR (expires_at IS NULL AND published_at IS NOT NULL AND DATE_ADD(published_at, INTERVAL :days DAY) <= NOW())
                  )
            ";

            $stmt = $this->db()->prepare($sql);
            $stmt->bindValue(':days', $defaultDays, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function getExpiredJobs(): array
    {
        try {
            $stmt = $this->db()->query("SELECT * FROM jobs WHERE status = 'expired' ORDER BY updated_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            return [];
        }
    }
}
