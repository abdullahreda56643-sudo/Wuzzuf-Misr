<?php
 $user = $user ?? current_user();
 $companyName = $companyName ?? (($user['company_name'] ?? '') ?: (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'شركتي');
 $companyLocation = $companyLocation ?? 'غير محدد';
 $companyIndustry = $companyIndustry ?? 'تكنولوجيا';
 $companyStatus = $companyStatus ?? 'نشط';
 $jobCount = (int) ($jobCount ?? 0);
 $activeJobs = (int) ($activeJobs ?? 0);
 $expiredJobs = (int) ($expiredJobs ?? 0);
 $totalViews = (int) ($totalViews ?? 0);
 $applications = (int) ($applications ?? 0);
 $pendingReview = (int) ($pendingReview ?? 0);
 $renewalNeeded = (int) ($renewalNeeded ?? 0);
 $recentJobs = $recentJobs ?? [
    ['title' => 'لا توجد وظائف بعد', 'status' => 'مسودة', 'applications' => 0, 'views' => 0, 'type' => 'دوام كامل', 'renew' => false],
];
 $recentApplications = $recentApplications ?? [];
?>
<link href='https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
    .job-card{transition:all .3s cubic-bezier(.4,0,.2,1)}
    .job-card:hover{box-shadow:0 18px 50px -12px rgba(7,27,47,.18);transform:translateY(-2px)}
    .stat-pill{transition:all .25s ease}
    .stat-pill:hover{transform:translateY(-2px);box-shadow:0 10px 25px -5px rgba(0,0,0,.08)}
    .icon-badge{display:inline-flex;align-items:center;justify-content:center;border-radius:14px}
    .hover-grow{transition:transform .2s ease}
    .hover-grow:hover{transform:scale(1.05)}
    .gold-line{background:linear-gradient(90deg,transparent,#d4a64a,transparent)}

    /* Prevent horizontal scroll from decorative blurred blobs on small viewports */
    .dashboard-shell{overflow-x:hidden}

    /* ===== Tablet / mid-size tune-up ===== */
    @media (max-width: 900px){
        .stat-pill{padding:1.1rem}
    }

    /* ===== Mobile (≤640px) ===== */
    @media (max-width: 640px){
        .dashboard-shell{padding-left:0.85rem!important;padding-right:0.85rem!important}
        .mobile-header{padding:1.25rem}
        .mobile-header .mobile-title{font-size:1.7rem;line-height:1.2;word-break:break-word}
        .mobile-actions{display:flex;flex-direction:column;width:100%}
        .mobile-actions a{width:100%;justify-content:center;min-height:48px}
        .stat-pill{padding:1rem}.stat-pill p{font-size:.72rem}.stat-pill .stat-value{font-size:1.6rem}
        .quick-actions{grid-template-columns:1fr}
        .quick-actions a{min-height:56px;justify-content:center}
        .job-item-main{display:flex!important;flex-direction:column!important}
        .job-item-main > div:first-child{width:100%}
        .job-item-actions{
            display:flex!important;
            flex-direction:row!important;
            flex-wrap:nowrap!important;
            align-items:center!important;
            justify-content:space-between!important;
            width:100%;
            margin-top:.65rem;
            padding-top:.65rem;
            border-top:1px dashed #e2e8f0;
        }
        .job-item-actions a, .job-item-actions button{min-height:40px;flex-shrink:0}
        .job-item-actions > span{flex-shrink:0}
        .company-row{display:block}.company-row > span:last-child{display:block;margin-top:.5rem;text-align:right}
        .mobile-application-card{padding:1rem}.mobile-application-card .header-row{display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem}
        .icon-badge{border-radius:12px}
    }

    /* ===== Small phones (≤480px) ===== */
    @media (max-width: 480px){
        .mobile-header{padding:1.1rem;border-radius:22px}
        .mobile-header .mobile-title{font-size:1.45rem}
        .mobile-header span.inline-flex{white-space:normal;text-align:center;line-height:1.4}
        .stat-pill{border-radius:18px}
        .stat-pill .stat-value{font-size:1.4rem}
        .job-card{padding:1.1rem!important;border-radius:20px}
        h2.text-lg, h3.text-lg{font-size:1rem}
    }

    /* ===== Extra small phones (≤360px) ===== */
    @media (max-width: 360px){
        .dashboard-shell{padding-left:0.6rem!important;padding-right:0.6rem!important}
        .mobile-header .mobile-title{font-size:1.25rem}
        .stat-pill{padding:.85rem}
        .stat-pill .stat-value{font-size:1.2rem}
        .quick-actions a{padding:.85rem}
    }
</style>

<div class="min-h-screen bg-slate-100" dir="rtl">
    <div class="dashboard-shell mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <!-- Header -->
        <section class="mobile-header relative overflow-hidden rounded-[28px] bg-gradient-to-l from-[#071b2f] via-[#0d2340] to-[#173d5d] p-6 text-white shadow-[0_24px_60px_rgba(7,27,47,0.28)] sm:p-8 lg:p-10">
            <div class="absolute -top-20 -left-20 h-64 w-64 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-10 h-72 w-72 rounded-full bg-[#173d5d]/40 blur-3xl"></div>
            
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <span class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-gold-500/15 px-3 py-1 text-xs font-bold text-gold-300 ring-1 ring-gold-500/20">
                        <i class='bx bx-grid-alt'></i> لوحة تحكم صاحب العمل
                    </span>
                    <h1 class="mobile-title text-2xl font-black leading-tight sm:text-3xl lg:text-4xl">مرحباً، <?= htmlspecialchars(($user['first_name'] ?? 'صاحب') . ' ' . ($user['last_name'] ?? 'العمل')) ?></h1>
                </div>
                <div class="mobile-actions flex flex-wrap gap-3">
                    <a href="<?= htmlspecialchars(url('/employer/jobs/create')) ?>" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gold-500 px-5 py-3 text-sm font-black text-slate-900 shadow-lg shadow-gold-500/30 transition hover:bg-gold-400 hover-grow sm:w-auto">
                        <i class='bx bx-plus-circle'></i> إضافة وظيفة جديدة
                    </a>
                    <a href="<?= htmlspecialchars(url('/employer/jobs')) ?>" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/10 hover-grow sm:w-auto">
                        <i class='bx bx-list-ul'></i> إدارة الوظائف
                    </a>
                </div>
            </div>
        </section>

        <!-- Stats Grid -->
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6">
            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">إجمالي الوظائف</p>
                        <p class="stat-value mt-2 text-2xl font-black text-slate-900 sm:text-3xl"><?= $jobCount ?></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-[#eef6ff] text-xl text-[#2563eb]">
                        <i class='bx bx-file'></i>
                    </div>
                </div>
            </div>

            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">الوظائف النشطة</p>
                        <p class="stat-value mt-2 text-2xl font-black text-slate-900 sm:text-3xl"><?= $activeJobs ?></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-[#ecfdf5] text-xl text-[#16a34a]">
                        <i class='bx bx-check-circle'></i>
                    </div>
                </div>
            </div>

            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">الوظائف المنتهية</p>
                        <p class="stat-value mt-2 text-2xl font-black text-slate-900 sm:text-3xl"><?= $expiredJobs ?></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-[#fef2f2] text-xl text-[#dc2626]">
                        <i class='bx bx-time'></i>
                    </div>
                </div>
            </div>

            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">عدد المشاهدات</p>
                        <p class="stat-value mt-2 text-2xl font-black text-slate-900 sm:text-3xl"><?= number_format($totalViews) ?></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-[#f5f3ff] text-xl text-[#7c3aed]">
                        <i class='bx bx-show'></i>
                    </div>
                </div>
            </div>

            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">المتقدمون</p>
                        <p class="stat-value mt-2 text-2xl font-black text-slate-900 sm:text-3xl"><?= $applications ?></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-[#fff7ed] text-xl text-[#f97316]">
                        <i class='bx bx-group'></i>
                    </div>
                </div>
            </div>

            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">تجديد الإعلان</p>
                        <p class="stat-value mt-2 text-2xl font-black text-slate-900 sm:text-3xl"><?= $renewalNeeded ?></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-[#fef3c7] text-xl text-[#b45309]">
                        <i class='bx bx-revision'></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mt-6 grid gap-6 lg:grid-cols-[1.6fr_0.9fr]">
            
            <!-- Recent Jobs -->
            <div class="job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="icon-badge h-10 w-10 bg-gold-100 text-gold-700"><i class='bx bx-briefcase-alt-2 text-lg'></i></span>
                        <div>
                            <p class="text-xs font-bold text-gold-600">إحصائيات سريعة</p>
                            <h2 class="text-lg font-black text-slate-900 sm:text-xl">آخر الوظائف</h2>
                        </div>
                    </div>
                    <a href="<?= htmlspecialchars(url('/employer/jobs')) ?>" class="text-sm font-bold text-[#1d4ed8] transition hover:text-[#1e40af]">عرض الكل</a>
                </div>

                <div class="space-y-4">
                    <?php foreach ($recentJobs as $job): ?>
                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 p-4 transition hover:border-gold-200 hover:bg-white hover:shadow-sm">
                            <div class="job-item-main flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <p class="break-words text-base font-black text-slate-900 sm:text-lg"><?= htmlspecialchars($job['title']) ?></p>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs font-bold">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-slate-600 border border-slate-200">
                                            <i class='bx bx-time'></i> <?= htmlspecialchars($job['type']) ?>
                                        </span>
                                        <span class="inline-flex items-center gap-1 rounded-full <?= ($job['status'] === 'نشط') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?> px-2.5 py-1">
                                            <span class="h-1.5 w-1.5 rounded-full <?= ($job['status'] === 'نشط') ? 'bg-emerald-500' : 'bg-amber-500' ?>"></span> <?= htmlspecialchars($job['status']) ?>
                                        </span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-200 text-slate-700 px-2.5 py-1">
                                            <i class='bx bx-show'></i> <?= (int) $job['views'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="job-item-actions flex items-center justify-between gap-3 sm:justify-end">
                                    <span class="inline-flex items-center gap-1 text-sm font-bold text-slate-500">
                                        <i class='bx bx-group'></i> <?= (int) $job['applications'] ?>
                                    </span>
                                    <?php if (($job['renew'] ?? false)): ?>
                                        <form method="POST" action="<?= htmlspecialchars(url('/employer/jobs/' . ($job['id'] ?? 0) . '/renew')) ?>" class="inline-block">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-gold-500 px-3 py-2 text-xs font-black text-slate-900 hover-grow">
                                                <i class='bx bx-revision'></i> تجديد
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?= htmlspecialchars(url('/employer/jobs')) ?>" class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3 py-2 text-xs font-black text-white hover-grow">
                                            <i class='bx bx-dots-horizontal-rounded'></i> التفاصيل
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Company Profile -->
                <div class="job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="icon-badge h-10 w-10 bg-[#071b2f] text-gold-300"><i class='bx bx-building-house text-lg'></i></span>
                            <div>
                                <p class="text-xs font-bold text-gold-600">ملف الشركة</p>
                                <h3 class="text-lg font-black text-slate-900 sm:text-xl"><?= htmlspecialchars($companyName) ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm text-slate-600">
                        <div class="company-row flex items-center justify-between gap-3 rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-100">
                            <span class="flex items-center gap-2 text-slate-500"><i class='bx bx-map text-slate-400'></i> الموقع</span>
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($companyLocation) ?></span>
                        </div>
                        <div class="company-row flex items-center justify-between gap-3 rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-100">
                            <span class="flex items-center gap-2 text-slate-500"><i class='bx bx-category text-slate-400'></i> النشاط</span>
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($companyIndustry) ?></span>
                        </div>
                        <div class="company-row flex items-center justify-between gap-3 rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-100">
                            <span class="flex items-center gap-2 text-slate-500"><i class='bx bx-shield text-slate-400'></i> الحالة</span>
                            <span class="inline-flex items-center gap-1.5 font-bold <?= $companyStatus === 'نشط' ? 'text-emerald-600' : 'text-amber-600' ?>">
                                <span class="h-2 w-2 rounded-full <?= $companyStatus === 'نشط' ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' ?>"></span>
                                <?= htmlspecialchars($companyStatus) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="icon-badge h-10 w-10 bg-gold-100 text-gold-700"><i class='bx bx-bolt text-lg'></i></span>
                        <h3 class="text-lg font-black text-slate-900 sm:text-xl">إجراءات سريعة</h3>
                    </div>
                    <div class="quick-actions grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <a href="<?= htmlspecialchars(url('/employer/jobs/create')) ?>" class="inline-flex flex-col items-center gap-1.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center text-xs font-bold text-slate-800 transition hover:bg-white hover:border-gold-200 hover:shadow-sm">
                            <i class='bx bx-plus-circle text-xl text-gold-600'></i> إضافة وظيفة
                        </a>
                        <a href="<?= htmlspecialchars(url('/employer/jobs')) ?>" class="inline-flex flex-col items-center gap-1.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center text-xs font-bold text-slate-800 transition hover:bg-white hover:border-gold-200 hover:shadow-sm">
                            <i class='bx bx-edit text-xl text-gold-600'></i> تعديل الوظائف
                        </a>
                        <a href="<?= htmlspecialchars(url('/employer/applications')) ?>" class="inline-flex flex-col items-center gap-1.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center text-xs font-bold text-slate-800 transition hover:bg-white hover:border-gold-200 hover:shadow-sm">
                            <i class='bx bx-user-check text-xl text-gold-600'></i> المتقدمون
                        </a>
                        <a href="<?= htmlspecialchars(url('/employer/settings')) ?>" class="inline-flex flex-col items-center gap-1.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center text-xs font-bold text-slate-800 transition hover:bg-white hover:border-gold-200 hover:shadow-sm">
                            <i class='bx bx-cog text-xl text-gold-600'></i> الإعدادات
                        </a>
                        <a href="<?= htmlspecialchars(url('/employer/jobs')) ?>" class="sm:col-span-2 inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center text-xs font-bold text-slate-800 transition hover:bg-white hover:border-gold-200 hover:shadow-sm">
                            <i class='bx bx-revision text-xl text-gold-600'></i> تجديد الإعلانات
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="mt-8 job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="icon-badge h-10 w-10 bg-emerald-100 text-emerald-700"><i class='bx bx-user-pin text-lg'></i></span>
                    <div>
                        <p class="text-xs font-bold text-gold-600">المتقدمون الجدد</p>
                        <h2 class="text-lg font-black text-slate-900 sm:text-xl">طلبات التقديم الأخيرة</h2>
                    </div>
                </div>
                <a href="<?= htmlspecialchars(url('/employer/applications')) ?>" class="text-sm font-bold text-[#1d4ed8] transition hover:text-[#1e40af]">عرض الكل</a>
            </div>

            <!-- Desktop Table -->
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full text-right text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-4 py-3 font-bold">الاسم</th>
                            <th class="px-4 py-3 font-bold">الوظيفة</th>
                            <th class="px-4 py-3 font-bold">الحالة</th>
                            <th class="px-4 py-3 font-bold">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentApplications as $app): ?>
                            <tr class="border-b border-slate-100 last:border-0 transition hover:bg-slate-50">
                                <td class="px-4 py-4 font-bold text-slate-800"><?= htmlspecialchars($app['name']) ?></td>
                                <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($app['job']) ?></td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold <?= $app['status'] === 'تمت الموافقة' ? 'bg-emerald-100 text-emerald-700' : ($app['status'] === 'تمت المقابلة' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') ?>"><?= htmlspecialchars($app['status']) ?></span>
                                </td>
                                <td class="px-4 py-4">
                                    <a href="<?= htmlspecialchars(url('/employer/applications')) ?>" class="inline-flex items-center gap-1 font-bold text-[#1d4ed8] hover:underline">
                                        عرض <i class='bx bx-left-arrow-alt'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="space-y-3 md:hidden">
                <?php foreach ($recentApplications as $app): ?>
                    <div class="mobile-application-card rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="header-row mb-2 flex items-center justify-between gap-3">
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($app['name']) ?></span>
                            <a href="<?= htmlspecialchars(url('/employer/applications')) ?>" class="text-sm font-bold text-[#1d4ed8]">عرض التفاصيل</a>
                        </div>
                        <p class="mb-3 text-sm text-slate-600"><i class='bx bx-briefcase text-slate-400'></i> <?= htmlspecialchars($app['job']) ?></p>
                        <span class="rounded-full px-2.5 py-1 text-xs font-bold <?= $app['status'] === 'تمت الموافقة' ? 'bg-emerald-100 text-emerald-700' : ($app['status'] === 'تمت المقابلة' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') ?>"><?= htmlspecialchars($app['status']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>