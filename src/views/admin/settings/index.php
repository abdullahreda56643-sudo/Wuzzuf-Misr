<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$settings = $settings ?? [];
$groupedSettings = $groupedSettings ?? [];
$durations = $durations ?? [];

$groups = [
    'general' => ['label' => 'عام', 'icon' => 'bx-globe', 'description' => 'معلومات الموقع الأساسية'],
    'social' => ['label' => 'السوشال ميديا', 'icon' => 'bx-share-alt', 'description' => 'روابط السوشال ميديا وواتساب العائم'],
    'durations' => ['label' => 'مدد الإعلانات', 'icon' => 'bx-time-five', 'description' => 'المدد التي يمكن اختيارها عند إنشاء إعلان وظيفة'],
];

$groupOrder = ['general', 'social'];

function settingInputType(string $type, string $key): string
{
    if ($type === 'boolean') {
        return 'checkbox';
    }
    if (stripos($key, 'password') !== false || stripos($key, 'secret') !== false || stripos($key, 'token') !== false) {
        return 'password';
    }
    if ($type === 'number') {
        return 'number';
    }
    if ($type === 'text' || stripos($key, 'description') !== false || stripos($key, 'message') !== false) {
        return 'textarea';
    }
    return 'text';
}

function settingLabel(string $key): string
{
    $labels = [
        'site_name' => 'اسم الموقع',
        'social_facebook' => 'فيسبوك',
        'social_twitter' => 'X / تويتر',
        'social_linkedin' => 'لينكدإن',
        'social_instagram' => 'إنستغرام',
        'social_youtube' => 'يوتيوب',
        'social_whatsapp' => 'رقم واتساب العائم',
        'social_whatsapp_message' => 'رسالة واتساب العائم',
    ];

    return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
}
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">SETTINGS</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إعدادات الموقع</h2>
            </div>
            <button type="submit" form="settings-form" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-navy-900">
                <i class="bx bx-save"></i>
                حفظ التغييرات
            </button>
        </div>
    </div>

    <form id="settings-form" method="POST" action="<?= htmlspecialchars(url('/admin/settings')) ?>" class="space-y-6">
        <?php foreach ($groupOrder as $groupKey): ?>
            <?php $groupData = $groupedSettings[$groupKey] ?? []; ?>
            <?php if (empty($groupData)) continue; ?>

            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                        <i class="bx <?= htmlspecialchars($groups[$groupKey]['icon'] ?? 'bx-cog') ?> text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-navy-900"><?= htmlspecialchars($groups[$groupKey]['label'] ?? ucfirst($groupKey)) ?></h3>
                        <p class="text-sm text-slate-500"><?= htmlspecialchars($groups[$groupKey]['description'] ?? '') ?></p>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <?php foreach ($groupData as $setting): ?>
                        <?php
                        $key = (string) ($setting['key'] ?? '');
                        $value = $setting['value'] ?? '';
                        $type = (string) ($setting['type'] ?? 'string');
                        $description = (string) ($setting['description'] ?? '');
                        $inputType = settingInputType($type, $key);
                        $label = settingLabel($key);
                        ?>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <label for="setting_<?= htmlspecialchars($key) ?>" class="mb-2 block text-sm font-black text-slate-700">
                                <?= htmlspecialchars($label) ?>
                            </label>

                            <?php if ($inputType === 'checkbox'): ?>
                                <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-3">
                                    <span class="text-sm font-bold text-slate-700">تفعيل</span>
                                    <input id="setting_<?= htmlspecialchars($key) ?>" type="checkbox" name="<?= htmlspecialchars($key) ?>" value="1" <?= !empty($value) && filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' ?> class="h-5 w-5 rounded border-slate-300 text-gold-500 focus:ring-gold-400" />
                                </label>
                            <?php elseif ($inputType === 'textarea'): ?>
                                <textarea id="setting_<?= htmlspecialchars($key) ?>" name="<?= htmlspecialchars($key) ?>" rows="4" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200"><?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?></textarea>
                            <?php else: ?>
                                <input id="setting_<?= htmlspecialchars($key) ?>" type="<?= htmlspecialchars($inputType) ?>" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200" />
                            <?php endif; ?>

                            <?php if ($description !== ''): ?>
                                <p class="mt-2 text-xs text-slate-500"><?= htmlspecialchars($description) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="flex justify-end">
            <button type="submit" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-black text-navy-900">
                <i class="bx bx-save"></i>
                حفظ الإعدادات
            </button>
        </div>
    </form>

    <form method="POST" action="<?= htmlspecialchars(url('/admin/settings')) ?>" class="space-y-6">
        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700"><i class="bx bx-time-five text-xl"></i></div>
                <div>
                    <h3 class="text-xl font-black text-navy-900">مدد الإعلانات</h3>
                    <p class="text-sm text-slate-500">تحكم في الخيارات الظاهرة لصاحب العمل عند إنشاء الوظيفة.</p>
                </div>
            </div>
            <div class="space-y-4">
                <?php foreach ($durations as $duration): ?>
                    <?php $durationId = (int) ($duration['id'] ?? 0); ?>
                    <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-6">
                        <input name="duration[<?= $durationId ?>][name]" value="<?= htmlspecialchars((string) ($duration['name'] ?? '')) ?>" placeholder="اسم المدة" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        <input name="duration[<?= $durationId ?>][days]" type="number" min="1" value="<?= (int) ($duration['days'] ?? 1) ?>" placeholder="الأيام" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        <input name="duration[<?= $durationId ?>][price]" type="number" min="0" step="0.01" value="<?= htmlspecialchars((string) ($duration['price'] ?? '')) ?>" placeholder="السعر" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        <input name="duration[<?= $durationId ?>][sort_order]" type="number" min="0" value="<?= (int) ($duration['sort_order'] ?? 0) ?>" placeholder="الترتيب" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        <input name="duration[<?= $durationId ?>][description]" value="<?= htmlspecialchars((string) ($duration['description'] ?? '')) ?>" placeholder="الوصف" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700"><input type="checkbox" name="duration[<?= $durationId ?>][is_active]" value="1" <?= !empty($duration['is_active']) ? 'checked' : '' ?> class="h-4 w-4"> مفعلة</label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-5 rounded-2xl border border-dashed border-gold-300 bg-gold-50 p-4">
                <p class="mb-3 text-sm font-black text-slate-800">إضافة مدة جديدة</p>
                <div class="grid gap-3 md:grid-cols-5">
                    <input name="new_duration[name]" placeholder="اسم المدة" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    <input name="new_duration[days]" type="number" min="1" placeholder="عدد الأيام" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    <input name="new_duration[price]" type="number" min="0" step="0.01" placeholder="السعر" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    <input name="new_duration[sort_order]" type="number" min="0" placeholder="الترتيب" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    <input name="new_duration[description]" placeholder="الوصف" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                </div>
            </div>
        </div>
        <div class="flex justify-end"><button type="submit" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-black text-navy-900"><i class="bx bx-save"></i>حفظ مدد الإعلانات</button></div>
    </form>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
