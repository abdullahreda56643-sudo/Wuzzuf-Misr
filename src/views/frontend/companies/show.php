<?php
$company = $company ?? [];
$jobs = $jobs ?? [];

$companyId = (int) ($company['id'] ?? 0);
$companyName = (string) ($company['name'] ?? 'شركة');
$industry = (string) ($company['industry'] ?? 'قطاع عام');
$location = (string) ($company['location'] ?? 'غير محدد');
$description = (string) ($company['description'] ?? 'لا توجد معلومات إضافية');
$website = (string) ($company['website'] ?? '');
$email = (string) ($company['email'] ?? '');
$phone = (string) ($company['phone'] ?? '');
$foundedYear = (int) ($company['founded_year'] ?? 0);
$companySize = (string) ($company['company_size'] ?? 'small');
$logo = (string) ($company['logo'] ?? '');
$logoUrl = $logo !== '' ? asset($logo) : '';
$initials = strtoupper(substr($companyName, 0, 2));
$currentUser = current_user();
$averageRating = (float) ($averageRating ?? 0);
$ratingsCount = (int) ($ratingsCount ?? 0);
$latestReviews = $latestReviews ?? [];
$userRating = $userRating ?? null;

if (!function_exists('companySizeLabel')) {
    function companySizeLabel(string $value): string {
        $map = [
            'startup' => 'شركة ناشئة',
            'small' => 'شركة صغيرة',
            'medium' => 'شركة متوسطة',
            'large' => 'شركة كبيرة',
            'enterprise' => 'مؤسسة ضخمة',
        ];
        return $map[$value] ?? $value;
    }
}

if (!function_exists('formatSalary')) {
    function formatSalary($salaryMin, $salaryMax, $currency = 'EGP', $negotiable = 0): string {
        if (!empty($negotiable)) {
            return 'قابل للتفاوض';
        }
        $currency = strtoupper((string) $currency);
        $currencySymbol = $currency === 'SAR' ? 'ر.س' : ($currency === 'EGP' ? 'ج.م' : $currency);
        if ($salaryMin && $salaryMax) {
            return number_format((float) $salaryMin, 0, '.', ',') . ' - ' . number_format((float) $salaryMax, 0, '.', ',') . ' ' . $currencySymbol;
        }
        if ($salaryMin) {
            return 'من ' . number_format((float) $salaryMin, 0, '.', ',') . ' ' . $currencySymbol;
        }
        if ($salaryMax) {
            return 'حتى ' . number_format((float) $salaryMax, 0, '.', ',') . ' ' . $currencySymbol;
        }
        return 'مرتب تنافسي';
    }
}

if (!function_exists('jobTypeLabel')) {
    function jobTypeLabel(string $value): string {
        $map = [
            'full-time' => 'دوام كامل',
            'part-time' => 'دوام جزئي',
            'contract' => 'عقد',
            'temporary' => 'مؤقت',
            'internship' => 'تدريب',
            'remote' => 'عن بعد',
        ];
        return $map[$value] ?? ucfirst((string) $value);
    }
}

if (!function_exists('jobTypeIcon')) {
    function jobTypeIcon(string $value): string {
        $map = [
            'full-time' => 'bx-briefcase-alt-2',
            'part-time' => 'bx-time-five',
            'contract' => 'bx-file',
            'temporary' => 'bx-calendar',
            'internship' => 'bx-graduation',
            'remote' => 'bx-home-heart',
        ];
        return $map[$value] ?? 'bx-briefcase-alt-2';
    }
}


 $companyName = (string) ($company['name'] ?? 'شركة');
 $industry = (string) ($company['industry'] ?? 'قطاع عام');
 $location = (string) ($company['location'] ?? 'غير محدد');
 $description = (string) ($company['description'] ?? 'لا توجد معلومات إضافية تم إدخالها');
 $website = (string) ($company['website'] ?? '');
 $email = (string) ($company['email'] ?? '');
 $phone = (string) ($company['phone'] ?? '');
 $foundedYear = (string) ($company['founded_year'] ?? '');
 $employees = (string) ($company['employees'] ?? '');
 $companyId = (int) ($company['id'] ?? 0);
 $initials = strtoupper(substr($companyName, 0, 2));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الشركة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #eef2f7; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        
        .job-card { transition: all .3s cubic-bezier(.22,1,.36,1); }
        .job-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px -12px rgba(13, 35, 64, .15); }

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

        .fade-in { animation: fadeIn .4s ease both; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px);} to {opacity:1; transform: translateY(0);} }
    </style>
</head>
<body class="text-slate-800 antialiased">

<div class="min-h-screen">
    <div class="mx-auto max-w-7xl px-3 py-4 sm:px-6 sm:py-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="mb-4 flex items-center gap-1 sm:gap-2 text-[10px] sm:text-xs lg:text-sm font-semibold text-slate-500 overflow-x-auto pb-1">
            <a href="<?= htmlspecialchars(url('/')) ?>" class="flex items-center gap-1 hover:text-gold-600 transition flex-shrink-0">
                <i class='bx bx-home text-sm'></i> <span class="hidden sm:inline">الرئيسية</span>
            </a>
            <i class='bx bx-chevron-left text-slate-400 flex-shrink-0'></i>
            <a href="<?= htmlspecialchars(url('/companies')) ?>" class="flex items-center gap-1 hover:text-gold-600 transition flex-shrink-0">
                <span>الشركات</span>
            </a>
            <i class='bx bx-chevron-left text-slate-400 flex-shrink-0'></i>
            <span class="text-slate-800 truncate"><?= htmlspecialchars($companyName) ?></span>
        </nav>

        <!-- Hero Section -->
        <section class="hero-pattern relative overflow-hidden rounded-2xl sm:rounded-[28px] text-white shadow-[0_20px_50px_rgba(7,27,47,0.25)] sm:shadow-[0_24px_60px_rgba(7,27,47,0.28)] p-4 sm:p-6 lg:p-10">
            <div class="absolute -left-20 top-0 h-32 w-32 sm:h-40 sm:w-40 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="absolute -bottom-20 right-0 h-32 w-32 sm:h-44 sm:w-44 rounded-full bg-[#4f87d6]/20 blur-3xl"></div>

            <div class="relative">
                <div class="flex flex-col gap-4 sm:gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-3 sm:gap-4 lg:gap-5 min-w-0">
                        <?php if ($logoUrl !== ''): ?>
                            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($companyName) ?>" class="h-16 w-16 sm:h-20 sm:w-20 lg:h-24 lg:w-24 rounded-xl sm:rounded-2xl object-cover ring-3 sm:ring-4 ring-gold-500/30 shadow-lg flex-shrink-0" />
                        <?php else: ?>
                            <div class="flex h-16 w-16 sm:h-20 sm:w-20 lg:h-24 lg:w-24 items-center justify-center rounded-xl sm:rounded-2xl bg-gradient-to-br from-gold-400 to-gold-600 text-2xl sm:text-3xl font-black text-white ring-3 sm:ring-4 ring-gold-500/30 shadow-lg flex-shrink-0">
                                <?= htmlspecialchars($initials) ?>
                            </div>
                        <?php endif; ?>

                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl lg:text-4xl font-black leading-tight break-words">
                                <?= htmlspecialchars($companyName) ?>
                            </h1>
                            <div class="mt-2 sm:mt-3 flex flex-wrap items-center gap-1.5 sm:gap-2">
                                <span class="inline-flex items-center gap-1 rounded-full bg-gold-500/15 px-2 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-bold text-gold-300 ring-1 ring-gold-500/20 flex-shrink-0">
                                    <i class='bx bx-briefcase-alt-2 text-xs sm:text-sm'></i> <span class="hidden sm:inline"><?= htmlspecialchars($industry) ?></span>
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-500/15 px-2 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-bold text-sky-300 ring-1 ring-sky-500/20 flex-shrink-0">
                                    <i class='bx bx-map text-xs sm:text-sm'></i> <span class="hidden sm:inline"><?= htmlspecialchars($location) ?></span>
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-bold text-emerald-300 ring-1 ring-emerald-500/20 flex-shrink-0">
                                    <i class='bx bx-building text-xs sm:text-sm'></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 sm:gap-3 w-full sm:w-auto mt-2 sm:mt-0 flex-shrink-0">
                        <?php if ($website !== ''): ?>
                            <a href="<?= htmlspecialchars($website) ?>" target="_blank" class="btn-gold flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl px-4 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-black text-white transition hover:-translate-y-0.5 active:scale-95">
                                <i class='bx bx-globe text-sm sm:text-base'></i> <span class="hidden sm:inline">زيارة</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($email !== '' || $phone !== ''): ?>
                            <a href="<?= $email !== '' ? 'mailto:' . htmlspecialchars($email) : 'tel:' . htmlspecialchars($phone) ?>" class="btn-navy flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl px-4 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-black text-white transition hover:-translate-y-0.5 active:scale-95">
                                <i class='bx bx-envelope text-sm sm:text-base'></i> <span class="hidden sm:inline">تواصل</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <?php $companyHeaderAd = renderAdMarkup($homeAds['company-header'] ?? null, '', 'company-header'); ?>
        <?php if ($companyHeaderAd !== ''): ?>
        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
            </div>
            <div class="p-2 sm:p-3">
                <?= $companyHeaderAd ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- MAIN GRID -->
        <section class="mt-4 sm:mt-6 lg:mt-8 grid gap-3 sm:gap-4 lg:gap-6 grid-cols-1 lg:grid-cols-[1fr_0.85fr] auto-rows-max lg:auto-rows-auto">
            
            <!-- Left Column -->
            <div class="space-y-3 sm:space-y-4 lg:space-y-6 order-2 lg:order-1">
                <!-- About -->
                <article class="company-card fade-in rounded-xl sm:rounded-[24px] lg:rounded-[28px] border border-slate-200 bg-white p-3 sm:p-4 lg:p-6 shadow-sm">
                    <div class="flex flex-col gap-2 sm:gap-3 border-b border-slate-100 pb-3 sm:pb-4">
                        <div>
                            <p class="text-[10px] sm:text-xs lg:text-sm font-bold text-gold-700 flex items-center gap-1.5">
                                <i class='bx bx-purchase-tag-alt text-sm'></i> <?= htmlspecialchars($industry) ?>
                            </p>
                            <h2 class="mt-1 sm:mt-1.5 text-base sm:text-lg lg:text-2xl font-black text-slate-900">نبذة عن الشركة</h2>
                        </div>
                    </div>

                    <p class="mt-3 sm:mt-4 text-[13px] sm:text-sm lg:text-base leading-6 sm:leading-7 lg:leading-8 text-slate-600">
                        <?= nl2br(htmlspecialchars($description)) ?>
                    </p>
                </article>

                <!-- Open Jobs -->
                <article id="open-jobs" class="company-card fade-in rounded-xl sm:rounded-[24px] lg:rounded-[28px] border border-slate-200 bg-white p-3 sm:p-4 lg:p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3 sm:mb-4 lg:mb-5 border-b border-slate-100 pb-3 sm:pb-4">
                        <i class='bx bx-list-ul text-lg sm:text-xl lg:text-2xl text-gold-600'></i>
                        <h3 class="text-base sm:text-lg lg:text-2xl font-black text-slate-900">الوظائف (<?= count($jobs) ?>)</h3>
                    </div>
                    
                    <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                        <?php foreach ($jobs as $job):
                            $jobId = (int) ($job['id'] ?? 0);
                            $jobTitle = (string) ($job['title'] ?? 'وظيفة');
                            $jobType = jobTypeLabel((string) ($job['job_type'] ?? 'full-time'));
                            $jobTypeIcon = jobTypeIcon((string) ($job['job_type'] ?? 'full-time'));
                            $jobLocation = (string) ($job['location'] ?? 'غير محدد');
                            $salary = formatSalary($job['salary_min'] ?? null, $job['salary_max'] ?? null, $job['salary_currency'] ?? 'SAR', $job['salary_negotiable'] ?? 0);
                        ?>
                            <a href="<?= htmlspecialchars(url('/jobs/' . $jobId)) ?>" class="block group rounded-lg sm:rounded-xl border border-slate-200 bg-slate-50 p-2.5 sm:p-3 lg:p-4 transition hover:border-gold-200 hover:bg-white hover:shadow-sm active:scale-98">
                                <div class="flex flex-col gap-1.5 sm:gap-2 lg:gap-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="text-xs sm:text-sm lg:text-base font-black text-slate-900 group-hover:text-gold-700 transition line-clamp-2">
                                            <?= htmlspecialchars($jobTitle) ?>
                                        </h4>
                                        <div class="flex items-center gap-1 text-[10px] sm:text-xs font-black text-navy-800 bg-navy-50 px-1.5 sm:px-2.5 py-1 sm:py-1.5 rounded-md sm:rounded-lg ring-1 ring-navy-100 flex-shrink-0">
                                            <i class='bx bx-money text-xs'></i> <span class="hidden sm:inline"><?= htmlspecialchars($salary) ?></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1 sm:gap-1.5 text-[9px] sm:text-xs text-slate-500">
                                        <span class="flex items-center gap-0.5">
                                            <i class='bx <?= $jobTypeIcon ?> text-xs text-gold-600'></i> <span class="hidden sm:inline"><?= htmlspecialchars($jobType) ?></span>
                                        </span>
                                        <span class="hidden sm:inline text-slate-300">•</span>
                                        <span class="flex items-center gap-0.5">
                                            <i class='bx bx-map text-xs text-gold-600'></i> <?= htmlspecialchars($jobLocation) ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </article>
            </div>

            <!-- Right Column (Sidebar) -->
            <aside class="space-y-3 sm:space-y-4 lg:space-y-6 order-1 lg:order-2">
                <!-- Company Info -->
                <div class="company-card fade-in rounded-xl sm:rounded-[24px] lg:rounded-[28px] border border-slate-200 bg-white p-3 sm:p-4 lg:p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3 sm:mb-4 lg:mb-5 border-b border-slate-100 pb-3 sm:pb-4">
                        <i class='bx bx-info-circle text-lg sm:text-xl lg:text-2xl text-gold-600'></i>
                        <h3 class="text-sm sm:text-base lg:text-xl font-black text-slate-900">معلومات</h3>
                    </div>
                    <div class="space-y-2 sm:space-y-2.5 lg:space-y-3 text-[12px] sm:text-xs lg:text-sm text-slate-600">
                        <div class="flex items-center justify-between gap-2 rounded-lg sm:rounded-xl bg-slate-50 p-2.5 sm:p-3">
                            <span class="flex items-center gap-1 text-slate-500 flex-shrink-0">
                                <i class='bx bx-map text-sm sm:text-base text-gold-600'></i> <span class="hidden sm:inline">الموقع</span>
                            </span>
                            <span class="font-bold text-slate-800 text-right text-[11px] sm:text-xs line-clamp-2"><?= htmlspecialchars($location) ?></span>
                        </div>
                        <div class="flex items-center justify-between gap-2 rounded-lg sm:rounded-xl bg-slate-50 p-2.5 sm:p-3">
                            <span class="flex items-center gap-1 text-slate-500 flex-shrink-0">
                                <i class='bx bx-category text-sm sm:text-base text-gold-600'></i> <span class="hidden sm:inline">القطاع</span>
                            </span>
                            <span class="font-bold text-slate-800 text-right text-[11px] sm:text-xs line-clamp-2"><?= htmlspecialchars($industry) ?></span>
                        </div>
                        <?php if ($foundedYear !== 0): ?>
                            <div class="flex items-center justify-between gap-2 rounded-lg sm:rounded-xl bg-slate-50 p-2.5 sm:p-3">
                                <span class="flex items-center gap-1 text-slate-500 flex-shrink-0">
                                    <i class='bx bx-calendar-event text-sm sm:text-base text-gold-600'></i> <span class="hidden sm:inline">تأسست</span>
                                </span>
                                <span class="font-bold text-slate-800 text-[11px] sm:text-xs"><?= htmlspecialchars((string)$foundedYear) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($employees !== ''): ?>
                            <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 p-3">
                                <span class="flex items-center gap-1.5 text-slate-500">
                                    <i class='bx bx-group text-gold-600'></i> الموظفون
                                </span>
                                <span class="font-bold text-slate-800 text-[11px] sm:text-sm"><?= htmlspecialchars($employees) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact -->
                <div class="company-card fade-in rounded-xl sm:rounded-[24px] lg:rounded-[28px] border border-slate-200 bg-white p-3 sm:p-4 lg:p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3 sm:mb-4 lg:mb-5 border-b border-slate-100 pb-3 sm:pb-4">
                        <i class='bx bx-phone-call text-lg sm:text-xl lg:text-2xl text-gold-600'></i>
                        <h3 class="text-sm sm:text-base lg:text-xl font-black text-slate-900">التواصل</h3>
                    </div>
                    <div class="space-y-2 sm:space-y-2.5 lg:space-y-3 text-[12px] sm:text-xs lg:text-sm">
                        <?php if ($email !== ''): ?>
                            <a href="mailto:<?= htmlspecialchars($email) ?>" class="flex items-center justify-between gap-2 rounded-lg sm:rounded-xl bg-slate-50 p-2.5 sm:p-3 transition hover:bg-slate-100 active:scale-98">
                                <span class="flex items-center gap-1 text-slate-500 flex-shrink-0">
                                    <i class='bx bx-envelope text-sm sm:text-base text-gold-600'></i> <span class="hidden sm:inline">البريد</span>
                                </span>
                                <span class="font-bold text-slate-800 text-right text-[10px] sm:text-xs truncate"><?= htmlspecialchars($email) ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($phone !== ''): ?>
                            <a href="tel:<?= htmlspecialchars($phone) ?>" class="flex items-center justify-between gap-2 rounded-lg sm:rounded-xl bg-slate-50 p-2.5 sm:p-3 transition hover:bg-slate-100 active:scale-98">
                                <span class="flex items-center gap-1 text-slate-500 flex-shrink-0">
                                    <i class='bx bx-phone text-sm sm:text-base text-gold-600'></i> <span class="hidden sm:inline">الهاتف</span>
                                </span>
                                <span class="font-bold text-slate-800 text-[10px] sm:text-xs" dir="ltr"><?= htmlspecialchars($phone) ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($website !== ''): ?>
                            <a href="<?= htmlspecialchars($website) ?>" target="_blank" class="flex items-center justify-between gap-2 rounded-lg sm:rounded-xl bg-slate-50 p-2.5 sm:p-3 transition hover:bg-slate-100 active:scale-98">
                                <span class="flex items-center gap-1 text-slate-500 flex-shrink-0">
                                    <i class='bx bx-globe text-sm sm:text-base text-gold-600'></i> <span class="hidden sm:inline">الموقع</span>
                                </span>
                                <span class="flex items-center gap-1 font-bold text-gold-700 text-[10px] sm:text-xs">
                                    <span class="hidden sm:inline">زيارة</span> <i class='bx bx-link-external text-xs sm:text-sm'></i>
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="company-rating" class="company-card fade-in rounded-xl sm:rounded-[24px] lg:rounded-[28px] border border-slate-200 bg-white p-3 sm:p-4 lg:p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3 sm:mb-4 lg:mb-5 border-b border-slate-100 pb-3 sm:pb-4">
                        <i class='bx bx-star text-lg sm:text-xl lg:text-2xl text-gold-600'></i>
                        <h3 class="text-sm sm:text-base lg:text-xl font-black text-slate-900">تقييم الشركة</h3>
                    </div>

                    <div class="rounded-2xl bg-amber-50 p-3 ring-1 ring-amber-100">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-bold text-slate-700">متوسط التقييم</span>
                            <span class="flex items-center gap-1 text-lg font-black text-amber-700">
                                <i class='bx bxs-star'></i> <?= number_format($averageRating, 1) ?>
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">تم تقييم الشركة <?= number_format($ratingsCount) ?> مرة</p>
                    </div>

                    <?php if ($currentUser && !empty($currentUser['id'])): ?>
                        <form method="POST" action="<?= htmlspecialchars(url('/companies/' . $companyId . '/rate')) ?>" class="mt-4 space-y-4">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">اختر تقييمك</label>
                                <div class="flex items-center gap-1.5">
                                    <?php for ($star = 1; $star <= 5; $star++): ?>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="rating" value="<?= $star ?>" class="peer sr-only" <?= (($userRating['rating'] ?? 0) == $star) ? 'checked' : '' ?> required>
                                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-xl text-slate-300 transition peer-checked:border-amber-300 peer-checked:bg-amber-50 peer-checked:text-amber-500 hover:border-amber-200 hover:text-amber-400">
                                                <i class='bx bxs-star'></i>
                                            </span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div>
                                <label for="company-review" class="mb-2 block text-sm font-bold text-slate-700">مراجعتك (اختياري)</label>
                                <textarea id="company-review" name="review" rows="4" class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-100" placeholder="شارك تجربة قصيرة عن الشركة..."><?= htmlspecialchars((string) (($userRating['review'] ?? ''))) ?></textarea>
                            </div>

                            <button type="submit" class="w-full rounded-2xl bg-gradient-to-l from-[#0d2236] to-[#173d5d] px-5 py-3 text-sm font-black text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5">
                                حفظ التقييم
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="mt-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            <a href="<?= htmlspecialchars(url('/login')) ?>" class="font-black text-gold-700 underline">سجل الدخول</a> لتقييم هذه الشركة.
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($latestReviews)): ?>
                        <div class="mt-6">
                            <h4 class="mb-3 text-sm font-black text-slate-800">آخر التقييمات</h4>
                            <div class="space-y-3">
                                <?php foreach ($latestReviews as $review): ?>
                                    <?php $reviewRating = (int) ($review['rating'] ?? 0); ?>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-sm font-black text-slate-800">
                                                <?= htmlspecialchars(trim((string) (($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')) ?: 'مستخدم')) ?>
                                            </span>
                                            <span class="flex items-center gap-1 text-xs font-black text-amber-600">
                                                <?php for ($star = 1; $star <= 5; $star++): ?>
                                                    <i class='bx <?= $star <= $reviewRating ? 'bxs-star' : 'bx-star' ?>'></i>
                                                <?php endfor; ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($review['review'])): ?>
                                            <p class="mt-2 text-sm leading-6 text-slate-600"><?= htmlspecialchars((string) $review['review']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </section>

        <style>
            @media (max-width: 640px) {
                .active\:scale-98:active { transform: scale(0.98); }
            }
        </style>
    </div>
</div>

</body>
</html>