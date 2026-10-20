<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$company = $company ?? [];
$companyId = (int) ($company['id'] ?? 0);
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">EDIT COMPANY</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">تعديل الشركة</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/companies')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <form method="post" action="<?= htmlspecialchars(url('/admin/companies/' . $companyId . '/edit')) ?>" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-black text-slate-700">اسم الشركة</label>
                    <input id="name" type="text" value="<?= htmlspecialchars((string) ($company['name'] ?? '')) ?>" disabled class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-bold text-slate-800 outline-none">
                </div>

                <div class="space-y-2">
                    <label for="status" class="block text-sm font-black text-slate-700">حالة الشركة</label>
                    <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="active" <?= (($company['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>نشط</option>
                        <option value="inactive" <?= (($company['status'] ?? 'inactive') === 'inactive') ? 'selected' : '' ?>>غير نشط</option>
                    </select>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="verification_status" class="block text-sm font-black text-slate-700">حالة التوثيق</label>
                    <select id="verification_status" name="verification_status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="pending" <?= (($company['verification_status'] ?? 'pending') === 'pending') ? 'selected' : '' ?>>قيد المراجعة</option>
                        <option value="verified" <?= (($company['verification_status'] ?? 'pending') === 'verified') ? 'selected' : '' ?>>موثقة</option>
                        <option value="rejected" <?= (($company['verification_status'] ?? 'pending') === 'rejected') ? 'selected' : '' ?>>مرفوضة</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-black text-slate-700">البريد الإلكتروني</label>
                    <input id="email" type="text" value="<?= htmlspecialchars((string) ($company['email'] ?? '')) ?>" disabled class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-bold text-slate-800 outline-none">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <button type="submit" name="verification_status" value="verified" class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-black text-emerald-700 transition hover:bg-emerald-100">
                    <i class="bx bx-check-circle"></i>
                    تأكيد التوثيق
                </button>

                <button type="submit" name="verification_status" value="rejected" class="inline-flex items-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-black text-red-700 transition hover:bg-red-100">
                    <i class="bx bx-x-circle"></i>
                    رفض التوثيق
                </button>

                <a href="<?= htmlspecialchars(url('/admin/companies')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="gold-btn rounded-2xl px-5 py-3 text-sm font-black text-navy-900">تحديث الشركة</button>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>

