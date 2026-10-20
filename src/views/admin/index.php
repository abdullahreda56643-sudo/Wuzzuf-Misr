<?php
require_once __DIR__ . '/../layout/admin/header.php';
require_once __DIR__ . '/../layout/admin/sidebar.php';

$dashboardStats = $stats ?? [];
$stats = [
    ['label' => 'إجمالي المستخدمين', 'key' => 'users', 'icon' => 'bx-user', 'color' => 'bg-sky-100 text-sky-700'],
    ['label' => 'إجمالي الشركات', 'key' => 'companies', 'icon' => 'bx-building-house', 'color' => 'bg-violet-100 text-violet-700'],
    ['label' => 'إجمالي الوظائف', 'key' => 'jobs', 'icon' => 'bx-briefcase', 'color' => 'bg-amber-100 text-amber-700'],
    ['label' => 'التطبيقات', 'key' => 'applications', 'icon' => 'bx-file-find', 'color' => 'bg-emerald-100 text-emerald-700'],
];

foreach ($stats as &$stat) {
    $metric = $dashboardStats[$stat['key']] ?? ['value' => 0, 'trend' => '0%'];
    $stat['value'] = (int) ($metric['value'] ?? 0);
    $stat['trend'] = (string) ($metric['trend'] ?? '0%');
    $stat['trendClass'] = str_starts_with($stat['trend'], '-')
        ? 'bg-rose-100 text-rose-700'
        : 'bg-emerald-100 text-emerald-700';
}
unset($stat);

$quickLinks = [
    ['label' => 'إضافة وظيفة', 'url' => '/admin/jobs/create', 'icon' => 'bx-plus-circle', 'color' => 'bg-gold-100 text-gold-700'],
    ['label' => 'إضافة شركة', 'url' => '/admin/companies', 'icon' => 'bx-building-house', 'color' => 'bg-sky-100 text-sky-700'],
    ['label' => 'إضافة مستخدم', 'url' => '/admin/users', 'icon' => 'bx-user-plus', 'color' => 'bg-violet-100 text-violet-700'],
    ['label' => 'الإعدادات', 'url' => '/admin/settings', 'icon' => 'bx-cog', 'color' => 'bg-slate-100 text-slate-700'],
];

$recentJobs = $recentJobs ?? [];
?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 rounded-[30px] border border-slate-200 bg-gradient-to-r from-navy-900 via-navy-800 to-navy-700 p-6 text-white shadow-lift sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-[11px] font-bold tracking-[0.2em] text-gold-300">DASHBOARD OVERVIEW</p>
                <h2 class="mt-2 text-3xl font-black text-white">مرحباً بك في لوحة الإدارة</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-300">إدارة كاملة للمستخدمين، الشركات، الوظائف، والبيانات التشغيلية في منصة الوظائف.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="<?= htmlspecialchars(url('/admin/jobs/create')) ?>" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-navy-900">
                    <i class="bx bx-plus-circle"></i>
                    إضافة وظيفة
                </a>
                <a href="<?= htmlspecialchars(url('/admin/settings')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                    <i class="bx bx-cog"></i>
                    الإعدادات
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-4">
        <?php foreach ($stats as $stat): ?>
            <div class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-soft">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-slate-500"><?= htmlspecialchars($stat['label']) ?></p>
                        <h3 class="mt-3 text-3xl font-black text-navy-900"><?= number_format((int) $stat['value']) ?></h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl <?= htmlspecialchars($stat['color']) ?>">
                        <i class="bx <?= htmlspecialchars($stat['icon']) ?> text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <span class="rounded-full <?= htmlspecialchars($stat['trendClass']) ?> px-2.5 py-1 text-xs font-black"><?= htmlspecialchars($stat['trend']) ?></span>
                    <span class="text-xs font-bold text-slate-400">هذا الشهر</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.35fr_0.95fr]">
        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">RECENT ACTIVITIES</p>
                    <h3 class="mt-1 text-xl font-black text-navy-900">آخر الوظائف</h3>
                </div>
                <a href="<?= htmlspecialchars(url('/admin/jobs')) ?>" class="text-sm font-black text-gold-700 transition hover:text-gold-600">عرض الكل</a>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200">
                <table class="admin-table min-w-full text-right text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-bold">العنوان</th>
                            <th class="px-4 py-3 font-bold">الشركة</th>
                            <th class="px-4 py-3 font-bold">الحالة</th>
                            <th class="px-4 py-3 font-bold">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentJobs)): ?>
                            <?php foreach ($recentJobs as $job): ?>
                                <tr class="border-t border-slate-200 bg-white">
                                    <td class="px-4 py-3 font-bold text-slate-800"><?= htmlspecialchars($job['title'] ?? 'بدون عنوان') ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($job['company_name'] ?? 'غير محدد') ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= ($job['status'] ?? 'draft') === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                            <?= htmlspecialchars($job['status'] ?? 'draft') ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars(date('Y-m-d', strtotime($job['created_at'] ?? 'now'))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-500">لا توجد وظائف حالياً.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
            <div class="mb-5">
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">QUICK ACTIONS</p>
                <h3 class="mt-1 text-xl font-black text-navy-900">إجراءات سريعة</h3>
            </div>

            <div class="grid gap-3">
                <?php foreach ($quickLinks as $link): ?>
                    <a href="<?= htmlspecialchars(url($link['url'])) ?>" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 transition hover:bg-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl <?= htmlspecialchars($link['color']) ?>">
                                <i class="bx <?= htmlspecialchars($link['icon']) ?> text-xl"></i>
                            </div>
                            <span class="text-sm font-black text-slate-800"><?= htmlspecialchars($link['label']) ?></span>
                        </div>
                        <i class="bx bx-chevron-left text-xl text-slate-400"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/admin/footer.php'; ?>

