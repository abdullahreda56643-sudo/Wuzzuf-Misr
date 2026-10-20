<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$job = $job ?? [];
$companies = $companies ?? [];
$jobId = (int) ($job['id'] ?? 0);
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">EDIT JOB</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">تعديل الوظيفة</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/jobs')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <form method="post" action="<?= htmlspecialchars(url('/admin/jobs/' . $jobId . '/edit')) ?>" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="company_id" class="block text-sm font-black text-slate-700">الشركة</label>
                    <select id="company_id" name="company_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="">اختر الشركة</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= (int) ($company['id'] ?? 0) ?>" <?= ((int) ($job['company_id'] ?? 0) === (int) ($company['id'] ?? 0)) ? 'selected' : '' ?>><?= htmlspecialchars((string) ($company['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="status" class="block text-sm font-black text-slate-700">الحالة</label>
                    <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="draft" <?= (($job['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>مسودة</option>
                        <option value="published" <?= (($job['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>>منشور</option>
                        <option value="closed" <?= (($job['status'] ?? 'draft') === 'closed') ? 'selected' : '' ?>>مغلق</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label for="title" class="block text-sm font-black text-slate-700">عنوان الوظيفة</label>
                <input id="title" name="title" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" value="<?= htmlspecialchars((string) ($job['title'] ?? '')) ?>">
            </div>

            <div class="space-y-2">
                <label for="slug" class="block text-sm font-black text-slate-700">الرابط المختصر</label>
                <input id="slug" name="slug" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" value="<?= htmlspecialchars((string) ($job['slug'] ?? '')) ?>">
            </div>

            <div class="space-y-2">
                <label for="description" class="block text-sm font-black text-slate-700">وصف الوظيفة</label>
                <textarea id="description" name="description" rows="6" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white"><?= htmlspecialchars((string) ($job['description'] ?? '')) ?></textarea>
            </div>

            <div class="space-y-2">
                <label for="requirements" class="block text-sm font-black text-slate-700">المتطلبات</label>
                <textarea id="requirements" name="requirements" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white"><?= htmlspecialchars((string) ($job['requirements'] ?? '')) ?></textarea>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?= htmlspecialchars(url('/admin/jobs')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="gold-btn rounded-2xl px-5 py-3 text-sm font-black text-navy-900">تحديث الوظيفة</button>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
