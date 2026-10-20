<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">CREATE ADVERTISEMENT</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إضافة إعلان جديد</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/ads')) ?>" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة
            </a>
        </div>
    </div>

    <form method="POST" action="<?= htmlspecialchars(url('/admin/ads/create')) ?>" class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="space-y-6">
            <!-- عنوان الإعلان -->
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">عنوان الإعلان *</label>
                <input type="text" name="title" placeholder="مثال: إعلان Google AdSense" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200">
            </div>

            <!-- كود Google AdSense -->
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">كود Google AdSense *</label>
                <textarea name="ad_code" rows="8" placeholder="انسخ كود الإعلان الكامل هنا" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200"></textarea>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">الوصف</label>
                <textarea name="description" placeholder="وصف الإعلان..." rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200"></textarea>
            </div>

            <!-- نوع الإعلان والموقع -->
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">نوع الإعلان *</label>
                    <select name="ad_type" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200">
                        <option value="banner">إعلان بانر</option>
                        <option value="featured_job">وظيفة مميزة</option>
                        <option value="company_promo">ترويج شركة</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">مكان الإعلان *</label>
                    <select name="ad_location_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200">
                        <?php foreach (($locations ?? []) as $location): ?>
                            <?php $locationSlug = (string) ($location['slug'] ?? ''); ?>
                            <option value="<?= (int) $location['id'] ?>"><?= htmlspecialchars((string) $location['name']) ?><?= $locationSlug !== '' ? ' — ' . htmlspecialchars($locationSlug) : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- مدة الإعلان والحالة -->
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">مدة الإعلان *</label>
                    <select name="ad_duration_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200">
                        <?php foreach (($durations ?? []) as $duration): ?>
                            <option value="<?= (int) $duration['id'] ?>"><?= htmlspecialchars((string) $duration['name']) ?> (<?= (int) $duration['days'] ?> يوم)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200">
                        <option value="active">نشط</option>
                        <option value="pending">قيد المراجعة</option>
                        <option value="draft">مسودة</option>
                    </select>
                </div>
            </div>

            <!-- التاريخ -->
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">تاريخ البداية *</label>
                    <input type="date" name="start_date" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200" />
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">تاريخ النهاية *</label>
                    <input type="date" name="end_date" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-200" />
                </div>
            </div>

            <!-- الأزرار -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="gold-btn flex-1 rounded-2xl px-4 py-3 text-sm font-black text-navy-900">
                    <i class="bx bx-plus-circle"></i>
                    إضافة الإعلان
                </button>
                <a href="<?= htmlspecialchars(url('/admin/ads')) ?>" class="flex flex-1 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                    <i class="bx bx-x"></i>
                    إلغاء
                </a>
            </div>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
