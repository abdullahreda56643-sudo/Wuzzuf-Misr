<?php
$notifications = $notifications ?? [];
$typeLabels = [
    'job_applied' => ['label' => 'تقديم على وظيفة', 'class' => 'bg-blue-100 text-blue-700'],
    'job_rejected' => ['label' => 'رفض طلب', 'class' => 'bg-red-100 text-red-700'],
    'job_shortlisted' => ['label' => 'تم ترشيحك', 'class' => 'bg-emerald-100 text-emerald-700'],
    'job_accepted' => ['label' => 'تم قبول طلبك', 'class' => 'bg-green-100 text-green-700'],
    'job_expired' => ['label' => 'انتهاء فرصة', 'class' => 'bg-amber-100 text-amber-700'],
    'company_message' => ['label' => 'رسالة من الشركة', 'class' => 'bg-violet-100 text-violet-700'],
    'system' => ['label' => 'إشعار نظام', 'class' => 'bg-slate-200 text-slate-700'],
    'payment' => ['label' => 'إشعار دفع', 'class' => 'bg-cyan-100 text-cyan-700'],
    'other' => ['label' => 'إشعار', 'class' => 'bg-slate-200 text-slate-700'],
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'الإشعارات') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Cairo', 'sans-serif'] },
                    colors: {
                        navy: { 900: '#071b2f', 800: '#0d2340', 700: '#12355d' },
                        gold: { 500: '#c79734', 600: '#b47a18', 700: '#966a16' }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-[28px] bg-gradient-to-r from-navy-900 to-navy-800 p-6 text-white shadow-[0_18px_45px_rgba(7,27,47,0.18)]">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm text-gold-500">الباحث عن العمل</p>
                    <h1 class="mt-1 text-3xl font-black">الإشعارات</h1>
                </div>
                <div class="flex gap-3">
                    <a href="<?= htmlspecialchars(url('/dashboard')) ?>" class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-bold transition hover:bg-white/10">لوحة التحكم</a>
                    <a href="<?= htmlspecialchars(url('/profile')) ?>" class="rounded-xl bg-gold-500 px-4 py-2 text-sm font-bold text-navy-900 transition hover:bg-gold-400">الملف الشخصي</a>
                </div>
            </div>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="rounded-[28px] border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-2xl">🔔</div>
                <h2 class="mt-5 text-2xl font-black text-navy-900">لا توجد إشعارات حتى الآن</h2>
                <p class="mt-3 text-sm text-slate-500">سوف تظهر هنا كل التحديثات والمراسلات المتعلقة بطلباتك.</p>
                <a href="<?= htmlspecialchars(url('/jobs')) ?>" class="mt-6 inline-block rounded-2xl bg-gold-500 px-5 py-3 text-sm font-black text-navy-900 transition hover:bg-gold-400">استعراض الوظائف</a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($notifications as $notification): ?>
                    <?php
                    $typeKey = (string) ($notification['type'] ?? 'system');
                    $typeMeta = $typeLabels[$typeKey] ?? $typeLabels['system'];
                    $isUnread = (int) ($notification['is_read'] ?? 0) === 0;
                    ?>
                    <div class="rounded-[28px] border <?= $isUnread ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-white' ?> p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-navy-900 text-xl text-white">
                                    <i class='bx bx-bell'></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold <?= htmlspecialchars($typeMeta['class']) ?>"><?= htmlspecialchars($typeMeta['label']) ?></span>
                                        <?php if ($isUnread): ?>
                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-700">جديد</span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="text-xl font-black text-navy-900"><?= htmlspecialchars((string) ($notification['title'] ?? 'إشعار')) ?></h3>
                                    <p class="mt-3 text-sm leading-7 text-slate-700">
                                        <?= nl2br(htmlspecialchars((string) ($notification['message'] ?? ''))) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col items-start gap-3 lg:items-end">
                                <span class="text-xs font-bold text-slate-500"><?= !empty($notification['created_at']) ? date('Y-m-d H:i', strtotime($notification['created_at'])) : '' ?></span>
                                <?php if (!empty($notification['action_url'])): ?>
                                    <a href="<?= htmlspecialchars(url((string) $notification['action_url'])) ?>" class="rounded-xl bg-gold-500 px-4 py-2 text-xs font-black text-navy-900 transition hover:bg-gold-400">عرض التفاصيل</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
