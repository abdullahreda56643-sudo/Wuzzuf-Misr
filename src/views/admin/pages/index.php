<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$pages = $pages ?? [];
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">MANAGE PAGES</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إدارة الصفحات</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/pages/create')) ?>" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-navy-900">
                <i class="bx bx-plus-circle"></i>
                إضافة صفحة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
        <div class="overflow-x-auto">
            <table class="admin-table min-w-full text-right text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-bold">عنوان الصفحة</th>
                        <th class="px-4 py-3 font-bold">الرابط</th>
                        <th class="px-4 py-3 font-bold">الحالة</th>
                        <th class="px-4 py-3 font-bold">تاريخ الإنشاء</th>
                        <th class="px-4 py-3 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pages)): ?>
                        <?php foreach ($pages as $page): ?>
                            <tr class="border-t border-slate-200 bg-white">
                                <td class="px-4 py-3">
                                    <div class="font-black text-slate-800"><?= htmlspecialchars((string) ($page['title'] ?? 'بدون عنوان')) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                                        <?= htmlspecialchars((string) ($page['slug'] ?? '')) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= (($page['status'] ?? 'published') === 'published') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= (($page['status'] ?? 'published') === 'published') ? 'نشط' : 'مسودة' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars(date('Y-m-d', strtotime((string) ($page['created_at'] ?? 'now')))) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="<?= htmlspecialchars(url('/admin/pages/' . (int) ($page['id'] ?? 0) . '/edit')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-black text-sky-700 transition hover:bg-sky-100">
                                            <i class="bx bx-edit-alt"></i>
                                            تعديل
                                        </a>
                                        <a href="<?= htmlspecialchars(url('/admin/pages/' . (int) ($page['id'] ?? 0) . '/delete')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-black text-red-700 transition hover:bg-red-100" onclick="return confirm('هل أنت متأكد من حذف هذه الصفحة؟')">
                                            <i class="bx bx-trash"></i>
                                            حذف
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد صفحات حتى الآن.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>

