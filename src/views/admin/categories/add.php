<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">NEW CATEGORY</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إضافة تصنيف جديد</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/categories')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <form method="post" action="<?= htmlspecialchars(url('/admin/categories/create')) ?>" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-black text-slate-700">اسم التصنيف</label>
                    <input id="name" name="name" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="أدخل اسم التصنيف" value="">
                </div>

                <div class="space-y-2">
                    <label for="status" class="block text-sm font-black text-slate-700">الحالة</label>
                    <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label for="description" class="block text-sm font-black text-slate-700">الوصف</label>
                <textarea id="description" name="description" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="اكتب وصفاً مختصراً للتصنيف..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?= htmlspecialchars(url('/admin/categories')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="gold-btn rounded-2xl px-5 py-3 text-sm font-black text-navy-900">حفظ التصنيف</button>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>

