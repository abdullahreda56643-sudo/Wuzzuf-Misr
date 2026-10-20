<?php
$user = $user ?? current_user();
$stats = $stats ?? [
    'jobs_applied' => 0,
    'saved_jobs' => 0,
    'notifications' => 0,
];
$profileSkills = $user['skills'] ?? '[]';
if (is_string($profileSkills)) {
    $decodedSkills = json_decode($profileSkills, true);
    $profileSkills = is_array($decodedSkills) ? $decodedSkills : [];
} elseif (!is_array($profileSkills)) {
    $profileSkills = [];
}
$skillsText = implode(', ', array_slice($profileSkills, 0, 8));
$fullName = trim((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
$displayName = $fullName !== '' ? $fullName : 'مستخدم';
$profileStatus = !empty($user['qualification']) ? 'ملفك الشخصي مكتمل' : 'اكمل ملفك الشخصي';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'لوحة التحكم') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif']
                    },
                    boxShadow: {
                        soft: '0 18px 40px rgba(15, 39, 58, 0.08)',
                        glow: '0 18px 35px rgba(199, 151, 52, 0.18)'
                    },
                    colors: {
                        navy: { 900: '#071b2f', 800: '#0d2340', 700: '#12355d' },
                        gold: { 500: '#c79734', 600: '#b47a18', 700: '#966a16' },
                        mist: '#f4f8fc'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        html, body { font-family: 'Cairo', sans-serif; }
        body { background: linear-gradient(180deg, #edf4fb 0%, #f8fafc 100%); }
        .glass-card { backdrop-filter: blur(8px); }
        @media (max-width: 640px) {
            .stat-number { font-size: 1.8rem; }
            .hero-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <div class="mx-auto max-w-7xl px-3 py-5 sm:px-5 lg:px-8 lg:py-8">

        <header class="mb-6 overflow-hidden rounded-[30px] bg-gradient-to-r from-navy-900 via-[#0d2340] to-[#12355d] p-4 text-white shadow-soft sm:p-6 lg:p-7">
            <div class="hero-grid grid gap-4 lg:grid-cols-[1.3fr_0.7fr] lg:items-center">
                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-bold text-gold-500">
                        <span class="h-2 w-2 rounded-full bg-gold-500"></span>
                        لوحة الباحث عن العمل
                    </div>
                    <h1 class="text-2xl font-black sm:text-3xl lg:text-4xl">مرحباً، <?= htmlspecialchars($displayName) ?></h1>
                    <p class="mt-2 max-w-xl text-sm leading-7 text-slate-200 sm:text-base">
                        لديك فرصة للمتابعة اليومية على طلباتك، الوظائف المحفوظة، والملف الشخصي في مكان واحد.
                    </p>
                </div>

                <div class="glass-card rounded-[22px] border border-white/10 bg-white/5 p-4 shadow-glow">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-lg font-black text-gold-500">
                            <?= htmlspecialchars(mb_substr($displayName, 0, 1, 'UTF-8')) ?>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-slate-300">حالة الملف</p>
                            <p class="text-sm font-black text-white"><?= htmlspecialchars($profileStatus) ?></p>
                        </div>
                    </div>

                    <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-white/10">
                        <div class="h-full rounded-full bg-gradient-to-r from-gold-500 to-gold-400" style="width: <?= !empty($user['qualification']) ? '78%' : '42%' ?>;"></div>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-xs text-slate-300">
                        <span>ملف شخصي</span>
                        <span><?= !empty($user['qualification']) ? '78%' : '42%' ?></span>
                    </div>
                </div>
            </div>
        </header>

        <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-soft transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-500">طلبات التقديم</p>
                        <p class="stat-number mt-3 text-3xl font-black text-navy-900"><?= (int) ($stats['jobs_applied'] ?? 0) ?></p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-xl text-blue-700">
                        <i class='bx bx-file'></i>
                    </div>
                </div>
            </div>

            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-soft transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-500">الوظائف المحفوظة</p>
                        <p class="stat-number mt-3 text-3xl font-black text-navy-900"><?= (int) ($stats['saved_jobs'] ?? 0) ?></p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-xl text-emerald-700">
                        <i class='bx bx-bookmark'></i>
                    </div>
                </div>
            </div>

            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-soft transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-500">الإشعارات</p>
                        <p class="stat-number mt-3 text-3xl font-black text-navy-900"><?= (int) ($stats['notifications'] ?? 0) ?></p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-100 text-xl text-amber-700">
                        <i class='bx bx-bell'></i>
                    </div>
                </div>
            </div>

            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-soft transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-500">ملفي الشخصي</p>
                        <p class="stat-number mt-3 text-3xl font-black text-navy-900"><?= !empty($user['qualification']) ? 'مكتمل' : 'قيد التحديث' ?></p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-xl text-violet-700">
                        <i class='bx bx-user-circle'></i>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5 lg:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold tracking-[0.2em] text-slate-500">ملفك الشخصي</p>
                        <h2 class="mt-1 text-2xl font-black text-navy-900">نظرة سريعة</h2>
                    </div>
                    <a href="<?= htmlspecialchars(url('/profile')) ?>" class="inline-flex items-center justify-center rounded-xl bg-gold-500 px-4 py-2.5 text-sm font-black text-navy-900 shadow-glow transition hover:bg-gold-400">
                        تحديث البيانات
                    </a>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500">الاسم الكامل</p>
                        <p class="mt-3 text-lg font-black text-navy-900"><?= htmlspecialchars($displayName) ?></p>
                    </div>

                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500">الهاتف</p>
                        <p class="mt-3 text-lg font-black text-navy-900"><?= htmlspecialchars($user['phone'] ?? 'غير موجود') ?></p>
                    </div>

                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500">المؤهل</p>
                        <p class="mt-3 text-lg font-black text-navy-900"><?= htmlspecialchars($user['qualification'] ?? 'غير محدد') ?></p>
                    </div>

                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500">حالة السيرة</p>
                        <p class="mt-3 text-lg font-black text-navy-900"><?= !empty($user['experience']) ? 'موجودة' : 'غير مكتملة' ?></p>
                    </div>

                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-4 md:col-span-2">
                        <p class="text-xs font-bold text-slate-500">الخبرة</p>
                        <p class="mt-3 text-sm leading-7 text-slate-700"><?= htmlspecialchars($user['experience'] ?: 'لم يتم إدخال الخبرة بعد') ?></p>
                    </div>

                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-4 md:col-span-2">
                        <p class="text-xs font-bold text-slate-500">المهارات</p>
                        <p class="mt-3 text-sm leading-7 text-slate-700"><?= htmlspecialchars($skillsText ?: 'لم يتم إدخال المهارات بعد') ?></p>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft">
                    <h2 class="text-xl font-black text-navy-900">إجراءات سريعة</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <a href="<?= htmlspecialchars(url('/profile')) ?>" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-sm font-bold text-navy-900 transition hover:bg-slate-100">
                            <span>ملفي الشخصي</span>
                            <i class='bx bx-user text-lg'></i>
                        </a>
                        <a href="<?= htmlspecialchars(url('/profile/saved-jobs')) ?>" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-sm font-bold text-navy-900 transition hover:bg-slate-100">
                            <span>الوظائف المحفوظة</span>
                            <i class='bx bx-bookmark text-lg'></i>
                        </a>
                        <a href="<?= htmlspecialchars(url('/profile/applications')) ?>" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-sm font-bold text-navy-900 transition hover:bg-slate-100">
                            <span>طلباتي</span>
                            <i class='bx bx-task text-lg'></i>
                        </a>
                        <a href="<?= htmlspecialchars(url('/profile/notifications')) ?>" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-sm font-bold text-navy-900 transition hover:bg-slate-100">
                            <span>الإشعارات</span>
                            <i class='bx bx-bell text-lg'></i>
                        </a>
                        <a href="<?= htmlspecialchars(url('/jobs')) ?>" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-sm font-bold text-navy-900 transition hover:bg-slate-100">
                            <span>تصفح الوظائف</span>
                            <i class='bx bx-search-alt-2 text-lg'></i>
                        </a>
                    </div>
                </div>

                <?php $recentNotifications = $recentNotifications ?? []; ?>
                <?php if (!empty($recentNotifications)): ?>
                    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h2 class="text-xl font-black text-navy-900">آخر الإشعارات</h2>
                            <a href="<?= htmlspecialchars(url('/profile/notifications')) ?>" class="text-sm font-bold text-navy-900 underline-offset-4 hover:underline">عرض الكل</a>
                        </div>
                        <div class="space-y-3">
                            <?php foreach (array_slice($recentNotifications, 0, 4) as $notification): ?>
                                <?php
                                $notificationType = (string) ($notification['type'] ?? 'system');
                                $typeLabels = [
                                    'job_applied' => 'تقديم على وظيفة',
                                    'job_rejected' => 'رفض طلب',
                                    'job_shortlisted' => 'تم ترشيحك',
                                    'job_accepted' => 'تم قبول طلبك',
                                    'job_expired' => 'انتهاء فرصة',
                                    'company_message' => 'رسالة من الشركة',
                                    'system' => 'إشعار نظام',
                                    'payment' => 'إشعار دفع',
                                    'other' => 'إشعار',
                                ];
                                $typeLabel = $typeLabels[$notificationType] ?? 'إشعار';
                                $isUnread = !empty($notification['is_read']) ? false : true;
                                ?>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 flex h-8 w-8 items-center justify-center rounded-full bg-navy-900 text-xs font-black text-white">
                                            <i class='bx bx-bell'></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-black text-navy-900"><?= htmlspecialchars((string) ($notification['title'] ?? $typeLabel)) ?></p>
                                                <?php if ($isUnread): ?>
                                                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mt-1 text-xs font-bold text-slate-500"><?= htmlspecialchars($typeLabel) ?></p>
                                            <p class="mt-2 text-sm leading-6 text-slate-700">
                                                <?= nl2br(htmlspecialchars((string) ($notification['message'] ?? ''))) ?>
                                            </p>
                                            <p class="mt-2 text-[11px] text-slate-400"><?= !empty($notification['created_at']) ? date('Y-m-d H:i', strtotime($notification['created_at'])) : '' ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="rounded-[28px] border border-dashed border-slate-300 bg-slate-50 p-5 shadow-soft">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-navy-900">CV</h2>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black text-emerald-700">مرفوع</span>
                    </div>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        أضف أو عدّل سيرتك الذاتية عند الحاجة، ثم تابع التحسينات على ملفك الشخصي.
                    </p>
                    <a href="<?= htmlspecialchars(url('/profile')) ?>" class="mt-4 inline-flex items-center justify-center rounded-xl bg-navy-900 px-4 py-2.5 text-sm font-black text-white transition hover:bg-navy-800">
                        إدارة السيرة الذاتية
                    </a>
                </div>
            </aside>
        </section>

        <section class="mt-6 rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5 lg:p-6">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold tracking-[0.2em] text-slate-500">أحدث الفرص</p>
                    <h2 class="mt-1 text-2xl font-black text-navy-900">وظائف مناسبة لك</h2>
                </div>
                <a href="<?= htmlspecialchars(url('/jobs')) ?>" class="text-sm font-bold text-navy-900 underline-offset-4 hover:underline">عرض الكل</a>
            </div>

            <div class="space-y-4">
                <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                        <?php
                        $jobTitle = htmlspecialchars((string) ($job['title'] ?? 'وظيفة جديدة'));
                        $companyName = htmlspecialchars((string) ($job['company_name'] ?? 'شركة'));
                        $location = htmlspecialchars((string) ($job['governorate_name'] ?? $job['location'] ?? 'مصر'));
                        $jobType = (string) ($job['job_type'] ?? 'full-time');
                        $jobTypeLabel = match ($jobType) {
                            'full-time' => 'دوام كامل',
                            'part-time' => 'دوام جزئي',
                            'contract' => 'عقد',
                            'remote' => 'عن بُعد',
                            'internship' => 'تدريب',
                            default => $jobType,
                        };
                        $badgeClass = match ((int) ($job['id'] ?? 0) % 3) {
                            0 => 'bg-emerald-100 text-emerald-700',
                            1 => 'bg-blue-100 text-blue-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                        $badgeText = ((int) ($job['id'] ?? 0) % 2 === 0) ? 'متاح الآن' : 'فرصة جديدة';
                        $companyLogo = (string) ($job['company_logo'] ?? '');
                        $companyLogoUrl = $companyLogo !== '' ? asset($companyLogo) : '';
                        $iconClasses = ['bg-blue-50 text-blue-600', 'bg-pink-50 text-pink-600', 'bg-green-50 text-green-600', 'bg-orange-50 text-orange-600', 'bg-purple-50 text-purple-600'];
                        $iconIndex = (int) (($job['id'] ?? 0) % count($iconClasses));
                        $initials = mb_substr((string) ($job['company_name'] ?? 'ش'), 0, 1, 'UTF-8');
                        ?>
                        <div class="flex flex-col gap-4 rounded-[22px] border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-3">
                                <?php if ($companyLogoUrl !== ''): ?>
                                    <img src="<?= htmlspecialchars($companyLogoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $companyName ?>" class="h-14 w-14 flex-shrink-0 rounded-xl object-cover ring-2 ring-gold-100 shadow-sm" />
                                <?php else: ?>
                                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl <?= $iconClasses[$iconIndex] ?> font-black text-lg">
                                        <?= $initials ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <p class="text-lg font-black text-navy-900"><?= $jobTitle ?></p>
                                    <p class="mt-1 text-sm text-slate-500"><?= $companyName ?> • <?= $location ?></p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-slate-700 border border-slate-200"><?= htmlspecialchars($jobTypeLabel) ?></span>
                                        <span class="text-[11px] text-slate-500"><?= htmlspecialchars((string) ($job['created_at'] ?? '')) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold <?= $badgeClass ?>"><?= $badgeText ?></span>
                                <a href="<?= htmlspecialchars(url('/jobs/' . (int) ($job['id'] ?? 0))) ?>" class="inline-flex items-center justify-center rounded-xl bg-gold-500 px-3.5 py-2 text-xs font-black text-navy-900 transition hover:bg-gold-400">عرض التفاصيل</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="rounded-[22px] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-600">
                        لا توجد وظائف مناسبة لك في الوقت الحالي.
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php $acceptanceNotifications = $acceptanceNotifications ?? []; ?>
        <?php if (!empty($acceptanceNotifications)): ?>
            <section class="mt-6 rounded-[28px] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-4 shadow-soft sm:p-5 lg:p-6">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl text-emerald-600">
                            <i class='bx bx-check-circle'></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold tracking-[0.2em] text-emerald-600">رسائل القبول</p>
                            <h2 class="mt-1 text-2xl font-black text-emerald-900">طلباتك المقبولة</h2>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <?php foreach (array_slice($acceptanceNotifications, 0, 5) as $notification): ?>
                        <div class="rounded-[20px] border border-emerald-200 bg-white p-4 transition hover:shadow-md hover:border-emerald-300">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                            <i class='bx bx-check text-sm'></i> تمت الموافقة
                                        </span>
                                        <span class="text-xs text-slate-500">
                                            <?= !empty($notification['created_at']) ? date('Y-m-d H:i', strtotime($notification['created_at'])) : '' ?>
                                        </span>
                                    </div>
                                    <p class="font-bold text-navy-900 mb-2">
                                        <?= htmlspecialchars((string) ($notification['title'] ?? 'تحديث حالة الطلب')) ?>
                                    </p>
                                    <p class="text-sm leading-7 text-slate-700 bg-emerald-50 rounded-xl p-3 border border-emerald-100">
                                        <?= nl2br(htmlspecialchars((string) ($notification['message'] ?? ''))) ?>
                                    </p>
                                    <div class="mt-3 pt-3 border-t border-emerald-100">
                                        <a href="<?= htmlspecialchars(url('/profile/applications')) ?>" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:underline">
                                            <i class='bx bx-right-arrow-alt'></i> عرض التفاصيل
                                        </a>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl text-emerald-600">
                                        <i class='bx bx-trophy'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($acceptanceNotifications) > 5): ?>
                    <div class="mt-4 text-center">
                        <a href="<?= htmlspecialchars(url('/profile/applications')) ?>" class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:underline">
                            عرض جميع الرسائل (<?= count($acceptanceNotifications) ?>)
                            <i class='bx bx-right-arrow-alt'></i>
                        </a>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</body>
</html>
