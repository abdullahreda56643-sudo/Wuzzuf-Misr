<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$posts = $posts ?? [];
$search = $search ?? '';
$page = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$totalPosts = (int) ($totalPosts ?? 0);
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">MANAGE POSTS</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إدارة المقالات</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/posts/create')) ?>" class="gold-btn inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-black text-navy-900">
                <i class="bx bx-plus-circle"></i>
                إضافة مقال
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
        <form method="GET" action="<?= htmlspecialchars(url('/admin/posts')) ?>" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex w-full max-w-xl items-center gap-3">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="بحث عن مقال..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                <button type="submit" class="gold-btn rounded-2xl px-4 py-3 text-sm font-black text-navy-900">بحث</button>
            </div>
            <a href="<?= htmlspecialchars(url('/admin/posts')) ?>" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-black text-slate-700">إعادة تعيين</a>
        </form>

        <div class="mb-4 flex items-center justify-between text-sm text-slate-500">
            <span>إجمالي المقالات: <span class="font-black text-slate-700"><?= (int) $totalPosts ?></span></span>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table min-w-full text-right text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-bold">العنوان</th>
                        <th class="px-4 py-3 font-bold">التصنيف</th>
                        <th class="px-4 py-3 font-bold">الحالة</th>
                        <th class="px-4 py-3 font-bold">تاريخ الإنشاء</th>
                        <th class="px-4 py-3 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr class="border-t border-slate-200 bg-white">
                                <td class="px-4 py-3">
                                    <div class="font-black text-slate-800"><?= htmlspecialchars((string) ($post['title'] ?? 'بدون عنوان')) ?></div>
                                    <div class="mt-1 text-xs text-slate-500">/<?= htmlspecialchars((string) ($post['slug'] ?? '')) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?= htmlspecialchars((string) ($post['category'] ?? 'عام')) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= (($post['status'] ?? 'draft') === 'published') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= (($post['status'] ?? 'draft') === 'published') ? 'منشور' : 'مسودة' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars(date('Y-m-d', strtotime((string) ($post['created_at'] ?? 'now')))) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="<?= htmlspecialchars(url('/admin/posts/' . (int) ($post['id'] ?? 0) . '/edit')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-black text-sky-700 transition hover:bg-sky-100">
                                            <i class="bx bx-edit-alt"></i>
                                            تعديل
                                        </a>
                                        <a href="<?= htmlspecialchars(url('/admin/posts/' . (int) ($post['id'] ?? 0) . '/delete')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-black text-red-700 transition hover:bg-red-100" onclick="return confirm('هل أنت متأكد من حذف هذا المقال؟')">
                                            <i class="bx bx-trash"></i>
                                            حذف
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد مقالات حتى الآن.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                <?php if ($page > 1): ?>
                    <a href="<?= htmlspecialchars(url('/admin/posts') . '?q=' . urlencode($search) . '&page=' . ($page - 1)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="السابق">
                        <i class="bx bx-chevron-right"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= htmlspecialchars(url('/admin/posts') . '?q=' . urlencode($search) . '&page=' . $i) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black <?= $i === $page ? 'bg-gold-500 text-white' : 'text-slate-600 hover:bg-slate-100' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= htmlspecialchars(url('/admin/posts') . '?q=' . urlencode($search) . '&page=' . ($page + 1)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="التالي">
                        <i class="bx bx-chevron-left"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
