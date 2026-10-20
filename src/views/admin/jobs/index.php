<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$jobs = $jobs ?? [];
$search = trim((string) ($search ?? ''));
$page = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$totalJobs = (int) ($totalJobs ?? 0);
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">JOB MANAGEMENT</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إدارة الوظائف</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/jobs/create')) ?>" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-navy-900">
                <i class="bx bx-plus-circle"></i>
                إضافة وظيفة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
        <form method="GET" action="<?= htmlspecialchars(url('/admin/jobs')) ?>" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex w-full max-w-md items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                <i class="bx bx-search text-lg text-slate-400"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث عن عنوان الوظيفة أو الشركة..." class="w-full bg-transparent text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:outline-none" />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="gold-btn rounded-2xl px-4 py-2.5 text-sm font-black text-navy-900">بحث</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= htmlspecialchars(url('/admin/jobs')) ?>" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-black text-slate-700">إعادة تعيين</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="mb-4 text-sm font-bold text-slate-600">
            إجمالي النتائج: <span class="text-slate-900"><?= (int) $totalJobs ?></span>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table min-w-full text-right text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-bold">#</th>
                        <th class="px-4 py-3 font-bold">العنوان</th>
                        <th class="px-4 py-3 font-bold">الشركة</th>
                        <th class="px-4 py-3 font-bold">الحالة</th>
                        <th class="px-4 py-3 font-bold">تاريخ الإنشاء</th>
                        <th class="px-4 py-3 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($jobs)): ?>
                        <?php foreach ($jobs as $job): ?>
                            <tr class="border-t border-slate-200 bg-white">
                                <td class="px-4 py-3 font-bold text-slate-600"><?= (int) ($job['id'] ?? 0) ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-black text-slate-800"><?= htmlspecialchars((string) ($job['title'] ?? 'بدون عنوان')) ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars((string) ($job['slug'] ?? '')) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars((string) ($job['company_name'] ?? 'غير محدد')) ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= (($job['status'] ?? 'draft') === 'published') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= htmlspecialchars((string) ($job['status'] ?? 'draft')) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars(date('Y-m-d', strtotime((string) ($job['created_at'] ?? 'now')))) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="<?= htmlspecialchars(url('/admin/jobs/' . (int) ($job['id'] ?? 0) . '/edit')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-black text-sky-700 transition hover:bg-sky-100">
                                            <i class="bx bx-edit-alt"></i>
                                            تعديل
                                        </a>
                                        <a href="<?= htmlspecialchars(url('/admin/jobs/' . (int) ($job['id'] ?? 0))) ?>" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-black text-slate-700 transition hover:bg-slate-100">
                                            <i class="bx bx-show"></i>
                                            تفاصيل
                                        </a>
                                        <?php if (($job['status'] ?? 'draft') !== 'published'): ?>
                                            <form method="post" action="<?= htmlspecialchars(url('/admin/jobs/' . (int) ($job['id'] ?? 0) . '/approve')) ?>" class="inline">
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-xs font-black text-emerald-700 transition hover:bg-emerald-100" onclick="return confirm('هل تريد اعتماد ونشر هذه الوظيفة؟')">
                                                    <i class="bx bx-check"></i>
                                                    اعتماد
                                                </button>
                                            </form>
                                            <form method="post" action="<?= htmlspecialchars(url('/admin/jobs/' . (int) ($job['id'] ?? 0) . '/reject')) ?>" class="inline">
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-black text-rose-700 transition hover:bg-rose-100" onclick="return confirm('هل تريد رفض هذه الوظيفة؟')">
                                                    <i class="bx bx-x"></i>
                                                    رفض
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="<?= htmlspecialchars(url('/admin/jobs/' . (int) ($job['id'] ?? 0) . '/delete')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-black text-red-700 transition hover:bg-red-100" onclick="return confirm('هل أنت متأكد من حذف هذه الوظيفة؟')">
                                            <i class="bx bx-trash"></i>
                                            حذف
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد وظائف مطابقة للبحث.</td>
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
                        <a href="<?= htmlspecialchars(url('/admin/jobs') . '?q=' . urlencode($search) . '&page=' . ($page - 1)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="السابق">
                            <i class="bx bx-chevron-right text-xl"></i>
                        </a>
                    <?php else: ?>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300">
                            <i class="bx bx-chevron-right text-xl"></i>
                        </span>
                    <?php endif; ?>

                    <?php if ($startPage > 1): ?>
                        <a href="<?= htmlspecialchars(url('/admin/jobs') . '?q=' . urlencode($search) . '&page=1') ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-100">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="flex h-9 w-9 items-center justify-center text-slate-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="<?= htmlspecialchars(url('/admin/jobs') . '?q=' . urlencode($search) . '&page=' . $i) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black <?= $i === $page ? 'bg-gold-500 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="flex h-9 w-9 items-center justify-center text-slate-400">...</span>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars(url('/admin/jobs') . '?q=' . urlencode($search) . '&page=' . $totalPages) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-100"><?= $totalPages ?></a>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= htmlspecialchars(url('/admin/jobs') . '?q=' . urlencode($search) . '&page=' . ($page + 1)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="التالي">
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

