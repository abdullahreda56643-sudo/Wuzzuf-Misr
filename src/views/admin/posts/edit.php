<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$post = $post ?? [];
$postId = (int) ($post['id'] ?? 0);
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">EDIT POST</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">تعديل المقال</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/posts')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <form method="post" action="<?= htmlspecialchars(url('/admin/posts/' . $postId . '/edit')) ?>" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="title" class="block text-sm font-black text-slate-700">عنوان المقال</label>
                    <input id="title" name="title" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="أدخل عنوان المقال" value="<?= htmlspecialchars((string) ($post['title'] ?? '')) ?>">
                </div>

                <div class="space-y-2">
                    <label for="slug" class="block text-sm font-black text-slate-700">الرابط المختصر</label>
                    <input id="slug" name="slug" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="example-post" value="<?= htmlspecialchars((string) ($post['slug'] ?? '')) ?>">
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="category" class="block text-sm font-black text-slate-700">التصنيف</label>
                    <input id="category" name="category" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="عام / وظائف / نصائح" value="<?= htmlspecialchars((string) ($post['category'] ?? '')) ?>">
                </div>

                <div class="space-y-2">
                    <label for="status" class="block text-sm font-black text-slate-700">الحالة</label>
                    <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="published" <?= (($post['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>>منشور</option>
                        <option value="draft" <?= (($post['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>مسودة</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label for="excerpt" class="block text-sm font-black text-slate-700">موجز المقال</label>
                <textarea id="excerpt" name="excerpt" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="اكتب ملخصًا مختصرًا للمقال..."><?= htmlspecialchars((string) ($post['excerpt'] ?? '')) ?></textarea>
            </div>

            <div class="space-y-2">
                <label for="content" class="block text-sm font-black text-slate-700">محتوى المقال</label>
                <textarea id="content" name="content" rows="10" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="اكتب محتوى المقال هنا..."><?= htmlspecialchars((string) ($post['content'] ?? '')) ?></textarea>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?= htmlspecialchars(url('/admin/posts')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="gold-btn rounded-2xl px-5 py-3 text-sm font-black text-navy-900">تحديث المقال</button>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
