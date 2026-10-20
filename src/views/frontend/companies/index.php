<?php
 $companies = $companies ?? [];
foreach ($companies as &$company) {
    $company['jobs_count'] = (int) ($company['jobs_count'] ?? 0);
}
unset($company);

if (!function_exists('companyInitials')) {
    function companyInitials(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return 'C';
        }
        $parts = preg_split('/\s+/', $name);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
}

// حساب الإحصائيات
 $totalJobs = array_sum(array_map(static fn ($company) => (int) ($company['jobs_count'] ?? 0), $companies));
 $avgRating = count($companies) > 0 ? array_sum(array_map(static fn ($company) => (float) ($company['rating'] ?? 0), $companies)) / count($companies) : 0;
?>
<link href='https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 800: '#0d2236', 700: '#173d5d', 900: '#071b2f' },
                    gold: { 50: '#fbf6ed',100: '#f7eddb',200: '#efdbb6',300: '#e5c288',400: '#d9a857',500: '#c98e2c',600: '#ad7022',700: '#8b531e' },
                },
                fontFamily: { sans: ['Cairo', 'sans-serif'] },
            },
        },
    };
</script>
<style>
    * { font-family: 'Cairo', sans-serif; }
    body { background: #f4f6fb; }
    .company-card { transition: all .3s cubic-bezier(.22,1,.36,1); }
    .company-card:hover { transform: translateY(-4px); box-shadow: 0 18px 38px -14px rgba(13,35,64,.15); }
    .stat-pill { transition: all .2s ease; }
    .stat-pill:hover { transform: translateY(-2px); box-shadow: 0 14px 28px -12px rgba(13,35,64,.16); }
    .filter-chip { transition: all .2s ease; }
    .filter-chip:hover { transform: translateY(-1px); }
    .hero-pattern {
        background: #0d2236 !important;
        background-image:
            radial-gradient(circle at 20% 20%, rgba(255,255,255,0.06), transparent 35%),
            radial-gradient(circle at 80% 80%, rgba(59,130,246,0.18), transparent 40%);
    }
    .btn-gold { background: linear-gradient(135deg, #d9a857 0%, #c98e2c 100%); box-shadow: 0 6px 16px -4px rgba(201, 142, 44, 0.5); }
    .btn-gold:hover { box-shadow: 0 10px 20px -4px rgba(201, 142, 44, 0.7); }
    .btn-navy { background: linear-gradient(135deg, #0d2236 0%, #173d5d 100%); box-shadow: 0 6px 16px -6px rgba(13, 35, 64, 0.6); }
    .btn-navy:hover { box-shadow: 0 10px 22px -6px rgba(13, 35, 64, 0.8); }
    .icon-badge{display:inline-flex;align-items:center;justify-content:center;border-radius:14px}
    .hover-grow{transition:transform .2s ease}
    .hover-grow:hover{transform:scale(1.05)}
    .gold-line{background:linear-gradient(90deg,transparent,#d4a64a,transparent)}
    .fade-in { animation: fadeIn .4s ease both; }
    .soft-card{background:rgba(255,255,255,0.72);backdrop-filter:blur(2px)}
    .brand-chip{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14)}
    .company-meta-chip{background:#f8fafc;border:1px solid #e2e8f0}
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px);} to {opacity:1; transform: translateY(0);} }
    @media (max-width:640px){
        .company-card-title{font-size:1.05rem!important}
        .company-cta{width:100%!important}
        .company-cta-wrap{display:flex!important;flex-direction:column!important}
    }
</style>

<div class="min-h-screen bg-slate-100" dir="rtl">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="mb-5 flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500">
            <a href="#" class="flex items-center gap-1.5 hover:text-gold-600 transition">
                <i class='bx bx-home text-base'></i> الرئيسية
            </a>
            <i class='bx bx-chevron-left text-slate-400'></i>
            <span class="text-slate-800">تصفح الشركات</span>
        </nav>

        <!-- Header / Hero -->
        <section class="hero-pattern relative overflow-hidden rounded-[28px] p-6 text-white shadow-[0_24px_60px_rgba(7,27,47,0.28)] sm:p-8 lg:p-10">
            <div class="absolute -left-16 top-0 h-40 w-40 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="absolute -bottom-16 right-0 h-44 w-44 rounded-full bg-[#4f87d6]/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-500/15 px-3 py-1 text-xs font-bold text-gold-300 ring-1 ring-gold-500/20">
                            <i class='bx bx-buildings'></i> سوق الشركات
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold text-emerald-300 ring-1 ring-emerald-500/20">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> متاح الآن
                        </span>
                    </div>

                    <h1 class="text-2xl font-black leading-tight sm:text-3xl lg:text-4xl">
                        اكتشف أفضل <span class="text-gold-400">الشركات</span> في السوق
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">
                        تصفح أفضل بيئات العمل، واستعرض تفاصيل الشركات الكبرى قبل التقديم إلى الوظائف التي تناسبك.
                    </p>
                </div>

                <div class="flex items-center gap-3 self-start rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-bold backdrop-blur shadow-lg">
                    <i class='bx bx-check-shield text-gold-300'></i>
                    <span class="text-slate-200"><?= count($companies) ?> شركة مسجلة</span>
                </div>
            </div>
        </section>

        <!-- Stats Grid -->
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">الشركات النشطة</p>
                        <p class="mt-2 text-2xl sm:text-3xl font-black text-slate-900"><?= count($companies) ?></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-gold-100 text-xl text-gold-700">
                        <i class='bx bx-buildings'></i>
                    </div>
                </div>
            </div>

            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">الوظائف المفتوحة</p>
                        <p class="mt-2 text-2xl sm:text-3xl font-black text-slate-900"><?= $totalJobs ?></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-emerald-50 text-xl text-emerald-700">
                        <i class='bx bx-briefcase-alt-2'></i>
                    </div>
                </div>
            </div>

            <div class="stat-pill rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-500">متوسط التقييم</p>
                        <p class="mt-2 text-2xl sm:text-3xl font-black text-slate-900"><?= number_format($avgRating, 1) ?> <span class="text-sm text-slate-400">/ 5</span></p>
                    </div>
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-700">
                        <i class='bx bxs-star'></i>
                    </div>
                </div>
            </div>
        </div>

        <?php $companiesTopAd = renderAdMarkup($homeAds['company-header'] ?? null, '', 'company-header'); ?>
        <?php if ($companiesTopAd !== ''): ?>
        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
            </div>
            <div class="p-2 sm:p-3">
                <?= $companiesTopAd ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Companies Section -->
        <section class="mt-8">
            <div class="flex items-center gap-3 mb-5">
                <span class="icon-badge h-10 w-10 bg-gold-100 text-gold-700"><i class='bx bx-building-house text-lg'></i></span>
                <div>
                    <p class="text-xs font-bold text-gold-600">قائمة الشركات</p>
                    <h2 class="text-lg font-black text-slate-900 sm:text-xl">تصفح جميع الشركات</h2>
                </div>
            </div>
            <div class="gold-line h-px w-full opacity-40 mb-6"></div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($companies as $idx => $company):
                    $companyId = (int) ($company['id'] ?? 0);
                    $companyName = (string) ($company['name'] ?? 'شركة');
                    $industry = (string) ($company['industry'] ?? 'قطاع عام');
                    $location = (string) ($company['location'] ?? 'غير محدد');
                    $rating = (float) ($company['rating'] ?? 0);
                    $jobsCount = (int) ($company['jobs_count'] ?? 0);
                    $description = (string) ($company['description'] ?? 'شركة تعمل في السوق المحلي وتبحث عن المواهب المتميزة.');
                    $logo = $company['logo'] ?? '';
                    $logoUrl = $logo !== '' ? asset((string) $logo) : '';
                    $initials = companyInitials($companyName);
                ?>
                    <article class="company-card fade-in rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6" style="animation-delay: <?= $idx * 80 ?>ms">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <?php if ($logoUrl !== ''): ?>
                                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($companyName) ?>" class="h-14 w-14 rounded-2xl object-cover ring-2 ring-gold-100 shadow-sm sm:h-16 sm:w-16" />
                                <?php else: ?>
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#071b2f] to-[#173d5d] text-lg font-black text-gold-400 shadow-md sm:h-16 sm:w-16">
                                        <?= htmlspecialchars($initials) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="min-w-0">
                                    <h3 class="company-card-title truncate text-lg font-black text-slate-900"><?= htmlspecialchars($companyName) ?></h3>
                                    <p class="mt-1 flex items-center gap-1.5 truncate text-sm text-slate-500">
                                        <i class='bx bx-category text-xs text-slate-400'></i> <?= htmlspecialchars($industry) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col items-center justify-center rounded-2xl bg-amber-50 px-3 py-1.5 ring-1 ring-amber-100">
                                <span class="text-[10px] font-bold text-amber-600">التقييم</span>
                                <span class="flex items-center gap-1 text-sm font-black text-amber-700">
                                    <i class='bx bxs-star'></i> <?= number_format($rating, 1) ?>
                                </span>
                            </div>
                        </div>

                        <p class="mt-4 line-clamp-2 text-sm leading-7 text-slate-500">
                            <?= htmlspecialchars($description) ?>
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold text-slate-600">
                            <span class="company-meta-chip inline-flex items-center gap-1.5 rounded-full px-3 py-1.5">
                                <i class='bx bx-map text-gold-600'></i> <?= htmlspecialchars($location) ?>
                            </span>
                            <span class="company-meta-chip inline-flex items-center gap-1.5 rounded-full px-3 py-1.5">
                                <i class='bx bx-briefcase-alt-2 text-gold-600'></i> <?= number_format($jobsCount) ?> وظائف متاحة
                            </span>
                        </div>

                        <div class="company-cta-wrap mt-5 flex items-center gap-3 border-t border-slate-100 pt-4">
                            <a href="<?= htmlspecialchars(url('/companies/' . $companyId)) ?>" class="company-cta btn-navy flex flex-1 items-center justify-center gap-1.5 rounded-2xl px-4 py-3 text-sm font-black text-white transition hover:-translate-y-0.5">
                                <i class='bx bx-show'></i> عرض الملف
                            </a>
                            <a href="<?= htmlspecialchars(url('/jobs?company=' . $companyId)) ?>" class="company-cta btn-gold flex items-center justify-center gap-1.5 rounded-2xl border border-gold-200 px-4 py-3 text-sm font-black text-slate-900 transition hover:-translate-y-0.5">
                                <i class='bx bx-list-ul'></i> الوظائف
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php $companiesBottomAd = renderAdMarkup($homeAds['company-header'] ?? null, '', 'company-header'); ?>
        <?php if ($companiesBottomAd !== ''): ?>
        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
            </div>
            <div class="p-2 sm:p-3">
                <?= $companiesBottomAd ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>