<?php

class AdService
{
    protected function db(): PDO
    {
        return Database::connection();
    }

    public function expireAds(): int
    {
        try {
            $sql = "
                UPDATE ads
                SET status = 'expired', updated_at = NOW()
                WHERE status = 'active' AND end_date <= NOW()
            ";

            $stmt = $this->db()->prepare($sql);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (Throwable $e) {
            error_log('Error expiring ads: ' . $e->getMessage());
            return 0;
        }
    }

    public function activatePendingAds(): int
    {
        try {
            $sql = "
                UPDATE ads
                SET status = 'active', updated_at = NOW()
                WHERE status = 'pending' AND start_date <= NOW() AND end_date > NOW()
            ";

            $stmt = $this->db()->prepare($sql);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (Throwable $e) {
            error_log('Error activating pending ads: ' . $e->getMessage());
            return 0;
        }
    }

    public function getActiveAdsForLocation(int $locationId): array
    {
        try {
            $stmt = $this->db()->prepare(
                'SELECT * FROM ads 
                WHERE ad_location_id = ? 
                  AND status = "active" 
                  AND start_date <= NOW() 
                  AND end_date > NOW() 
                  AND TRIM(COALESCE(ad_code, "")) <> "" 
                ORDER BY created_at DESC'
            );
            $stmt->execute([$locationId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('Error getting active ads: ' . $e->getMessage());
            return [];
        }
    }

    public function ensureFallbackAdForLocation(int $locationId, string $slug = 'unknown'): bool
    {
        try {
            $pdo = $this->db();
            
            // التحقق من وجود إعلان نشط لهذا الموقع
            $existing = $pdo->prepare(
                'SELECT id FROM ads 
                WHERE ad_location_id = ? 
                  AND status = "active" 
                  AND start_date <= NOW() 
                  AND end_date > NOW() 
                LIMIT 1'
            );
            $existing->execute([$locationId]);

            if ($existing->fetchColumn()) {
                return true;
            }

            // جلب مستخدم افتراضي
            $userId = (int) $pdo->query(
                'SELECT id FROM users WHERE status = "active" ORDER BY id ASC LIMIT 1'
            )->fetchColumn();
            if ($userId <= 0) {
                $userId = 1;
            }

            // جلب مدة إعلان افتراضية
            $durationId = (int) $pdo->query(
                'SELECT id FROM ad_durations WHERE is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT 1'
            )->fetchColumn();
            if ($durationId <= 0) {
                // إذا لم توجد مدة، نقوم بإنشائها
                $pdo->exec("
                    INSERT INTO ad_durations (name, days, is_active, sort_order, created_at) 
                    VALUES ('30 يوم', 30, 1, 10, NOW())
                ");
                $durationId = (int) $pdo->lastInsertId();
                if ($durationId <= 0) {
                    $durationId = 1;
                }
            }

            $title = 'إعلان تجريبي - ' . preg_replace('/[^a-zA-Z0-9_\-\s]+/', ' ', $slug ?: 'slot');
            $title = trim((string) $title);
            if ($title === '') {
                $title = 'إعلان تجريبي';
            }

            // كود إعلان تجريبي (لن يستخدم إذا كان هناك إعلان حقيقي)
            $code = <<<'HTML'
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-1439681675419375"
     data-ad-slot="1234567890"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1439681675419375"
     crossorigin="anonymous"></script>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
HTML;

            $stmt = $pdo->prepare(
                'INSERT INTO ads (
                    user_id, title, ad_code, description, ad_location_id, ad_duration_id, ad_type,
                    start_date, end_date, status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), "active", NOW(), NOW())'
            );

            return $stmt->execute([
                $userId,
                $title,
                $code,
                'إعلان تلقائي للموقع - ' . $slug,
                $locationId,
                $durationId,
                'banner',
            ]);
        } catch (Throwable $e) {
            error_log('Error creating fallback ad: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * جلب جميع الإعلانات النشطة حسب الموقع (باستخدام slug)
     */
    public function getAdsByLocationSlug(string $locationSlug): array
    {
        try {
            $pdo = $this->db();
            $stmt = $pdo->prepare(
                'SELECT a.* 
                FROM ads a
                INNER JOIN ad_locations l ON l.id = a.ad_location_id
                WHERE l.slug = ? 
                  AND a.status = "active" 
                  AND a.start_date <= NOW() 
                  AND a.end_date > NOW() 
                  AND TRIM(COALESCE(a.ad_code, "")) <> "" 
                ORDER BY a.created_at DESC
                LIMIT 1'
            );
            $stmt->execute([$locationSlug]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('Error getting ads by location slug: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * جلب إعلان واحد حسب الموقع (باستخدام slug)
     */
    public function getAdByLocationSlug(string $locationSlug): ?array
    {
        $ads = $this->getAdsByLocationSlug($locationSlug);
        return !empty($ads) ? $ads[0] : null;
    }

    /**
     * جلب جميع مواقع الإعلانات النشطة
     */
    public function getActiveLocations(): array
    {
        try {
            $pdo = $this->db();
            $stmt = $pdo->query(
                'SELECT id, slug, name, sort_order 
                FROM ad_locations 
                WHERE is_active = 1 
                ORDER BY sort_order ASC'
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('Error getting active locations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * جلب إحصاءات الإعلانات
     */
    public function getAdStats(): array
    {
        try {
            $pdo = $this->db();
            $stats = [];
            
            // إجمالي الإعلانات
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM ads");
            $stats['total'] = (int) $stmt->fetchColumn();
            
            // الإعلانات النشطة
            $stmt = $pdo->query("
                SELECT COUNT(*) as active 
                FROM ads 
                WHERE status = 'active' 
                  AND start_date <= NOW() 
                  AND end_date > NOW()
            ");
            $stats['active'] = (int) $stmt->fetchColumn();
            
            // الإعلانات المنتهية
            $stmt = $pdo->query("
                SELECT COUNT(*) as expired 
                FROM ads 
                WHERE status = 'expired' OR end_date <= NOW()
            ");
            $stats['expired'] = (int) $stmt->fetchColumn();
            
            // الإعلانات المعلقة
            $stmt = $pdo->query("
                SELECT COUNT(*) as pending 
                FROM ads 
                WHERE status = 'pending'
            ");
            $stats['pending'] = (int) $stmt->fetchColumn();
            
            return $stats;
        } catch (Throwable $e) {
            error_log('Error getting ad stats: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'expired' => 0,
                'pending' => 0,
            ];
        }
    }

    /**
     * تحديث عدد مرات الظهور للإعلان
     */
    public function incrementViews(int $adId): bool
    {
        try {
            $pdo = $this->db();
            $stmt = $pdo->prepare(
                'UPDATE ads SET views_count = views_count + 1 WHERE id = ?'
            );
            return $stmt->execute([$adId]);
        } catch (Throwable $e) {
            error_log('Error incrementing views: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * تحديث عدد مرات النقر للإعلان
     */
    public function incrementClicks(int $adId): bool
    {
        try {
            $pdo = $this->db();
            $stmt = $pdo->prepare(
                'UPDATE ads SET clicks_count = clicks_count + 1 WHERE id = ?'
            );
            return $stmt->execute([$adId]);
        } catch (Throwable $e) {
            error_log('Error incrementing clicks: ' . $e->getMessage());
            return false;
        }
    }
}