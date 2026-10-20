<?php

require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/core/Helpers.php';
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/../src/core/Model.php';
require_once __DIR__ . '/../src/services/AdService.php';

try {
    $service = new AdService();
    $expired = $service->expireAds();
    $activated = $service->activatePendingAds();

    echo "[AD_EXPIRY] expired={$expired} activated={$activated}\n";
} catch (Throwable $e) {
    echo "[AD_EXPIRY] error: " . $e->getMessage() . "\n";
    exit(1);
}
