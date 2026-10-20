<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$ads = $ads ?? [];
$search = trim((string) ($search ?? ''));
$page = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$totalAds = (int) ($totalAds ?? 0);

$typeLabels = [
    'banner' => 'إعلان يافطة',
    'company' => 'إعلان شركة',
    'featured_job' => 'وظيفة مميزة',
];

$locationLabels = [
    'sidebar' => 'الشريط الجانبي',
    'homepage' => 'الصفحة الرئيسية',
    'jobs_page' => 'صفحة الوظائف',
    'top' => 'الأعلى',
];
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">ADVERTISEMENT MANAGEMENT</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إدارة الإعلانات</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/ads/create')) ?>" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-navy-900">
                <i class="bx bx-plus-circle"></i>
                إضافة إعلان
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
        <form method="GET" action="<?= htmlspecialchars(url('/admin/ads')) ?>" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex w-full max-w-md items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                <i class="bx bx-search text-lg text-slate-400"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث عن عنوان الإعلان أو نوعه..." class="w-full bg-transparent text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:outline-none" />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="gold-btn rounded-2xl px-4 py-2.5 text-sm font-black text-navy-900">بحث</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= htmlspecialchars(url('/admin/ads')) ?>" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-black text-slate-700">إعادة تعيين</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="mb-4 text-sm font-bold text-slate-600">
            إجمالي النتائج: <span class="text-slate-900"><?= (int) $totalAds ?></span>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table min-w-full text-right text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-bold">#</th>
                        <th class="px-4 py-3 font-bold">العنوان</th>
                        <th class="px-4 py-3 font-bold">النوع</th>
                        <th class="px-4 py-3 font-bold">الموقع</th>
                        <th class="px-4 py-3 font-bold">المشاهدات/الضغطات</th>
                        <th class="px-4 py-3 font-bold">التاريخ</th>
                        <th class="px-4 py-3 font-bold">الحالة</th>
                        <th class="px-4 py-3 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ads)): ?>
                        <?php foreach ($ads as $ad): ?>
                            <tr class="border-t border-slate-200 bg-white">
                                <td class="px-4 py-3 font-bold text-slate-600"><?= (int) ($ad['id'] ?? 0) ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-black text-slate-800"><?= htmlspecialchars((string) ($ad['title'] ?? 'بدون عنوان')) ?></div>
                                    <?php
                                        $adPreview = trim((string) ($ad['ad_code'] ?? ''));
                                        $adPreview = preg_replace('/\s+/', ' ', strip_tags($adPreview));
                                        $adPreview = mb_substr($adPreview, 0, 80, 'UTF-8');
                                    ?>
                                    <?php if ($adPreview !== ''): ?>
                                        <div class="mt-1 max-w-xs break-all text-[11px] leading-5 text-slate-500">
                                            <?= htmlspecialchars($adPreview . (mb_strlen($adPreview, 'UTF-8') >= 80 ? '...' : '')) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= match($ad['ad_type'] ?? 'banner') {
                                        'company_promo' => 'bg-purple-100 text-purple-700',
                                        'featured_job' => 'bg-sky-100 text-sky-700',
                                        default => 'bg-gold-100 text-gold-700'
                                    } ?>">
                                        <?= htmlspecialchars($typeLabels[$ad['ad_type'] ?? 'banner'] ?? $ad['ad_type'] ?? 'إعلان') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?= htmlspecialchars((string) ($ad['location_name'] ?? $ad['ad_location_id'] ?? 'غير محدد')) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center gap-1 text-emerald-700">
                                            <i class="bx bx-eye text-sm"></i>
                                            <?= (int) ($ad['views_count'] ?? 0) ?>
                                        </div>
                                        <div class="flex items-center gap-1 text-sky-700">
                                            <i class="bx bx-mouse-alt text-sm"></i>
                                            <?= (int) ($ad['clicks_count'] ?? 0) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1 text-xs text-slate-500">
                                        <div>من: <?= htmlspecialchars(date('Y-m-d', strtotime((string) ($ad['start_date'] ?? 'now')))) ?></div>
                                        <div>إلى: <?= htmlspecialchars(date('Y-m-d', strtotime((string) ($ad['end_date'] ?? 'now')))) ?></div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= (($ad['status'] ?? 'active') === 'active') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= (($ad['status'] ?? 'active') === 'active') ? 'نشط' : 'غير نشط' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="<?= htmlspecialchars(url('/admin/ads/' . (int) ($ad['id'] ?? 0) . '/edit')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-black text-sky-700 transition hover:bg-sky-100">
                                            <i class="bx bx-edit-alt"></i>
                                            تعديل
                                        </a>
                                        <a href="<?= htmlspecialchars(url('/admin/ads/' . (int) ($ad['id'] ?? 0) . '/delete')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-black text-red-700 transition hover:bg-red-100" onclick="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                            <i class="bx bx-trash"></i>
                                            حذف
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500">لا توجد إعلانات مطابقة للبحث.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                if ($endPage - $startPage < 4) {
                    if ($startPage === 1) {
                        $endPage = min($totalPages, $startPage + 4);
                    } else {
                        $startPage = max(1, $endPage - 4);
                    }
                }
            ?>
            <div class="mt-6 flex justify-center">
                <nav class="inline-flex items-center gap-1 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm">
                    <?php if ($page > 1): ?>
                        <a href="<?= htmlspecialchars(url('/admin/ads') . '?q=' . urlencode($search) . '&page=' . ($page - 1)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="السابق">
                            <i class="bx bx-chevron-right text-xl"></i>
                        </a>
                    <?php else: ?>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300">
                            <i class="bx bx-chevron-right text-xl"></i>
                        </span>
                    <?php endif; ?>

                    <?php if ($startPage > 1): ?>
                        <a href="<?= htmlspecialchars(url('/admin/ads') . '?q=' . urlencode($search) . '&page=1') ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-100">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="flex h-9 w-9 items-center justify-center text-slate-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="<?= htmlspecialchars(url('/admin/ads') . '?q=' . urlencode($search) . '&page=' . $i) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black <?= $i === $page ? 'bg-gold-500 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="flex h-9 w-9 items-center justify-center text-slate-400">...</span>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars(url('/admin/ads') . '?q=' . urlencode($search) . '&page=' . $totalPages) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-100"><?= $totalPages ?></a>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= htmlspecialchars(url('/admin/ads') . '?q=' . urlencode($search) . '&page=' . ($page + 1)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="التالي">
                            <i class="bx bx-chevron-left text-xl"></i>
                        </a>
                    <?php else: ?>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300">
                            <i class="bx bx-chevron-left text-xl"></i>
                        </span>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>

