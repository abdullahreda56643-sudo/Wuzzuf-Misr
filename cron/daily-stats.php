<?php

require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/core/Helpers.php';
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/../src/core/Model.php';
require_once __DIR__ . '/../src/libraries/Cron.php';

try {
    $cron = new Cron();
    $result = $cron->runDailyStats();

    if (($result['status'] ?? 'error') === 'ok') {
        echo "[DAILY_STATS] ok\n";
        echo json_encode($result['stats'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "[DAILY_STATS] error: " . ($result['message'] ?? 'unknown error') . "\n";
        exit(1);
    }
} catch (Throwable $e) {
    echo "[DAILY_STATS] error: " . $e->getMessage() . "\n";
    exit(1);
}
