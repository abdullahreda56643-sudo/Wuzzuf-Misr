<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';
$requests = $requests ?? [];
$statusLabels = ['pending' => 'جديد', 'contacted' => 'تم التواصل', 'closed' => 'مغلق'];
$statusClasses = ['pending' => 'bg-amber-100 text-amber-700', 'contacted' => 'bg-sky-100 text-sky-700', 'closed' => 'bg-slate-100 text-slate-600'];
?>
<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6"><p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">EMPLOYMENT REQUESTS</p><h2 class="mt-1 text-2xl font-black text-navy-900">طلبات توظيف الباحثين</h2><p class="mt-2 text-sm font-bold text-slate-500">تظهر أرقام الهواتف هنا للإدارة فقط، ولا تظهر في الموقع العام.</p></div>
    <div class="overflow-x-auto rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
        <table class="admin-table min-w-full text-right text-sm"><thead class="bg-slate-50 text-slate-600"><tr><th class="px-4 py-3 font-bold">طالب التوظيف</th><th class="px-4 py-3 font-bold">الباحث عن عمل</th><th class="px-4 py-3 font-bold">رقم الباحث</th><th class="px-4 py-3 font-bold">الرسالة</th><th class="px-4 py-3 font-bold">الحالة</th><th class="px-4 py-3 font-bold">الإجراء</th></tr></thead><tbody>
            <?php if (empty($requests)): ?><tr><td colspan="6" class="px-4 py-10 text-center font-bold text-slate-500">لا توجد طلبات توظيف حتى الآن.</td></tr><?php endif; ?>
            <?php foreach ($requests as $request): ?>
                <?php $status = (string) ($request['status'] ?? 'pending'); ?>
                <tr class="border-t border-slate-200 align-top"><td class="px-4 py-4"><p class="font-black text-slate-800"><?= htmlspecialchars(trim(($request['requester_first_name'] ?? '') . ' ' . ($request['requester_last_name'] ?? ''))) ?></p><p class="mt-1 text-xs text-slate-500"><?= htmlspecialchars($request['requester_email'] ?? '') ?></p><p class="mt-1 text-xs font-bold text-slate-600"><?= htmlspecialchars($request['requester_phone'] ?? 'بدون هاتف') ?></p></td><td class="px-4 py-4 font-black text-navy-900"><?= htmlspecialchars(trim(($request['seeker_first_name'] ?? '') . ' ' . ($request['seeker_last_name'] ?? ''))) ?></td><td class="px-4 py-4"><a href="tel:<?= htmlspecialchars($request['seeker_phone'] ?? '') ?>" class="font-black text-emerald-700 hover:underline"><?= htmlspecialchars($request['seeker_phone'] ?? 'غير مسجل') ?></a></td><td class="max-w-xs whitespace-pre-line px-4 py-4 text-slate-600"><?= htmlspecialchars($request['message'] ?? 'بدون رسالة') ?></td><td class="px-4 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-black <?= $statusClasses[$status] ?? $statusClasses['pending'] ?>"><?= htmlspecialchars($statusLabels[$status] ?? $status) ?></span><p class="mt-2 whitespace-nowrap text-xs text-slate-400"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($request['created_at'] ?? 'now'))) ?></p></td><td class="px-4 py-4"><div class="flex flex-col gap-2"><form method="POST" action="<?= htmlspecialchars(url('/admin/employment-requests/' . (int) $request['id'] . '/status/contacted')) ?>"><button class="whitespace-nowrap rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-black text-sky-700 hover:bg-sky-100" type="submit">تم التواصل</button></form><form method="POST" action="<?= htmlspecialchars(url('/admin/employment-requests/' . (int) $request['id'] . '/status/closed')) ?>"><button class="whitespace-nowrap rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-black text-slate-600 hover:bg-slate-100" type="submit">إغلاق الطلب</button></form></div></td></tr>
            <?php endforeach; ?>
        </tbody></table>
    </div>
</section>
<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
