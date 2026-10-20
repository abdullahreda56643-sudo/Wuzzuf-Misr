<?php

class Cron
{
    protected function db(): PDO
    {
        return Database::connection();
    }

    public function runAdExpiry(): array
    {
        $service = new AdService();
        $expired = $service->expireAds();

        return [
            'name' => 'ad_expiry',
            'expired_count' => $expired,
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    public function runJobExpiry(): array
    {
        $service = new JobExpiryService();
        $expired = $service->expireJobs();

        return [
            'name' => 'job_expiry',
            'expired_count' => $expired,
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    public function runDailyStats(): array
    {
        $stats = [
            'users' => 0,
            'companies' => 0,
            'jobs' => 0,
            'active_jobs' => 0,
            'published_jobs' => 0,
            'expired_jobs' => 0,
            'active_ads' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $stats['users'] = (int) $this->db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
            $stats['companies'] = (int) $this->db()->query('SELECT COUNT(*) FROM companies')->fetchColumn();
            $stats['jobs'] = (int) $this->db()->query('SELECT COUNT(*) FROM jobs')->fetchColumn();
            $stats['active_jobs'] = (int) $this->db()->query("SELECT COUNT(*) FROM jobs WHERE status = 'published'")->fetchColumn();
            $stats['published_jobs'] = (int) $this->db()->query("SELECT COUNT(*) FROM jobs WHERE status = 'published'")->fetchColumn();
            $stats['expired_jobs'] = (int) $this->db()->query("SELECT COUNT(*) FROM jobs WHERE status = 'expired'")->fetchColumn();
            $stats['active_ads'] = (int) $this->db()->query("SELECT COUNT(*) FROM ads WHERE status = 'active'")->fetchColumn();
        } catch (Throwable $e) {
            return [
                'name' => 'daily_stats',
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s'),
            ];
        }

        return [
            'name' => 'daily_stats',
            'status' => 'ok',
            'stats' => $stats,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }
}
