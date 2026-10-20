<?php

class SettingController extends Controller
{
    private array $siteBrandSettings = [
        ['key' => 'site_name', 'value' => 'بوابة وظائف مصر | Wazaef Misr', 'type' => 'string', 'group' => 'general', 'description' => 'اسم الموقع الظاهر في الهيدر والفوتر'],
        ['key' => 'social_facebook', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'رابط فيسبوك'],
        ['key' => 'social_twitter', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'رابط X / تويتر'],
        ['key' => 'social_linkedin', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'رابط لينكدإن'],
        ['key' => 'social_instagram', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'رابط إنستغرام'],
        ['key' => 'social_youtube', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'رابط يوتيوب'],
        ['key' => 'social_whatsapp', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'رقم واتساب أو رابط wa.me للزر العائم'],
        ['key' => 'social_whatsapp_message', 'value' => 'السلام عليكم، أريد معرفة المزيد', 'type' => 'text', 'group' => 'social', 'description' => 'نص الرسالة المرسلة عند الضغط على واتساب العائم'],
    ];

    private function ensureSiteBrandSettings(PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'INSERT INTO settings (`key`, value, type, `group`, description, is_editable)
             VALUES (?, ?, ?, ?, ?, 1)
             ON DUPLICATE KEY UPDATE `key` = `key`'
        );

        foreach ($this->siteBrandSettings as $setting) {
            $stmt->execute([
                $setting['key'],
                $setting['value'],
                $setting['type'],
                $setting['group'],
                $setting['description'],
            ]);
        }
    }

    public function index()
    {
        $pdo = $this->db();
        $this->ensureSiteBrandSettings($pdo);
        $allowedKeys = array_column($this->siteBrandSettings, 'key');
        $placeholders = implode(', ', array_fill(0, count($allowedKeys), '?'));
        $stmt = $pdo->prepare("SELECT * FROM settings WHERE `key` IN ({$placeholders}) ORDER BY `group` ASC, `key` ASC");
        $stmt->execute($allowedKeys);
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $durations = $pdo->query('SELECT * FROM ad_durations ORDER BY sort_order ASC, days ASC')->fetchAll(PDO::FETCH_ASSOC);

        $groupedSettings = [];
        foreach ($settings as $setting) {
            $group = (string) ($setting['group'] ?? 'general');
            $groupedSettings[$group][] = $setting;
        }

        $this->view('admin.settings.index', [
            'title' => 'الإعدادات',
            'settings' => $settings,
            'groupedSettings' => $groupedSettings,
            'durations' => $durations,
        ]);
    }

    public function save()
    {
        if ($this->isPost()) {
            $pdo = $this->db();
            $this->ensureSiteBrandSettings($pdo);
            $fields = $_POST;
            $allowedKeys = array_column($this->siteBrandSettings, 'key');

            foreach ((array) ($fields['duration'] ?? []) as $id => $duration) {
                $days = max(1, (int) ($duration['days'] ?? 0));
                $name = trim((string) ($duration['name'] ?? ''));
                if ($name === '' || $days <= 0) {
                    continue;
                }

                $stmt = $pdo->prepare('UPDATE ad_durations SET name = ?, days = ?, description = ?, price = ?, is_active = ?, sort_order = ?, updated_at = NOW() WHERE id = ?');
                $stmt->execute([
                    $name,
                    $days,
                    trim((string) ($duration['description'] ?? '')) ?: null,
                    ($duration['price'] ?? '') !== '' ? (float) $duration['price'] : null,
                    isset($duration['is_active']) ? 1 : 0,
                    max(0, (int) ($duration['sort_order'] ?? 0)),
                    (int) $id,
                ]);
            }

            $newDuration = $fields['new_duration'] ?? [];
            if (trim((string) ($newDuration['name'] ?? '')) !== '' && (int) ($newDuration['days'] ?? 0) > 0) {
                $stmt = $pdo->prepare('INSERT INTO ad_durations (name, days, description, price, is_active, sort_order, created_at) VALUES (?, ?, ?, ?, 1, ?, NOW())');
                $stmt->execute([
                    trim((string) $newDuration['name']),
                    max(1, (int) $newDuration['days']),
                    trim((string) ($newDuration['description'] ?? '')) ?: null,
                    ($newDuration['price'] ?? '') !== '' ? (float) $newDuration['price'] : null,
                    max(0, (int) ($newDuration['sort_order'] ?? 0)),
                ]);
            }

            foreach ($fields as $key => $value) {
                if ($key === 'csrf' || $key === 'submit' || !in_array($key, $allowedKeys, true)) {
                    continue;
                }

                $stmt = $pdo->prepare('UPDATE settings SET value = ?, updated_at = NOW() WHERE `key` = ?');
                $stmt->execute([$value, $key]);
            }

            $this->setFlash('success', 'تم حفظ الإعدادات بنجاح');
            $this->redirect('/admin/settings');
        }

        $this->redirect('/admin/settings');
    }
}

