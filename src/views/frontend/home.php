<?php
$jobs = $jobs ?? [];
$companies = $companies ?? [];
$page = isset($page) ? max(1, (int) $page) : 1;
$totalPages = isset($totalPages) ? max(1, (int) $totalPages) : 1;
$homeAds = $homeAds ?? [
    'header-home' => null,
    'middle-jobs' => null,
    'sidebar' => null,
    'footer' => null,
    'company-header' => null,
];

// ====== DEBUG: عرض محتوى الإعلانات ======
if (!empty($homeAds)) {
    echo '<!-- ===== AD DEBUG ===== -->';
    foreach ($homeAds as $slot => $ad) {
        if (!empty($ad)) {
            $code = $ad['ad_code'] ?? '';
            echo '<!-- Slot: ' . $slot . ' -->';
            echo '<!-- Code length: ' . strlen($code) . ' -->';
            echo '<!-- Code preview: ' . htmlspecialchars(substr($code, 0, 200)) . ' -->';
        } else {
            echo '<!-- Slot: ' . $slot . ' is empty -->';
        }
    }
    echo '<!-- ===== END AD DEBUG ===== -->';
}
// ================================

// دوال التنسيق فقط (بدون دوال الإعلانات)
if (!function_exists('formatJobType')) {
    function formatJobType(string $value): string
    {
        $map = [
            'full-time' => 'دوام كامل',
            'part-time' => 'دوام جزئي',
            'contract' => 'عقد',
            'temporary' => 'مؤقت',
            'internship' => 'تدريب',
            'full_time' => 'دوام كامل',
            'part_time' => 'دوام جزئي',
            'remote' => 'عن بعد',
        ];

        return $map[$value] ?? ucfirst((string) $value);
    }
}

if (!function_exists('formatSalaryRange')) {
    function formatSalaryRange($salaryMin, $salaryMax, $currency = 'EGP', $negotiable = 0): string
    {
        if (!empty($negotiable)) {
            return 'قابل للتفاوض';
        }

        $currencyName = strtoupper((string) $currency);

        if ($salaryMin !== null && $salaryMax !== null && $salaryMin > 0 && $salaryMax > 0) {
            return number_format((float) $salaryMin, 0, '.', ',') . ' - ' . number_format((float) $salaryMax, 0, '.', ',') . ' ' . $currencyName;
        }

        if ($salaryMin !== null && $salaryMin > 0) {
            return 'من ' . number_format((float) $salaryMin, 0, '.', ',') . ' ' . $currencyName;
        }

        if ($salaryMax !== null && $salaryMax > 0) {
            return 'حتى ' . number_format((float) $salaryMax, 0, '.', ',') . ' ' . $currencyName;
        }

        return 'راتب قابل للتفاوض';
    }
}

if (!function_exists('formatTimeAgo')) {
    function formatTimeAgo(string $date): string
    {
        if ($date === '') {
            return 'حديثاً';
        }

        try {
            $time = new DateTime($date);
            $diff = $time->diff(new DateTime());

            if ($diff->y > 0) {
                return 'منذ ' . $diff->y . ' سنة';
            }

            if ($diff->m > 0) {
                return 'منذ ' . $diff->m . ' شهر';
            }

            if ($diff->d > 0) {
                return 'منذ ' . $diff->d . ' يوم';
            }

            if ($diff->h > 0) {
                return 'منذ ' . $diff->h . ' ساعة';
            }

            if ($diff->i > 0) {
                return 'منذ ' . $diff->i . ' دقيقة';
            }

            return 'منذ لحظات';
        } catch (Throwable $e) {
            return 'حديثاً';
        }
    }
}

if (!function_exists('companyInitials')) {
    function companyInitials(string $name): string
    {
        $clean = trim($name);
        if ($clean === '') {
            return 'J';
        }

        $parts = preg_split('/\s+/', $clean);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        }

        return strtoupper(substr($clean, 0, 2));
    }
}
?>
<style>
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw;
    }

    body {
        position: relative;
    }

    *, *::before, *::after {
        box-sizing: border-box;
    }

    .ad-slot-wrap,
    .ad-slot-wrap iframe,
    .ad-slot-wrap ins,
    .ad-slot-wrap .adsbygoogle,
    .ad-slot-wrap > div {
        max-width: 100% !important;
        width: 100% !important;
        overflow: hidden !important;
        display: block !important;
    }

    .hero-section,
    section,
    aside,
    article,
    div {
        max-width: 100%;
    }
</style>

<div id="site-warning-bar" class="sticky top-0 z-[60] border-b border-amber-200 bg-gradient-to-r from-amber-50 via-yellow-50 to-orange-50 shadow-[0_8px_24px_rgba(120,53,15,0.12)] backdrop-blur-sm" dir="rtl">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-3 py-2.5 sm:px-6 lg:px-8">
        <div class="flex min-w-0 flex-1 items-start gap-2.5">
            <div class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-amber-100 text-base text-amber-600 shadow-sm">
                ⚠️
            </div>
            <p class="text-[11px] font-extrabold leading-6 text-amber-900 sm:text-[12px]">
                <span class="text-amber-700">تنبيه مهم:</span>
                موقع موقف الوظائف مجرد وسيط لعرض فرص العمل، وغير مسئول عن جهة العمل أو الأشخاص أو نتائج التعامل معهم. لا نطلب دفع أي مبالغ مالية مقابل الوظائف. في حالة طلب أموال، يرجى عدم الدفع وحظر الشخص والإبلاغ عنه. نحن مجرد وسيط للخير.
            </p>
        </div>
        <button type="button" aria-label="إغلاق التنبيه" class="close-warning-bar flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-amber-200 bg-white/80 text-sm font-black text-amber-700 transition hover:border-amber-300 hover:bg-white hover:text-amber-900" data-target="site-warning-bar">
            ×
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const banner = document.getElementById('site-warning-bar');
        if (!banner) return;

        document.querySelectorAll('.close-warning-bar').forEach(function (button) {
            button.addEventListener('click', function () {
                banner.style.display = 'none';
            });
        });
    });
</script>

<!-- 1. قسم الوظائف أولاً -->
<section class="relative bg-slate-50 font-sans py-10" dir="rtl">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        
        <!-- رأس قسم الوظائف -->
        <div class="mb-8 flex flex-col gap-3 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-1.5 rounded-full bg-gradient-to-b from-gold-400 to-gold-600"></div>
                <div>
                    <h2 class="text-xl font-extrabold text-navy-900 sm:text-2xl">أحدث الوظائف الشاغرة</h2>
                    <p class="mt-1 text-[12px] text-slate-500">تصفح أكثر من 1200 فرصة وظيفية مميزة وابحث عن ما يناسبك</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="hidden text-[12px] font-bold text-slate-500 sm:inline">ترتيب حسب:</span>
                <div class="relative">
                    <select class="w-full cursor-pointer appearance-none rounded-xl border border-slate-200 bg-white py-2.5 pr-4 pl-10 text-[12px] font-bold text-slate-700 shadow-sm transition-colors hover:border-gold-300 focus:border-gold-400 focus:outline-none focus:ring-2 focus:ring-gold-400/15 sm:w-auto">
                        <option>الأحدث</option>
                        <option>الأعلى راتباً</option>
                        <option>الأكثر تطابقاً</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- إعلان وسط الوظائف -->
        <?php $middleJobsAd = renderHomeAd($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
        <?php if ($middleJobsAd !== ''): ?>
        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">إعلان</span>
            </div>
            <div class="p-2 sm:p-3">
                <?= $middleJobsAd ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- قائمة بطاقات الوظائف -->
        <div class="space-y-4">
            <?php if (!empty($jobs)): ?>
                <?php foreach ($jobs as $job): ?>
                    <?php
                    $jobTitle = htmlspecialchars((string) ($job['title'] ?? 'وظيفة جديدة'), ENT_QUOTES, 'UTF-8');
                    $companyName = htmlspecialchars((string) ($job['company_name'] ?? 'شركة'), ENT_QUOTES, 'UTF-8');
                    $governorateName = htmlspecialchars((string) ($job['governorate_name'] ?? 'مصر'), ENT_QUOTES, 'UTF-8');
                    $jobType = formatJobType((string) ($job['job_type'] ?? 'full-time'));
                    $salary = formatSalaryRange($job['salary_min'] ?? null, $job['salary_max'] ?? null, $job['salary_currency'] ?? 'EGP', $job['salary_negotiable'] ?? 0);
                    $timeAgo = formatTimeAgo((string) ($job['created_at'] ?? ''));
                    $jobSlug = urlencode((string) ($job['slug'] ?? $job['id'] ?? 'job'));
                    $jobCompanySlug = urlencode((string) ($job['company_slug'] ?? $job['company_id'] ?? 'company'));
                    $initials = companyInitials((string) ($job['company_name'] ?? 'شركة'));
                    $companyLogo = (string) ($job['company_logo'] ?? '');
                    $companyLogoUrl = $companyLogo !== '' ? asset($companyLogo) : '';
                    $iconClasses = ['bg-blue-50 text-blue-600', 'bg-pink-50 text-pink-600', 'bg-green-50 text-green-600', 'bg-orange-50 text-orange-600', 'bg-purple-50 text-purple-600', 'bg-red-50 text-red-600'];
                    $iconIndex = (int) (($job['id'] ?? 0) % count($iconClasses));
                    $jobId = (int) ($job['id'] ?? 0);
                    $isSaved = in_array($jobId, $savedJobIds ?? [], true);
                    ?>
                    <div class="group flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-all duration-300 hover:border-gold-300 hover:shadow-md sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-3">
                            <?php if ($companyLogoUrl !== ''): ?>
                                <img src="<?= htmlspecialchars($companyLogoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $companyName ?>" class="h-12 w-12 flex-shrink-0 rounded-xl object-cover ring-2 ring-gold-100 shadow-sm" />
                            <?php else: ?>
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl <?= $iconClasses[$iconIndex] ?> text-sm font-black">
                                    <?= $initials ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h3 class="text-[14px] font-extrabold text-navy-900 sm:text-[15px]"><?= $jobTitle ?></h3>
                                <p class="mt-1 flex items-center gap-1 text-[12px] text-slate-500">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
                                    <?= $companyName ?> - <?= $governorateName ?>، مصر
                                </p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600"><?= $jobType ?></span>
                                    <span class="rounded-md bg-green-50 px-2 py-1 text-[10px] font-bold text-green-600"><?= $job['is_remote'] ?? 'مباشر' ?></span>
                                    <span class="rounded-md bg-gold-50 px-2 py-1 text-[10px] font-bold text-gold-700"><?= $salary ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2 border-t border-slate-100 pt-3 sm:flex-col sm:items-end sm:border-0 sm:pt-0">
                            <span class="text-[10px] text-slate-400"><?= $timeAgo ?></span>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="<?= htmlspecialchars(url('/jobs/' . $jobId . '/save')) ?>" class="inline-block">
                                    <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg border transition-colors <?= $isSaved ? 'border-gold-300 bg-gold-50 text-gold-700' : 'border-slate-200 text-slate-400 hover:border-gold-300 hover:text-gold-500' ?>" aria-label="حفظ الوظيفة" title="<?= $isSaved ? 'تم الحفظ' : 'حفظ الوظيفة' ?>">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="<?= $isSaved ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                                    </button>
                                </form>
                                <a href="<?= htmlspecialchars(url('/jobs/' . (int) ($job['id'] ?? 0))) ?>" class="rounded-lg bg-gradient-to-b from-gold-400 to-gold-500 px-4 py-2 text-[12px] font-extrabold text-navy-900 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md">عرض التفاصيل</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                    لا توجد وظائف متاحة حاليًا.
                </div>
            <?php endif; ?>
        </div>

        <!-- قسم شركات توظف الآن -->
        <div class="mt-10 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-5 flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-1.5 rounded-full bg-gradient-to-b from-gold-400 to-gold-600"></div>
                    <div>
                        <h3 class="text-xl font-extrabold text-navy-900">شركات توظف الآن</h3>
                        <p class="mt-1 text-[12px] text-slate-500">أفضل الشركات التي تفتح أبوابها اليوم أمام الكفاءات</p>
                    </div>
                </div>
                <a href="<?= htmlspecialchars(url('/companies')) ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-all hover:border-gold-300 hover:bg-gold-50 hover:text-gold-700">
                    عرض كل الشركات
                </a>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <?php if (!empty($companies)): ?>
                    <?php foreach ($companies as $company): ?>
                        <?php
                        $companyName = htmlspecialchars((string) ($company['name'] ?? 'شركة'), ENT_QUOTES, 'UTF-8');
                        $companyIndustry = htmlspecialchars((string) ($company['industry'] ?? 'شركات متنوعة'), ENT_QUOTES, 'UTF-8');
                        $companyGovernorate = htmlspecialchars((string) ($company['governorate_name'] ?? 'مصر'), ENT_QUOTES, 'UTF-8');
                        $companySlug = htmlspecialchars((string) ($company['slug'] ?? $company['id'] ?? 'company'), ENT_QUOTES, 'UTF-8');
                        $companyLogo = $company['logo'] ?? '';
                        $companyLogoUrl = $companyLogo !== '' ? asset((string) $companyLogo) : '';
                        $companyLogoText = $companyLogoUrl !== '' ? '<img src="' . htmlspecialchars($companyLogoUrl, ENT_QUOTES, 'UTF-8') . '" alt="' . $companyName . '" class="h-10 w-10 rounded-xl object-cover" />' : '<div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#0f172a] to-[#1d4ed8] text-lg font-black text-white shadow-sm">' . companyInitials((string) ($company['name'] ?? 'شركة')) . '</div>';
                        $jobCount = (int) ($company['jobs_count'] ?? 0);
                        ?>
                        <div class="group rounded-2xl border border-slate-200 bg-slate-50 p-3 transition-all duration-300 hover:border-gold-300 hover:bg-white hover:shadow-md">
                            <div class="mb-3 flex items-center justify-between">
                                <?= $companyLogoText ?>
                                <span class="rounded-full bg-gold-50 px-2 py-1 text-[10px] font-bold text-gold-700"><?= $jobCount ?> فرصة</span>
                            </div>
                            <h4 class="text-[13px] font-extrabold text-navy-900"><?= $companyName ?></h4>
                            <p class="mt-1 text-[11px] text-slate-500"><?= $companyIndustry ?></p>
                            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                <span class="text-[10px] font-bold text-slate-400">في <?= $companyGovernorate ?></span>
                                <a href="<?= htmlspecialchars(url('/companies/' . rawurlencode((string) ($company['id'] ?? $companySlug)))) ?>" class="text-[11px] font-extrabold text-gold-700 hover:text-gold-800">عرض</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                        لا توجد شركات نشطة حاليًا.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- الترقيم (Pagination) -->
        <?php
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        if ($startPage > 1) {
            $endPage = min($endPage + 1, $totalPages);
        }
        if ($endPage < $totalPages) {
            $startPage = max(1, $endPage - 4);
        }
        ?>
        <nav class="mt-10 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2" aria-label="Pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= max(1, $page - 1) ?>" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition-colors hover:bg-slate-50 disabled:opacity-50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            <?php else: ?>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-slate-300 opacity-50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
                </span>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <?php if ($i === $page): ?>
                    <span aria-current="page" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-b from-gold-400 to-gold-500 text-sm font-extrabold text-navy-900 shadow-sm"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm font-bold text-slate-700 transition-colors hover:bg-slate-50"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= min($totalPages, $page + 1) ?>" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition-colors hover:bg-slate-50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                </a>
            <?php else: ?>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-slate-300 opacity-50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                </span>
            <?php endif; ?>
        </nav>

        <!-- إعلان الفوتر (فوق الفوتر مباشرة) -->
        <?php $footerAd = renderHomeAd($homeAds['footer'] ?? null, '', 'footer'); ?>
        <?php if ($footerAd !== ''): ?>
        <div class="mt-10 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">إعلان</span>
            </div>
            <div class="p-2 sm:p-3">
                <?= $footerAd ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- 2. قسم فلتر البحث عن الوظائف (بشكل جميل ومنظم) -->
<section class="relative overflow-hidden bg-navy-900 font-sans py-14 sm:py-20" dir="rtl">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1514565131-fce0801e5785?auto=format&fit=crop&w=900&q=80" alt="مدينة" class="h-full w-full object-cover scale-110 opacity-45" />
        <div class="absolute inset-0 bg-gradient-to-l from-[#061827]/85 via-[#071b2f]/65 to-[#071b2f]/20"></div>
        <div class="absolute top-1/4 right-1/4 h-28 w-28 rounded-full bg-gold-500/8 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-gold-500/5 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-8 max-w-2xl text-center text-white">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-gold-500/30 bg-gold-500/10 px-3 py-1.5 backdrop-blur-sm">
                <span class="flex h-2 w-2 rounded-full bg-gold-400 animate-pulse"></span>
                <p class="text-[11px] font-bold tracking-wider text-gold-300">ابدأ بحثك المتقدم</p>
            </div>
            <h2 class="text-2xl font-black tracking-tight drop-shadow-lg sm:text-3xl">
                فلتر <span class="relative inline-block text-gold-400">البحث
                    <svg class="absolute -bottom-1 right-0 w-full" height="8" viewBox="0 0 200 10" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                        <path d="M2 8C48 2 152 2 198 8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </span> عن الوظائف
            </h2>
            <p class="mx-auto mt-3 max-w-lg text-[12px] leading-5 text-slate-300">
                استخدم أدوات الفلترة المتقدمة للعثور على الوظيفة التي تطابق طموحاتك ومهاراتك بدقة.
            </p>
        </div>

        <div class="mx-auto w-full max-w-4xl rounded-2xl border border-white/20 bg-white/95 p-4 shadow-[0_20px_50px_rgba(0,0,0,0.25)] backdrop-blur-xl sm:p-6">
            <form action="<?= htmlspecialchars(url('/jobs')) ?>" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                
                <div class="group relative">
                    <label for="category" class="mb-1.5 flex items-center gap-1.5 text-[13px] font-bold text-slate-700">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-gold-500/10 text-gold-600 transition-colors group-hover:bg-gold-500/20">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                        </span>
                        التخصص
                    </label>
                    <div class="relative">
                        <select id="category" name="category" class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/80 py-3 pr-3 pl-9 text-[13px] font-medium text-slate-800 transition-all duration-300 hover:border-gold-300 hover:bg-white focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-400/15">
                            <option value="">كل التخصصات</option>
                            <?php foreach ($categories as $category): ?>
                                <?php $categoryValue = (int) ($category['id'] ?? 0); ?>
                                <option value="<?= $categoryValue ?>" <?= ((int) ($selectedCategoryId ?? 0) === $categoryValue) ? 'selected' : '' ?>><?= htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </div>
                </div>

                <div class="group relative">
                    <label for="job_type" class="mb-1.5 flex items-center gap-1.5 text-[13px] font-bold text-slate-700">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-gold-500/10 text-gold-600 transition-colors group-hover:bg-gold-500/20">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </span>
                        نوع الوظيفة
                    </label>
                    <div class="relative">
                        <select id="job_type" name="job_type" class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/80 py-3 pr-3 pl-9 text-[13px] font-medium text-slate-800 transition-all duration-300 hover:border-gold-300 hover:bg-white focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-400/15">
                            <option value="">كل الأنواع</option>
                            <option value="full-time" <?= (($selectedJobType ?? '') === 'full-time') ? 'selected' : '' ?>>دوام كامل</option>
                            <option value="part-time" <?= (($selectedJobType ?? '') === 'part-time') ? 'selected' : '' ?>>دوام جزئي</option>
                            <option value="contract" <?= (($selectedJobType ?? '') === 'contract') ? 'selected' : '' ?>>عقد</option>
                            <option value="temporary" <?= (($selectedJobType ?? '') === 'temporary') ? 'selected' : '' ?>>مؤقت</option>
                            <option value="internship" <?= (($selectedJobType ?? '') === 'internship') ? 'selected' : '' ?>>تدريب</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </div>
                </div>

                <div class="group relative">
                    <label for="title" class="mb-1.5 flex items-center gap-1.5 text-[13px] font-bold text-slate-700">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-gold-500/10 text-gold-600 transition-colors group-hover:bg-gold-500/20">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        المسمى الوظيفي
                    </label>
                    <div class="relative">
                        <input id="title" type="text" name="title" value="<?= htmlspecialchars((string) ($keyword ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="مثال: مدير مشروع" class="w-full rounded-xl border border-slate-200 bg-slate-50/80 py-3 pr-3 pl-3 text-[13px] font-medium text-slate-800 placeholder:text-slate-400 transition-all duration-300 hover:border-gold-300 hover:bg-white focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-400/15" />
                    </div>
                </div>

                <div class="group relative">
                    <label for="location" class="mb-1.5 flex items-center gap-1.5 text-[13px] font-bold text-slate-700">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-gold-500/10 text-gold-600 transition-colors group-hover:bg-gold-500/20">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 21s-7-6-7-11a7 7 0 1114 0c0 5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        </span>
                        المحافظة
                    </label>
                    <div class="relative">
                        <select id="location" name="location" class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/80 py-3 pr-3 pl-9 text-[13px] font-medium text-slate-800 transition-all duration-300 hover:border-gold-300 hover:bg-white focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-400/15">
                            <option value="">كل المحافظات</option>
                            <?php foreach ($governorates as $governorate): ?>
                                <?php $govId = (int) ($governorate['id'] ?? 0); ?>
                                <option value="<?= $govId ?>" <?= ((int) ($selectedGovernorateId ?? 0) === $govId) ? 'selected' : '' ?>><?= htmlspecialchars((string) ($governorate['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </div>
                </div>

                <div class="sm:col-span-2 lg:col-span-4">
                    <button type="submit" class="group/btn flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-b from-gold-400 to-gold-500 px-6 py-3.5 text-navy-900 shadow-md shadow-gold-500/30 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-gold-500/40 active:translate-y-0 active:shadow-sm">
                        <svg class="h-5 w-5 transition-transform duration-300 group-hover/btn:scale-110" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6"></circle><path d="M16 16L21 21"></path></svg>
                        <span class="text-[14px] font-extrabold">بحث عن الوظائف</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-slate-200 pt-5">
                <span class="text-xs font-bold text-slate-600">الأكثر بحثاً:</span>
                <?php foreach (($popularSearches ?? []) as $popularSearch): ?>
                    <?php
                    $label = trim((string) ($popularSearch['label'] ?? ''));
                    $query = trim((string) ($popularSearch['query'] ?? ''));
                    if ($label === '' || $query === '') {
                        continue;
                    }
                    ?>
                    <a href="<?= htmlspecialchars(url('/jobs?q=' . rawurlencode($query))) ?>" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] font-medium text-slate-600 transition hover:border-gold-300 hover:bg-gold-50 hover:text-gold-700">
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- قسم أقسام الوظائف السريع داخل الفلتر -->
        <div class="mt-10">
            <h3 class="mb-5 text-center text-lg font-extrabold text-white">أو تصفح حسب القسم</h3>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $index => $category): ?>
                        <?php
                        $colors = [
                            ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'hover' => 'hover:bg-blue-500/20'],
                            ['bg' => 'bg-pink-500/10', 'text' => 'text-pink-400', 'hover' => 'hover:bg-pink-500/20'],
                            ['bg' => 'bg-green-500/10', 'text' => 'text-green-400', 'hover' => 'hover:bg-green-500/20'],
                            ['bg' => 'bg-orange-500/10', 'text' => 'text-orange-400', 'hover' => 'hover:bg-orange-500/20'],
                            ['bg' => 'bg-purple-500/10', 'text' => 'text-purple-400', 'hover' => 'hover:bg-purple-500/20'],
                            ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'hover' => 'hover:bg-red-500/20']
                        ];
                        $colorIndex = $index % count($colors);
                        $color = $colors[$colorIndex];
                        $categoryId = (int) ($category['id'] ?? 0);
                        $categoryName = htmlspecialchars((string) ($category['name'] ?? 'قسم'), ENT_QUOTES, 'UTF-8');
                        $jobCount = (int) ($category['jobs_count'] ?? 0);
                        ?>
                        <a href="<?= htmlspecialchars(url('/jobs?category=' . $categoryId)) ?>" class="group flex items-center justify-between rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm transition-all hover:bg-white/10">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg <?= $color['bg'] ?> <?= $color['text'] ?> transition-colors <?= $color['hover'] ?>">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                </span>
                                <span class="text-[13px] font-bold text-slate-200 group-hover:text-white"><?= $categoryName ?></span>
                            </div>
                            <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-bold text-slate-300 group-hover:bg-gold-500 group-hover:text-navy-900"><?= $jobCount ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full rounded-2xl border border-dashed border-white/20 bg-white/5 p-8 text-center text-slate-400">
                        لا توجد أقسام متاحة حالياً
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<!-- 3. قسم الإعلانات الجانبية والـ CTA (هل تبحث عن موظفين؟) -->
<section class="bg-slate-50 py-12 font-sans" dir="rtl">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 items-stretch">
            
            <!-- إعلان جانبي -->
            <?php $sidebarAd = renderHomeAd($homeAds['sidebar'] ?? null, '', 'sidebar'); ?>
            <?php if ($sidebarAd !== ''): ?>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                    <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">إعلان</span>
                </div>
                <div class="p-2 flex-grow flex items-center justify-center">
                    <?= $sidebarAd ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- هل تبحث عن موظفين؟ -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-navy-900 to-[#0a253b] p-6 text-white shadow-md">
                <div class="absolute -top-6 -left-6 h-24 w-24 rounded-full bg-gold-500/10 blur-2xl"></div>
                <div class="absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-gold-500/5 blur-xl"></div>
                
                <div class="relative flex h-full flex-col justify-center">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gold-500/15 backdrop-blur-sm border border-gold-500/20">
                        <svg class="h-6 w-6 text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    
                    <h3 class="text-xl font-extrabold text-gold-400">هل تبحث عن موظفين؟</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-300">
                        انضم إلى آلاف الشركات واعلن عن وظائفك للوصول إلى أفضل الكفاءات في مصر والوطن العربي.
                    </p>
                    
                    <a href="<?= htmlspecialchars(url('/employer/jobs/create')) ?>" class="mt-5 inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-gold-500/30 bg-gold-500/10 px-5 py-2.5 text-sm font-bold text-gold-300 backdrop-blur-sm transition-all hover:bg-gold-500/20 hover:text-gold-200">
                        أضف وظيفتك الآن
                        <svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>