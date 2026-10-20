<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$job = $job ?? [];
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">JOB DETAILS</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900"><?= htmlspecialchars((string) ($job['title'] ?? 'تفاصيل الوظيفة')) ?></h2>
            </div>
            <a href="<?= htmlspecialchars(url('/admin/jobs')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <p class="text-sm font-black text-slate-700">العنوان</p>
                <p class="mt-2 text-lg font-black text-navy-900"><?= htmlspecialchars((string) ($job['title'] ?? '')) ?></p>
            </div>
            <div>
                <p class="text-sm font-black text-slate-700">الحالة</p>
                <p class="mt-2 text-lg font-black text-navy-900"><?= htmlspecialchars((string) ($job['status'] ?? '')) ?></p>
            </div>
            <div>
                <p class="text-sm font-black text-slate-700">الشركة</p>
                <p class="mt-2 text-lg font-black text-navy-900"><?= htmlspecialchars((string) ($job['company_name'] ?? 'غير محدد')) ?></p>
            </div>
            <div>
                <p class="text-sm font-black text-slate-700">التصنيف والموقع</p>
                <p class="mt-2 text-lg font-black text-navy-900"><?= htmlspecialchars(trim((string) (($job['category_name'] ?? 'غير محدد') . ' - ' . ($job['governorate_name'] ?? 'غير محدد') . ' - ' . ($job['city_name'] ?? 'غير محدد')))) ?></p>
            </div>
            <div class="lg:col-span-2">
                <p class="text-sm font-black text-slate-700">الوصف</p>
                <div class="mt-2 rounded-2xl bg-slate-50 p-4 text-slate-700"><?= nl2br(htmlspecialchars((string) ($job['description'] ?? ''))) ?></div>
            </div>
            <div>
                <p class="text-sm font-black text-slate-700">المتطلبات</p>
                <div class="mt-2 rounded-2xl bg-slate-50 p-4 text-slate-700"><?= nl2br(htmlspecialchars((string) ($job['requirements'] ?? 'لا توجد'))) ?></div>
            </div>
            <div>
                <p class="text-sm font-black text-slate-700">المزايا</p>
                <div class="mt-2 rounded-2xl bg-slate-50 p-4 text-slate-700"><?= nl2br(htmlspecialchars((string) ($job['benefits'] ?? 'لا توجد'))) ?></div>
            </div>
        </div>
        <?php if (($job['status'] ?? 'draft') !== 'published'): ?>
            <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
                <form method="post" action="<?= htmlspecialchars(url('/admin/jobs/' . (int) ($job['id'] ?? 0) . '/reject')) ?>">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-black text-rose-700 hover:bg-rose-100" onclick="return confirm('هل تريد رفض هذه الوظيفة؟')"><i class="bx bx-x"></i>رفض الوظيفة</button>
                </form>
                <form method="post" action="<?= htmlspecialchars(url('/admin/jobs/' . (int) ($job['id'] ?? 0) . '/approve')) ?>">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white hover:bg-emerald-700" onclick="return confirm('هل تريد اعتماد ونشر هذه الوظيفة؟')"><i class="bx bx-check"></i>اعتماد ونشر</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>

