<?php

require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/core/Helpers.php';
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/../src/core/Model.php';
require_once __DIR__ . '/../src/services/JobExpiryService.php';

try {
    $service = new JobExpiryService();
    $expired = $service->expireJobs();

    echo "[JOB_EXPIRY] expired={$expired}\n";
} catch (Throwable $e) {
    echo "[JOB_EXPIRY] error: " . $e->getMessage() . "\n";
    exit(1);
}
