<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$governorates = $governorates ?? [];
$cities = $cities ?? [];
$governoratesPage = (int) ($governoratesPage ?? 1);
$governoratesTotalPages = (int) ($governoratesTotalPages ?? 1);
$citiesPage = (int) ($citiesPage ?? 1);
$citiesTotalPages = (int) ($citiesTotalPages ?? 1);

$baseLocationsUrl = url('/admin/locations');
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">MANAGE LOCATIONS</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إدارة المحافظات والمدن</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/locations/create')) ?>" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-navy-900">
                <i class="bx bx-plus-circle"></i>
                إضافة موقع
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-xl font-black text-navy-900">المحافظات</h3>
                <span class="rounded-full bg-sky-100 px-2.5 py-1 text-xs font-black text-sky-700"><?= count($governorates) ?> محافظة</span>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table min-w-full text-right text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-bold">اسم المحافظة</th>
                            <th class="px-4 py-3 font-bold">الرابط المختصر</th>
                            <th class="px-4 py-3 font-bold">تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($governorates)): ?>
                            <?php foreach ($governorates as $governorate): ?>
                                <tr class="border-t border-slate-200 bg-white">
                                    <td class="px-4 py-3">
                                        <div class="font-black text-slate-800"><?= htmlspecialchars((string) ($governorate['name'] ?? 'بدون اسم')) ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars((string) ($governorate['slug'] ?? '')) ?></td>
                                    <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars(date('Y-m-d', strtotime((string) ($governorate['created_at'] ?? 'now')))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-slate-500">لا توجد محافظات حتى الآن.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($governoratesTotalPages > 1): ?>
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-200 pt-4">
                    <a href="<?= htmlspecialchars($baseLocationsUrl . '?gpage=' . max(1, $governoratesPage - 1) . '&cpage=' . $citiesPage) ?>" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 <?= $governoratesPage <= 1 ? 'pointer-events-none opacity-40' : '' ?>">
                        السابق
                    </a>
                    <span class="text-sm font-black text-slate-600">الصفحة <?= $governoratesPage ?> من <?= $governoratesTotalPages ?></span>
                    <a href="<?= htmlspecialchars($baseLocationsUrl . '?gpage=' . min($governoratesTotalPages, $governoratesPage + 1) . '&cpage=' . $citiesPage) ?>" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 <?= $governoratesPage >= $governoratesTotalPages ? 'pointer-events-none opacity-40' : '' ?>">
                        التالي
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-xl font-black text-navy-900">المدن</h3>
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-black text-emerald-700"><?= count($cities) ?> مدينة</span>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table min-w-full text-right text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-bold">اسم المدينة</th>
                            <th class="px-4 py-3 font-bold">المحافظة</th>
                            <th class="px-4 py-3 font-bold">الرابط المختصر</th>
                            <th class="px-4 py-3 font-bold">تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cities)): ?>
                            <?php foreach ($cities as $city): ?>
                                <tr class="border-t border-slate-200 bg-white">
                                    <td class="px-4 py-3">
                                        <div class="font-black text-slate-800"><?= htmlspecialchars((string) ($city['name'] ?? 'بدون اسم')) ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars((string) ($city['governorate_name'] ?? 'غير محدد')) ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars((string) ($city['slug'] ?? '')) ?></td>
                                    <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars(date('Y-m-d', strtotime((string) ($city['created_at'] ?? 'now')))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-500">لا توجد مدن حتى الآن.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($citiesTotalPages > 1): ?>
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-200 pt-4">
                    <a href="<?= htmlspecialchars($baseLocationsUrl . '?cpage=' . max(1, $citiesPage - 1) . '&gpage=' . $governoratesPage) ?>" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 <?= $citiesPage <= 1 ? 'pointer-events-none opacity-40' : '' ?>">
                        السابق
                    </a>
                    <span class="text-sm font-black text-slate-600">الصفحة <?= $citiesPage ?> من <?= $citiesTotalPages ?></span>
                    <a href="<?= htmlspecialchars($baseLocationsUrl . '?cpage=' . min($citiesTotalPages, $citiesPage + 1) . '&gpage=' . $governoratesPage) ?>" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 <?= $citiesPage >= $citiesTotalPages ? 'pointer-events-none opacity-40' : '' ?>">
                        التالي
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
