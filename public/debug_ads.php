<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/core/Database.php';

header('Content-Type: text/html; charset=utf-8');

function adCheckSummary(string $code): array
{
    $clean = trim((string) $code);
    $hasAdsbygoogle = stripos($clean, 'adsbygoogle') !== false;
    $hasGoogleAd = stripos($clean, 'google_ad') !== false;
    $hasDataClient = stripos($clean, 'data-ad-client') !== false;
    $hasInsTag = stripos($clean, '<ins') !== false;
    $hasPush = stripos($clean, 'push({})') !== false;
    $hasScript = stripos($clean, '<script') !== false;

    return [
        'empty' => $clean === '',
        'has_adsbygoogle' => $hasAdsbygoogle,
        'has_google_ad' => $hasGoogleAd,
        'has_data_ad_client' => $hasDataClient,
        'has_ins_tag' => $hasInsTag,
        'has_push' => $hasPush,
        'has_script' => $hasScript,
        'valid_code' => $clean !== '' && ($hasAdsbygoogle || $hasGoogleAd || $hasDataClient),
    ];
}

try {
    $pdo = Database::connection();
    $expectedSlugs = ['header-home', 'middle-jobs', 'sidebar', 'footer', 'company-header'];

    echo '<h2>Ad Full Debug</h2>';
    echo '<pre>';

    echo "\n=== 1) DATABASE CONNECTION TEST ===\n";
    echo "DB connected successfully\n";

    echo "\n=== 2) AD LOCATIONS ===\n";
    $locs = $pdo->query("SELECT id, name, slug, is_active, sort_order FROM ad_locations ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
    if (!$locs) {
        echo "No ad_locations rows found\n";
    } else {
        print_r($locs);
    }

    echo "\n=== 3) AD DURATIONS ===\n";
    $durations = $pdo->query("SELECT id, name, days, is_active FROM ad_durations ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
    if (!$durations) {
        echo "No ad_durations rows found\n";
    } else {
        print_r($durations);
    }

    echo "\n=== 4) ALL ADS TABLE ROWS ===\n";
    $ads = $pdo->query("SELECT id, user_id, title, ad_code, ad_location_id, ad_duration_id, ad_type, status, start_date, end_date, created_at FROM ads ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    if (!$ads) {
        echo "No ads rows found\n";
    } else {
        print_r($ads);
    }

    echo "\n=== 5) CHECK EVERY AD AGAINST DISPLAY RULES ===\n";
    foreach ($ads as $ad) {
        $id = (int) ($ad['id'] ?? 0);
        $status = (string) ($ad['status'] ?? '');
        $title = trim((string) ($ad['title'] ?? ''));
        $code = (string) ($ad['ad_code'] ?? '');
        $locationId = (int) ($ad['ad_location_id'] ?? 0);
        $start = $ad['start_date'] ?? null;
        $end = $ad['end_date'] ?? null;
        $locationName = $pdo->prepare('SELECT slug, name FROM ad_locations WHERE id = ? LIMIT 1');
        $locationName->execute([$locationId]);
        $location = $locationName->fetch(PDO::FETCH_ASSOC);
        $check = adCheckSummary($code);

        echo "Ad #$id\n";
        echo "- title: " . ($title !== '' ? $title : '[EMPTY]') . "\n";
        echo "- ad_location_id: $locationId\n";
        echo "- location: " . (($location['name'] ?? 'UNKNOWN') . ' (' . ($location['slug'] ?? 'unknown') . ')') . "\n";
        echo "- status: $status\n";
        echo "- start_date: " . ($start ?? 'NULL') . "\n";
        echo "- end_date: " . ($end ?? 'NULL') . "\n";
        echo "- valid code: " . ($check['valid_code'] ? 'YES' : 'NO') . "\n";
        echo "- code contains adsbygoogle: " . ($check['has_adsbygoogle'] ? 'YES' : 'NO') . "\n";
        echo "- code contains data-ad-client: " . ($check['has_data_ad_client'] ? 'YES' : 'NO') . "\n";
        echo "- code contains <ins>: " . ($check['has_ins_tag'] ? 'YES' : 'NO') . "\n";
        echo "- code contains push({}): " . ($check['has_push'] ? 'YES' : 'NO') . "\n";
        echo "- code preview: " . substr(strip_tags($code), 0, 200) . "\n\n";
    }

    echo "\n=== 6) ACTIVE MATCH CHECK FOR EACH PAGE SLOT ===\n";
    foreach ($expectedSlugs as $slug) {
        $locationRow = $pdo->prepare('SELECT id, name, slug FROM ad_locations WHERE slug = ? LIMIT 1');
        $locationRow->execute([$slug]);
        $location = $locationRow->fetch(PDO::FETCH_ASSOC);

        echo "Slot: $slug\n";
        if (!$location) {
            echo "- NOT FOUND in ad_locations table\n\n";
            continue;
        }

        $locationId = (int) ($location['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT id, title, status, start_date, end_date, ad_code FROM ads WHERE ad_location_id = ? ORDER BY created_at DESC');
        $stmt->execute([$locationId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "- matching ads count: " . count($rows) . "\n";

        foreach ($rows as $row) {
            $status = (string) ($row['status'] ?? '');
            $code = (string) ($row['ad_code'] ?? '');
            $start = $row['start_date'] ?? null;
            $end = $row['end_date'] ?? null;
            $valid = adCheckSummary($code)['valid_code'];
            $isActive = ($status === 'active' && $start <= date('Y-m-d H:i:s') && $end > date('Y-m-d H:i:s'));

            echo "-- Ad #" . ($row['id'] ?? 0) . ": status=" . $status . ', active=' . (($isActive) ? 'YES' : 'NO') . ', valid_code=' . (($valid) ? 'YES' : 'NO') . "\n";
        }
        echo "\n";
    }

    echo "\n=== 7) DETAILED REASON SUMMARY ===\n";
    echo "Possible reasons for no ad display:\n";
    echo "1) No ad row exists in ads table\n";
    echo "2) status is not 'active'\n";
    echo "3) start_date is in the future or end_date has passed\n";
    echo "4) ad_location_id does not match the slot slug used on the public page\n";
    echo "5) ad_code is empty or does not contain adsbygoogle / data-ad-client\n";
    echo "6) page controller doesn't request that slot\n";
    echo "7) AdSense account is not approved or the slot is invalid\n";
    echo "8) the page uses a different slug than the selected ad location\n\n";

    echo "\n=== 8) IMPORTANT FRONTEND MAPPING ===\n";
    echo "Home page uses slots: header-home, middle-jobs, sidebar, footer\n";
    echo "Company page uses slots: company-header, middle-jobs\n";
    echo "Jobs page uses slots: middle-jobs, company-header\n";
    echo "If the selected ad belongs to a different slot, it will not display on that page\n";

    echo "\n=== 9) FRONTEND RENDER CHECK (PUBLIC PAGE LAYER) ===\n";
    $allAdRows = $pdo->query("SELECT id, title, status, ad_location_id, ad_code, start_date, end_date FROM ads ORDER BY ad_location_id, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $adRowsByLocation = [];
    foreach ($allAdRows as $row) {
        $locId = (int) ($row['ad_location_id'] ?? 0);
        $adRowsByLocation[$locId][] = $row;
    }

    $allLocations = $pdo->query("SELECT id, slug, name FROM ad_locations WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
    $locationBySlug = [];
    foreach ($allLocations as $loc) {
        $locationBySlug[(string) ($loc['slug'] ?? '')] = $loc;
    }

    $pagesToCheck = [
        'home' => ['header-home', 'middle-jobs', 'sidebar', 'footer'],
        'jobs_index' => ['middle-jobs'],
        'jobs_show' => ['middle-jobs'],
        'companies_index' => ['company-header'],
        'companies_show' => ['company-header'],
        'posts_index' => ['middle-jobs'],
        'about' => ['middle-jobs'],
        'contact' => ['middle-jobs'],
    ];

    foreach ($pagesToCheck as $pageName => $slots) {
        echo "Page: $pageName\n";

        foreach ($slots as $slotSlug) {
            $location = $locationBySlug[$slotSlug] ?? null;
            if (!$location) {
                echo "- slot $slotSlug: NOT FOUND in ad_locations\n";
                continue;
            }

            $locationId = (int) ($location['id'] ?? 0);
            $slotRows = $adRowsByLocation[$locationId] ?? [];
            $selectedRow = null;

            foreach ($slotRows as $row) {
                $status = strtolower((string) ($row['status'] ?? ''));
                $code = (string) ($row['ad_code'] ?? '');
                $start = $row['start_date'] ?? null;
                $end = $row['end_date'] ?? null;
                $valid = trim($code) !== '' && (stripos($code, 'adsbygoogle') !== false || stripos($code, 'googlesyndication') !== false || stripos($code, 'data-ad-client') !== false || stripos($code, '<script') !== false || stripos($code, '<ins') !== false || stripos($code, '<iframe') !== false);
                $isActive = $status === 'active' && $start && $end && $start <= date('Y-m-d H:i:s') && $end > date('Y-m-d H:i:s');

                if ($isActive && $valid) {
                    $selectedRow = $row;
                    break;
                }
            }

            if ($selectedRow) {
                echo "- slot $slotSlug: WOULD_RENDER = YES | ad_id=" . ($selectedRow['id'] ?? 0) . " | valid_code=YES\n";
            } else {
                echo "- slot $slotSlug: WOULD_RENDER = NO | reason=matching active ad not found or code invalid\n";
            }
        }

        echo "\n";
    }

    echo "\n=== 10) PUBLIC PAGE ROOT CAUSE CHECK ===\n";
    echo "If the DB check is YES but the frontend render check is NO for a page slot, the problem is in the public page layer (wrong slug, wrong slot mapping, or invalid code rejected by render validation).\n";

    echo '</pre>';
} catch (Throwable $e) {
    echo '<h2>Ad Debug Error</h2>';
    echo '<pre>';
    echo $e->getMessage();
    echo "\n\n";
    echo $e->getTraceAsString();
    echo '</pre>';
}
