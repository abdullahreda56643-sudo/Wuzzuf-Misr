<?php

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        if (strtolower($value) === 'true') {
            return true;
        }

        if (strtolower($value) === 'false') {
            return false;
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        return $value;
    }
}

if (!function_exists('dd')) {
    function dd($value): void
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
        exit;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        if ($path === '' || $path === '#') {
            header('Location: ' . config('site_url', '/'));
            exit;
        }

        if (preg_match('/^(https?:)?\/\//i', $path)) {
            header('Location: ' . $path);
            exit;
        }

        $base = rtrim(config('site_url', '/'), '/');
        header('Location: ' . $base . '/' . ltrim($path, '/'));
        exit;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $base = rtrim(config('site_url', '/'), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path): string
    {
        $base = rtrim(config('site_url', '/'), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        static $settings = null;
        static $fallback = null;

        if ($fallback === null) {
            $fallback = require dirname(__DIR__) . '/config/config.php';
        }

        if ($settings === null) {
            try {
                $dbConfig = require dirname(__DIR__) . '/config/database.php';
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $dbConfig['host'],
                    $dbConfig['port'],
                    $dbConfig['database'],
                    $dbConfig['charset']
                );

                $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options'] ?? []);
                $stmt = $pdo->query("SELECT `key`, `value`, `type` FROM settings");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $settings = [];
                foreach ($rows as $row) {
                    $value = $row['value'];
                    switch ($row['type']) {
                        case 'boolean':
                            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                            break;
                        case 'number':
                            $value = is_numeric($value) ? (int) $value : $value;
                            break;
                        case 'json':
                            $value = json_decode($value, true);
                            break;
                    }
                    $settings[$row['key']] = $value;
                }
            } catch (Throwable $e) {
                $settings = [];
            }
        }

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        if (array_key_exists($key, $fallback)) {
            return $fallback[$key];
        }

        return $default;
    }
}

// ==================== دوال الإعلانات ====================

if (!function_exists('isValidAdCode')) {
    function isValidAdCode(string $code): bool
    {
        $code = trim($code);

        if ($code === '') {
            return false;
        }

        $lower = strtolower($code);
        $validMarkers = [
            'adsbygoogle',
            'googlesyndication',
            'data-ad-client',
            'data-ad-slot',
            'google_ad',
            '<script',
            '<ins',
            '<iframe',
            'ca-pub-',
        ];

        foreach ($validMarkers as $marker) {
            if (strpos($lower, $marker) !== false) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('logInvalidAdCode')) {
    function logInvalidAdCode(string $slot = 'unknown', string $context = 'renderAdMarkup'): void
    {
        try {
            $logDir = defined('ROOT_PATH') ? ROOT_PATH . '/storage/logs' : __DIR__ . '/../../storage/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0775, true);
            }

            $line = '[' . date('Y-m-d H:i:s') . '] Invalid ad rejected | slot=' . $slot . ' | context=' . $context . PHP_EOL;
            @file_put_contents($logDir . '/ads.log', $line, FILE_APPEND | LOCK_EX);
        } catch (Throwable $e) {
            error_log('Invalid ad rejected: ' . $slot . ' | ' . $context);
        }
    }
}

if (!function_exists('normalizeAdCode')) {
    function normalizeAdCode(string $code): string
    {
        $code = trim(html_entity_decode($code, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if (preg_match('/^(?:script|ins)\b/i', $code)) {
            $code = '<' . $code;
        }

        return $code;
    }
}

if (!function_exists('renderAdMarkup')) {
    function renderAdMarkup($ad, string $fallback = '', string $slot = 'unknown'): string
    {
        $code = '';

        // محاولة قراءة الكود من الإعلان فقط (لا نستخدم الفالباك)
        if (!empty($ad) && is_array($ad)) {
            $code = normalizeAdCode((string) ($ad['ad_code'] ?? $ad['code'] ?? ''));
        }

        // إذا كان الكود فارغاً، لا نعرض شيئاً (نلغي الإعلانات التجريبية)
        if ($code === '') {
            return '';
        }

        // التحقق من صحة الكود
        if (!isValidAdCode($code)) {
            logInvalidAdCode($slot, 'renderAdMarkup');
            return '';
        }

        // إرجاع الكود مع الحاوية
        return '<div class="ad-slot-wrap" style="max-width:100%; width:100%; overflow-x:hidden !important; overflow-y:hidden !important; display:block;">' . $code . '</div>';
    }
}

// دالة مساعدة للتوافق مع الاسم القديم
if (!function_exists('renderHomeAd')) {
    function renderHomeAd($ad, string $fallback = '', string $slot = 'unknown'): string
    {
        return renderAdMarkup($ad, '', $slot);
    }
}

// ==================== دوال تنسيق البيانات ====================

if (!function_exists('formatJobType')) {
    function formatJobType(string $value): string
    {
        $map = [
            'full-time' => 'دوام كامل',
            'part-time' => 'دوام جزئي',
            'contract' => 'عقد',
            'temporary' => 'مؤقت',
            'internship' => 'تدريب',
            'full_time' => 'دوام كامل',
            'part_time' => 'دوام جزئي',
            'remote' => 'عن بعد',
        ];

        return $map[$value] ?? ucfirst((string) $value);
    }
}

if (!function_exists('formatSalaryRange')) {
    function formatSalaryRange($salaryMin, $salaryMax, $currency = 'EGP', $negotiable = 0): string
    {
        if (!empty($negotiable)) {
            return 'قابل للتفاوض';
        }

        $currencyName = strtoupper((string) $currency);

        if ($salaryMin !== null && $salaryMax !== null && $salaryMin > 0 && $salaryMax > 0) {
            return number_format((float) $salaryMin, 0, '.', ',') . ' - ' . number_format((float) $salaryMax, 0, '.', ',') . ' ' . $currencyName;
        }

        if ($salaryMin !== null && $salaryMin > 0) {
            return 'من ' . number_format((float) $salaryMin, 0, '.', ',') . ' ' . $currencyName;
        }

        if ($salaryMax !== null && $salaryMax > 0) {
            return 'حتى ' . number_format((float) $salaryMax, 0, '.', ',') . ' ' . $currencyName;
        }

        return 'راتب قابل للتفاوض';
    }
}

if (!function_exists('formatTimeAgo')) {
    function formatTimeAgo(string $date): string
    {
        if ($date === '') {
            return 'حديثاً';
        }

        try {
            $time = new DateTime($date);
            $diff = $time->diff(new DateTime());

            if ($diff->y > 0) {
                return 'منذ ' . $diff->y . ' سنة';
            }

            if ($diff->m > 0) {
                return 'منذ ' . $diff->m . ' شهر';
            }

            if ($diff->d > 0) {
                return 'منذ ' . $diff->d . ' يوم';
            }

            if ($diff->h > 0) {
                return 'منذ ' . $diff->h . ' ساعة';
            }

            if ($diff->i > 0) {
                return 'منذ ' . $diff->i . ' دقيقة';
            }

            return 'منذ لحظات';
        } catch (Throwable $e) {
            return 'حديثاً';
        }
    }
}

if (!function_exists('companyInitials')) {
    function companyInitials(string $name): string
    {
        $clean = trim($name);
        if ($clean === '') {
            return 'J';
        }

        $parts = preg_split('/\s+/', $clean);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        }

        return strtoupper(substr($clean, 0, 2));
    }
}

if (!function_exists('current_user')) {
    function current_user()
    {
        return Session::get('user');
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, string $currency = 'EGP'): string
    {
        if ($amount === null || $amount === '') {
            return '0 ' . $currency;
        }
        
        return number_format((float) $amount, 0, '.', ',') . ' ' . strtoupper($currency);
    }
}

if (!function_exists('truncateText')) {
    function truncateText(string $text, int $limit = 100, string $suffix = '...'): string
    {
        $text = trim($text);
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        
        return mb_substr($text, 0, $limit) . $suffix;
    }
}

if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map('sanitizeInput', $input);
        }
        
        return htmlspecialchars(trim((string) $input), ENT_QUOTES, 'UTF-8');
    }
}