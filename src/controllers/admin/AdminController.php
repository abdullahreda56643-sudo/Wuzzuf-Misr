<?php

class AdminController extends Controller
{
    public function index()
    {
        $recentJobs = [];
        $stats = [
            'users' => ['value' => 0, 'trend' => '0%'],
            'companies' => ['value' => 0, 'trend' => '0%'],
            'jobs' => ['value' => 0, 'trend' => '0%'],
            'applications' => ['value' => 0, 'trend' => '0%'],
            'categories' => ['value' => 0, 'trend' => '0%'],
            'pages' => ['value' => 0, 'trend' => '0%'],
        ];

        try {
            $pdo = $this->db();
            $recentJobsStmt = $pdo->query(
                'SELECT j.*, c.name AS company_name
                 FROM jobs j
                 LEFT JOIN companies c ON c.id = j.company_id
                 ORDER BY j.created_at DESC
                 LIMIT 5'
            );
            $recentJobs = $recentJobsStmt->fetchAll(PDO::FETCH_ASSOC);

            $periodStart = new DateTimeImmutable('first day of this month midnight');
            $previousPeriodStart = $periodStart->modify('-1 month');
            $periodStartSql = $periodStart->format('Y-m-d H:i:s');
            $previousPeriodStartSql = $previousPeriodStart->format('Y-m-d H:i:s');

            $metrics = [
                'users' => 'users',
                'companies' => 'companies',
                'jobs' => 'jobs',
                'applications' => 'job_applications',
                'categories' => 'job_categories',
                'pages' => 'pages',
            ];

            foreach ($metrics as $key => $table) {
                $stats[$key]['value'] = (int) $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
                $currentStmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE created_at >= ?");
                $currentStmt->execute([$periodStartSql]);
                $current = (int) $currentStmt->fetchColumn();

                $previousStmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE created_at >= ? AND created_at < ?");
                $previousStmt->execute([$previousPeriodStartSql, $periodStartSql]);
                $previous = (int) $previousStmt->fetchColumn();
                $stats[$key]['trend'] = $this->formatTrend($current, $previous);
            }
        } catch (Throwable $e) {
            foreach ($stats as $key => $stat) {
                $stats[$key] = ['value' => 0, 'trend' => '0%'];
            }
        }

        $this->view('admin.index', [
            'title' => 'لوحة التحكم',
            'stats' => $stats,
            'recentJobs' => $recentJobs,
        ]);
    }

    private function formatTrend(int $current, int $previous): string
    {
        if ($previous === 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $percentage = (($current - $previous) / $previous) * 100;
        $prefix = $percentage > 0 ? '+' : '';

        return $prefix . number_format($percentage, 1) . '%';
    }
}

