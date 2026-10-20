<?php

/**
 * Base controller for the application.
 */
class Controller
{
    /**
     * Render a view file from the frontend or admin directories.
     */
    protected function view(string $template, array $data = []): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $template = trim($template, '/');

        $paths = [
            ROOT_PATH . '/src/views/' . str_replace('.', '/', $template) . '.php',
            ROOT_PATH . '/src/views/frontend/' . str_replace('.', '/', $template) . '.php',
            ROOT_PATH . '/src/views/admin/' . str_replace('.', '/', $template) . '.php',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $layoutHeader = ROOT_PATH . '/src/views/layout/header.php';
                $layoutFooter = ROOT_PATH . '/src/views/layout/footer.php';
                $shouldUseLayout = !str_starts_with($template, 'layout.')
                    && !str_starts_with($template, 'admin.')
                    && !str_starts_with($template, 'auth.')
                    && !str_starts_with($template, 'components.');

                if ($shouldUseLayout && file_exists($layoutHeader)) {
                    extract($data, EXTR_SKIP);
                    require $layoutHeader;
                }

                extract($data, EXTR_SKIP);
                require $path;

                if ($shouldUseLayout && file_exists($layoutFooter)) {
                    require $layoutFooter;
                }

                return;
            }
        }

        throw new RuntimeException('View not found: ' . $template);
    }

    /**
     * Convenience alias for view.
     */
    protected function render(string $template, array $data = []): void
    {
        $this->view($template, $data);
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url): void
    {
        if ($url === '' || $url === '#') {
            header('Location: ' . config('site_url', '/'));
            exit;
        }

        if (preg_match('/^(https?:)?\/\//i', $url)) {
            header('Location: ' . $url);
            exit;
        }

        $base = rtrim(config('site_url', '/'), '/');
        $target = $base . '/' . ltrim($url, '/');
        header('Location: ' . $target);
        exit;
    }

    /**
     * Return a PDO connection using the project database config.
     */
    protected function db(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $configFile = ROOT_PATH . '/src/config/database.php';
        if (!file_exists($configFile)) {
            throw new RuntimeException('Database config file not found.');
        }

        $config = require $configFile;
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        $pdo = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            $config['options'] ?? []
        );

        return $pdo;
    }

    /**
     * Read a request value from POST or GET.
     */
    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Returns true when the current request is POST.
     */
    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Store a flash message.
     */
    protected function setFlash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    /**
     * Retrieve and clear a flash message.
     */
    protected function getFlash(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }

    /**
     * Return user from session if logged in.
     */
    protected function currentUser(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return $_SESSION['user'] ?? null;
    }

    /**
     * Load ad slots by slug for public pages.
     */
    protected function loadAdSlots(array $slugs): array
    {
        $slots = [];
        foreach ($slugs as $slug) {
            $slots[$slug] = null;
        }

        try {
            $pdo = $this->db();
            
            // التحقق من وجود جدول ad_locations
            $tableExists = $pdo->query("SHOW TABLES LIKE 'ad_locations'")->rowCount() > 0;
            if (!$tableExists) {
                $this->createAdTables();
            }
            
            $locationRows = $pdo->query("SELECT id, slug FROM ad_locations WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
            
            // إذا لم توجد مواقع، نقوم بإنشائها تلقائياً
            if (empty($locationRows)) {
                $this->ensureAdLocationsExist();
                $locationRows = $pdo->query("SELECT id, slug FROM ad_locations WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
            }
            
            $adService = new AdService();

            foreach ($locationRows as $location) {
                $slug = trim((string) ($location['slug'] ?? ''));
                $locationId = (int) ($location['id'] ?? 0);

                if ($slug !== '' && $locationId > 0 && array_key_exists($slug, $slots)) {
                    // جلب الإعلانات النشطة فقط (بدون إنشاء إعلانات تلقائية)
                    $adsForLocation = $adService->getActiveAdsForLocation($locationId);
                    
                    // ====== DEBUG ======
                    error_log('Ad Debug: Found ' . count($adsForLocation) . ' ads for location ' . $slug);
                    if (!empty($adsForLocation)) {
                        error_log('Ad Debug: Ad code length for ' . $slug . ': ' . strlen($adsForLocation[0]['ad_code'] ?? ''));
                    }
                    // ===================
                    
                    if (!empty($adsForLocation)) {
                        $this->recordAdImpression($adsForLocation[0]);
                        $slots[$slug] = $adsForLocation[0];
                    }
                }
            }
        } catch (Throwable $e) {
            error_log('Error loading ad slots: ' . $e->getMessage());
        }

        return $slots;
    }

    protected function recordAdImpression(array $ad): void
    {
        if (!config('ad_impression_tracking', true)) {
            return;
        }

        $adId = (int) ($ad['id'] ?? 0);
        if ($adId > 0) {
            (new AdService())->incrementViews($adId);
        }
    }

    /**
     * Ensure ad locations exist in the database.
     */
    protected function ensureAdLocationsExist(): void
    {
        try {
            $pdo = $this->db();
            
            $defaultLocations = [
                ['slug' => 'header-home', 'name' => 'أعلى الصفحة الرئيسية', 'sort_order' => 10],
                ['slug' => 'middle-jobs', 'name' => 'وسط صفحة الوظائف', 'sort_order' => 20],
                ['slug' => 'sidebar', 'name' => 'الشريط الجانبي', 'sort_order' => 30],
                ['slug' => 'footer', 'name' => 'أسفل الصفحة', 'sort_order' => 40],
                ['slug' => 'company-header', 'name' => 'رأس صفحة الشركات', 'sort_order' => 50],
            ];
            
            foreach ($defaultLocations as $location) {
                $stmt = $pdo->prepare("SELECT id FROM ad_locations WHERE slug = ? LIMIT 1");
                $stmt->execute([$location['slug']]);
                if (!$stmt->fetchColumn()) {
                    $stmt = $pdo->prepare("INSERT INTO ad_locations (slug, name, is_active, sort_order, created_at) VALUES (?, ?, 1, ?, NOW())");
                    $stmt->execute([$location['slug'], $location['name'], $location['sort_order']]);
                }
            }
        } catch (Throwable $e) {
            error_log('Error ensuring ad locations: ' . $e->getMessage());
        }
    }

    /**
     * Create ad tables if they don't exist.
     */
    protected function createAdTables(): void
    {
        try {
            $pdo = $this->db();
            
            // Create ad_locations table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `ad_locations` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `slug` varchar(100) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    `sort_order` int(11) DEFAULT 0,
                    `created_at` datetime DEFAULT NULL,
                    `updated_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `slug` (`slug`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Create ad_durations table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `ad_durations` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `days` int(11) NOT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    `sort_order` int(11) DEFAULT 0,
                    `created_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Create ads table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `ads` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `user_id` int(11) NOT NULL,
                    `title` varchar(255) NOT NULL,
                    `ad_code` text NOT NULL,
                    `description` text DEFAULT NULL,
                    `ad_location_id` int(11) DEFAULT NULL,
                    `ad_duration_id` int(11) DEFAULT NULL,
                    `ad_type` varchar(50) DEFAULT 'banner',
                    `start_date` datetime NOT NULL,
                    `end_date` datetime NOT NULL,
                    `status` enum('active','draft','pending','expired','rejected') DEFAULT 'draft',
                    `views_count` int(11) DEFAULT 0,
                    `clicks_count` int(11) DEFAULT 0,
                    `created_at` datetime DEFAULT NULL,
                    `updated_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `ad_location_id` (`ad_location_id`),
                    KEY `ad_duration_id` (`ad_duration_id`),
                    FOREIGN KEY (`ad_location_id`) REFERENCES `ad_locations` (`id`) ON DELETE SET NULL,
                    FOREIGN KEY (`ad_duration_id`) REFERENCES `ad_durations` (`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Insert default locations
            $this->ensureAdLocationsExist();
            
            // Insert default durations
            $defaultDurations = [
                ['name' => '7 أيام', 'days' => 7, 'sort_order' => 10],
                ['name' => '15 يوم', 'days' => 15, 'sort_order' => 20],
                ['name' => '30 يوم', 'days' => 30, 'sort_order' => 30],
                ['name' => '60 يوم', 'days' => 60, 'sort_order' => 40],
            ];
            
            foreach ($defaultDurations as $duration) {
                $stmt = $pdo->prepare("SELECT id FROM ad_durations WHERE days = ? LIMIT 1");
                $stmt->execute([$duration['days']]);
                if (!$stmt->fetchColumn()) {
                    $stmt = $pdo->prepare("INSERT INTO ad_durations (name, days, is_active, sort_order, created_at) VALUES (?, ?, 1, ?, NOW())");
                    $stmt->execute([$duration['name'], $duration['days'], $duration['sort_order']]);
                }
            }
        } catch (Throwable $e) {
            error_log('Error creating ad tables: ' . $e->getMessage());
        }
    }

    /**
     * Simple unauthorized redirect.
     */
    protected function requireAuth(): void
    {
        if (!$this->currentUser()) {
            $this->setFlash('error', 'يجب تسجيل الدخول أولاً');
            $this->redirect('/login');
        }
    }

    protected function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-_\u0600-\u06FF]+/u', '', $text);
        $text = preg_replace('/\s+/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);

        return trim((string) $text, '-');
    }
}