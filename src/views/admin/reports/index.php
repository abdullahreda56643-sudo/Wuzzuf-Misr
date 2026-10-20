<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$reports = $reports ?? [];
$status = $status ?? 'all';
$type = $type ?? 'all';
$search = trim((string) ($search ?? ''));
$stats = $stats ?? [
    'pending' => 0,
    'reviewing' => 0,
    'resolved' => 0,
    'rejected' => 0,
];

$statusLabels = [
    'all' => 'الكل',
    'pending' => 'قيد المراجعة',
    'reviewing' => 'جاري المراجعة',
    'resolved' => 'تم الحل',
    'rejected' => 'مرفوض',
];

$reportTypeLabels = [
    'all' => 'الكل',
    'job' => 'وظيفة',
    'company' => 'شركة',
    'user' => 'مستخدم',
    'ad' => 'إعلان',
];

$reasonLabels = [
    'spam' => 'سبام',
    'inappropriate' => 'محتوى غير لائق',
    'fraud' => 'احتيال',
    'harassment' => 'تحرش',
    'copyright' => 'حقوق ملكية',
    'other' => 'أخرى',
];

$statusClasses = [
    'pending' => 'bg-amber-100 text-amber-700',
    'reviewing' => 'bg-sky-100 text-sky-700',
    'resolved' => 'bg-emerald-100 text-emerald-700',
    'rejected' => 'bg-rose-100 text-rose-700',
];
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">REPORTS</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إدارة البلاغات</h2>
            </div>
            <a href="<?= htmlspecialchars(url('/admin/reports')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-black text-slate-700">
                <i class="bx bx-refresh"></i>
                تحديث
            </a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <?php foreach (['pending', 'reviewing', 'resolved', 'rejected'] as $key): ?>
            <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-soft">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"><?= htmlspecialchars($statusLabels[$key] ?? ucfirst($key)) ?></p>
                        <h3 class="mt-2 text-3xl font-black text-navy-900"><?= (int) ($stats[$key] ?? 0) ?></h3>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl <?= $statusClasses[$key] ?? 'bg-slate-100 text-slate-700' ?>">
                        <i class="bx bx-file text-xl"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
        <form method="GET" action="<?= htmlspecialchars(url('/admin/reports')) ?>" class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex w-full max-w-md items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                <i class="bx bx-search text-lg text-slate-400"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث عن اسم المبلغ أو الوصف..." class="w-full bg-transparent text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:outline-none" />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <select name="status" class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-bold text-slate-700 focus:outline-none">
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="type" class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-bold text-slate-700 focus:outline-none">
                    <?php foreach ($reportTypeLabels as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $type === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="gold-btn rounded-2xl px-4 py-2.5 text-sm font-black text-navy-900">تصفية</button>
                <?php if ($status !== 'all' || $type !== 'all' || $search !== ''): ?>
                    <a href="<?= htmlspecialchars(url('/admin/reports')) ?>" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-black text-slate-700">إعادة تعيين</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="admin-table min-w-full text-right text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-bold">#</th>
                        <th class="px-4 py-3 font-bold">المرسل</th>
                        <th class="px-4 py-3 font-bold">نوع العنصر</th>
                        <th class="px-4 py-3 font-bold">السبب</th>
                        <th class="px-4 py-3 font-bold">الحالة</th>
                        <th class="px-4 py-3 font-bold">الوصف</th>
                        <th class="px-4 py-3 font-bold">التاريخ</th>
                        <th class="px-4 py-3 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $report): ?>
                            <?php
                                $reporterName = trim((string) (($report['first_name'] ?? '') . ' ' . ($report['last_name'] ?? '')));
                                $reporterName = $reporterName !== '' ? $reporterName : 'مستخدم';
                                $reportType = (string) ($report['reported_type'] ?? 'job');
                                $reason = (string) ($report['reason'] ?? 'other');
                                $statusKey = (string) ($report['status'] ?? 'pending');
                                $description = trim((string) ($report['description'] ?? ''));
                                $createdAt = $report['created_at'] ?? date('Y-m-d H:i:s');
                                $statusClass = $statusClasses[$statusKey] ?? 'bg-slate-100 text-slate-700';
                            ?>
                            <tr class="border-t border-slate-200 bg-white">
                                <td class="px-4 py-3 font-black text-slate-600">#<?= (int) ($report['id'] ?? 0) ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-black text-slate-800"><?= htmlspecialchars($reporterName) ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars((string) ($report['email'] ?? '')) ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-700">
                                        <?= htmlspecialchars($reportTypeLabels[$reportType] ?? ucfirst($reportType)) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-700">
                                        <?= htmlspecialchars($reasonLabels[$reason] ?? ucfirst($reason)) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= $statusClass ?>">
                                        <?= htmlspecialchars($statusLabels[$statusKey] ?? ucfirst($statusKey)) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="max-w-md text-slate-600">
                                        <?= htmlspecialchars($description !== '' ? $description : 'لا يوجد وصف.') ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars(date('Y-m-d', strtotime((string) $createdAt))) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="<?= htmlspecialchars(url('/admin/reports/' . (int) ($report['id'] ?? 0) . '/status/reviewing')) ?>">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-black text-sky-700 transition hover:bg-sky-100">
                                                <i class="bx bx-show"></i>
                                                مراجعة
                                            </button>
                                        </form>
                                        <form method="POST" action="<?= htmlspecialchars(url('/admin/reports/' . (int) ($report['id'] ?? 0) . '/status/resolved')) ?>">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-xs font-black text-emerald-700 transition hover:bg-emerald-100">
                                                <i class="bx bx-check"></i>
                                                حل
                                            </button>
                                        </form>
                                        <form method="POST" action="<?= htmlspecialchars(url('/admin/reports/' . (int) ($report['id'] ?? 0) . '/status/rejected')) ?>">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-black text-amber-700 transition hover:bg-amber-100">
                                                <i class="bx bx-x"></i>
                                                رفض
                                            </button>
                                        </form>
                                        <form method="POST" action="<?= htmlspecialchars(url('/admin/reports/' . (int) ($report['id'] ?? 0) . '/delete')) ?>" onsubmit="return confirm('هل تريد حذف هذا البلاغ؟')">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-black text-red-700 transition hover:bg-red-100">
                                                <i class="bx bx-trash"></i>
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500">لا توجد بلاغات مطابقة للبحث الحالي.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
