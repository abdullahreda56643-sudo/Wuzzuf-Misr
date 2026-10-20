<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">ADD USER</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إضافة مستخدم</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/users')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <form method="post" action="<?= htmlspecialchars(url('/admin/users/create')) ?>" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="first_name" class="block text-sm font-black text-slate-700">الاسم الأول</label>
                    <input id="first_name" name="first_name" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="أدخل الاسم الأول">
                </div>

                <div class="space-y-2">
                    <label for="last_name" class="block text-sm font-black text-slate-700">اسم العائلة</label>
                    <input id="last_name" name="last_name" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="أدخل اسم العائلة">
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-black text-slate-700">البريد الإلكتروني</label>
                    <input id="email" name="email" type="email" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="example@email.com">
                </div>

                <div class="space-y-2">
                    <label for="type" class="block text-sm font-black text-slate-700">نوع الحساب</label>
                    <select id="type" name="type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="job_seeker">باحث عن عمل</option>
                        <option value="employer">صاحب عمل</option>
                        <option value="admin">إدارة</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-black text-slate-700">كلمة المرور</label>
                <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="أدخل كلمة المرور">
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?= htmlspecialchars(url('/admin/users')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="gold-btn rounded-2xl px-5 py-3 text-sm font-black text-navy-900">حفظ المستخدم</button>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>

