<?php
 $jobs = $jobs ?? [];
 $categories = $categories ?? [];
 $governorates = $governorates ?? [];
 $cities = $cities ?? [];
 $currentUser = current_user();
 $isEmployer = ($currentUser && ($currentUser['type'] ?? '') === 'employer');

 $keyword = trim((string) ($keyword ?? ''));
 $selectedCategoryId = (int) ($selectedCategoryId ?? 0);
 $selectedGovernorateId = (int) ($selectedGovernorateId ?? 0);
 $selectedCityId = (int) ($selectedCityId ?? 0);
 $selectedJobType = (string) ($selectedJobType ?? '');
 $selectedExperience = (string) ($selectedExperience ?? '');
 $salaryMin = (int) ($salaryMin ?? 0);
 $salaryMax = (int) ($salaryMax ?? 0);
 $page = max(1, (int) ($page ?? 1));
 $totalPages = max(1, (int) ($totalPages ?? 1));

if (!function_exists('jobsPageUrl')) {
    function jobsPageUrl(int $targetPage, array $params = []): string {
        $query = [];

        if (!empty($params['q'])) {
            $query['q'] = $params['q'];
        }
        if (!empty($params['category'])) {
            $query['category'] = (int) $params['category'];
        }
        if (!empty($params['governorate'])) {
            $query['governorate'] = (int) $params['governorate'];
        }
        if (!empty($params['city'])) {
            $query['city'] = (int) $params['city'];
        }
        if (!empty($params['job_type'])) {
            $query['job_type'] = $params['job_type'];
        }
        if (!empty($params['experience'])) {
            $query['experience'] = $params['experience'];
        }
        if (!empty($params['salary_min'])) {
            $query['salary_min'] = (int) $params['salary_min'];
        }
        if (!empty($params['salary_max'])) {
            $query['salary_max'] = (int) $params['salary_max'];
        }

        $query['page'] = max(1, $targetPage);

        return url('/jobs') . '?' . http_build_query($query);
    }
}

$jobsPaginationParams = [
    'q' => $keyword,
    'category' => $selectedCategoryId,
    'governorate' => $selectedGovernorateId,
    'city' => $selectedCityId,
    'job_type' => $selectedJobType,
    'experience' => $selectedExperience,
    'salary_min' => $salaryMin,
    'salary_max' => $salaryMax,
];

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

if (!function_exists('jobTypeColor')) {
    function jobTypeColor(string $value): string {
        $map = [
            'full-time' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'part-time' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'contract' => 'bg-violet-50 text-violet-700 ring-violet-200',
            'temporary' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'internship' => 'bg-pink-50 text-pink-700 ring-pink-200',
            'remote' => 'bg-teal-50 text-teal-700 ring-teal-200',
        ];
        return $map[$value] ?? 'bg-slate-50 text-slate-700 ring-slate-200';
    }
}

if (!function_exists('formatSalary')) {
    function formatSalary($salaryMin, $salaryMax, $currency = 'SAR', $negotiable = 0): string {
        if (!empty($negotiable)) {
            return 'قابل للتفاوض';
        }
        $currency = strtoupper((string) $currency);
        $currencySymbol = $currency === 'SAR' ? 'ر.س' : $currency;
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

if (!function_exists('timeAgo')) {
    function timeAgo(string $date): string {
        try {
            $dt = new DateTime($date);
            $diff = $dt->diff(new DateTime());
            if ($diff->y > 0) return 'منذ ' . $diff->y . ' سنة';
            if ($diff->m > 0) return 'منذ ' . $diff->m . ' شهر';
            if ($diff->d > 0) return 'منذ ' . $diff->d . ' يوم';
            if ($diff->h > 0) return 'منذ ' . $diff->h . ' ساعة';
            if ($diff->i > 0) return 'منذ ' . $diff->i . ' دقيقة';
            return 'منذ لحظات';
        } catch (Throwable $e) {
            return 'حديثاً';
        }
    }
}

if (!function_exists('experienceLabel')) {
    function experienceLabel(string $value): string {
        $map = [
            '0-1' => 'مبتدئ (0 - 1 سنة)',
            '1-2' => '1 - 2 سنة',
            '2-3' => '2 - 3 سنوات',
            '3-5' => '3 - 5 سنوات',
            '5+' => 'أكثر من 5 سنوات',
        ];
        return $map[$value] ?? $value;
    }
}

if (!function_exists('buildJobApplyUrl')) {
    function buildJobApplyUrl(array $job): string {
        $method = strtolower((string) ($job['application_method'] ?? 'internal'));
        $jobTitle = trim((string) ($job['title'] ?? 'وظيفة'));
        $companyName = trim((string) ($job['company_name'] ?? $job['company'] ?? 'شركة'));
        $message = 'السلام عليكم، أود التقديم على وظيفة ' . $jobTitle . ($companyName !== '' ? ' في ' . $companyName : '');

        if ($method === 'whatsapp') {
            $raw = trim((string) ($job['application_whatsapp'] ?? ''));
            if ($raw !== '') {
                $clean = preg_replace('/[^0-9]/', '', $raw);
                if ($clean !== '') {
                    return 'https://wa.me/' . rawurlencode($clean) . '?text=' . rawurlencode($message);
                }
            }
        }

        if ($method === 'phone') {
            $raw = trim((string) ($job['application_phone'] ?? ''));
            if ($raw !== '') {
                $clean = preg_replace('/[^0-9+]/', '', $raw);
                if ($clean !== '') {
                    return 'tel:' . rawurlencode($clean);
                }
            }
        }

        if ($method === 'email') {
            $raw = trim((string) ($job['application_email'] ?? ''));
            if ($raw !== '') {
                return 'mailto:' . rawurlencode($raw) . '?subject=' . rawurlencode('التقديم على وظيفة ' . $jobTitle) . '&body=' . rawurlencode($message);
            }
        }

        if ($method === 'external_link') {
            $raw = trim((string) ($job['application_link'] ?? ''));
            if ($raw !== '') {
                $url = $raw;
                if (!preg_match('#^https?://#i', $url)) {
                    $url = 'https://' . ltrim($url, '/');
                }
                return $url;
            }
        }

        return url('/jobs/' . (int) ($job['id'] ?? 0) . '#apply');
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جميع الوظائف</title>
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

        .fade-in { animation: fadeIn .4s ease both; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px);} to {opacity:1; transform: translateY(0);} }
    </style>
</head>
<body class="text-slate-800 antialiased">

<div class="min-h-screen">
    <div class="mx-auto max-w-7xl px-3 py-4 sm:px-6 sm:py-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="mb-4 flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500">
            <a href="<?= htmlspecialchars(url('/')) ?>" class="flex items-center gap-1 hover:text-gold-600">
                <i class='bx bx-home text-sm sm:text-base'></i> الرئيسية
            </a>
            <i class='bx bx-chevron-left text-slate-400'></i>
            <span class="text-slate-800">جميع الوظائف</span>
        </nav>

        <!-- HERO -->
        <section class="relative overflow-hidden rounded-[20px] sm:rounded-[30px] bg-gradient-to-br from-[#071b2f] via-[#0d2340] to-[#173d5d] p-5 sm:p-8 text-white shadow-[0_15px_40px_rgba(7,27,47,0.25)] hero-pattern">
            <div class="absolute -top-16 -left-16 h-48 w-48 sm:h-72 sm:w-72 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -right-8 h-48 w-48 sm:h-72 sm:w-72 rounded-full bg-sky-500/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-4 sm:gap-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-1.5 rounded-full border border-gold-400/30 bg-gold-400/10 px-3 py-1 text-[10px] sm:text-xs font-bold text-gold-300">
                            <i class='bx bx-briefcase-alt-2'></i> فرص عمل متميزة
                        </div>
                        <h1 class="mt-3 text-2xl sm:text-4xl font-black leading-tight">
                            ابحث عن <span class="text-gold-400">الوظيفة</span> التي تليق بطموحك
                        </h1>
                        <p class="mt-2 text-xs sm:text-sm leading-6 text-slate-300">
                            استكشف آلاف الفرص الوظيفية في مختلف القطاعات والمدن، وطبّق فلاتر متقدمة لتصل إلى وظيفتك المثالية.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 self-start rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-xs sm:text-sm font-bold backdrop-blur">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        <span class="text-slate-200"><?= count($jobs) ?> وظيفة متاحة</span>
                    </div>
                </div>

                <!-- Quick Search -->
                <form method="GET" action="<?= htmlspecialchars(url('/jobs')) ?>" class="relative grid gap-2.5 sm:gap-3 rounded-2xl sm:rounded-3xl border border-white/10 bg-white/5 p-2.5 sm:p-3 backdrop-blur md:grid-cols-[1.6fr_1fr_1fr_auto] md:items-center">
                    <div class="flex items-center gap-2 rounded-xl sm:rounded-2xl bg-white px-3 py-2.5 sm:py-3">
                        <i class='bx bx-search-alt text-lg sm:text-xl text-slate-400'></i>
                        <input id="q" name="q" value="<?= htmlspecialchars($keyword) ?>" class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none" placeholder="ابحث عن وظيفة، شركة..." />
                    </div>
                    <div class="flex items-center gap-2 rounded-xl sm:rounded-2xl bg-white px-3 py-2.5 sm:py-3">
                        <i class='bx bx-category text-lg sm:text-xl text-slate-400'></i>
                        <select name="category" class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none">
                            <option value="">كل الأقسام</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) ($category['id'] ?? 0) ?>" <?= ((int) ($category['id'] ?? 0)) === $selectedCategoryId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($category['name'] ?? '')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 rounded-xl sm:rounded-2xl bg-white px-3 py-2.5 sm:py-3">
                        <i class='bx bx-map text-lg sm:text-xl text-slate-400'></i>
                        <select name="location" class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none">
                            <option value="">كل المدن</option>
                            <?php foreach ($governorates as $governorate): ?>
                                <option value="<?= (int) ($governorate['id'] ?? 0) ?>" <?= ((int) ($governorate['id'] ?? 0)) === $selectedGovernorateId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($governorate['name'] ?? '')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-gold flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-black text-white transition hover:-translate-y-0.5">
                        <i class='bx bx-search text-lg'></i> بحث
                    </button>
                </form>
            </div>
        </section>

        <?php $jobsTopAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
        <?php if ($jobsTopAd !== ''): ?>
        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
            </div>
            <div class="p-2 sm:p-3">
                <?= $jobsTopAd ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- MAIN GRID -->
        <section class="mt-5 sm:mt-7 grid gap-4 sm:gap-6 lg:grid-cols-[290px_minmax(0,1fr)]">

            <!-- SIDEBAR FILTERS -->
            <aside class="lg:sticky lg:top-4 lg:self-start space-y-4">

                <!-- Mobile Toggle -->
                <button onclick="toggleFilters()" class="lg:hidden flex w-full items-center justify-center gap-2 rounded-2xl bg-navy-800 px-5 py-3.5 text-sm font-black text-white shadow-lg">
                    <i class='bx bx-filter-alt text-lg'></i>
                    <span>عرض الفلاتر المتقدمة</span>
                </button>

                <div id="filtersPanel" class="hidden lg:block space-y-4">

                    <form method="GET" action="<?= htmlspecialchars(url('/jobs')) ?>" class="space-y-4">

                        <!-- Quick Stats -->
                        <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold-100 text-gold-700">
                                    <i class='bx bx-line-chart text-xl'></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-slate-500">إجمالي النتائج</p>
                                    <p class="text-lg font-black text-slate-900"><?= count($jobs) ?> وظيفة</p>
                                </div>
                            </div>
                        </div>

                        <!-- Governorate & City -->
                        <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-4 shadow-sm space-y-4">
                            <div>
                                <div class="mb-2.5 flex items-center gap-2">
                                    <i class='bx bx-map text-lg text-gold-600'></i>
                                    <h3 class="text-sm font-black text-slate-900">المحافظة</h3>
                                </div>
                                <select id="sidebar-governorate-select" name="governorate" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-200">
                                    <option value="">كل المحافظات</option>
                                    <?php foreach ($governorates as $governorate): ?>
                                        <option value="<?= (int) ($governorate['id'] ?? 0) ?>" <?= ((int) ($governorate['id'] ?? 0)) === $selectedGovernorateId ? 'selected' : '' ?>>
                                            <?= htmlspecialchars((string) ($governorate['name'] ?? '')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <div class="mb-2.5 flex items-center gap-2">
                                    <i class='bx bx-buildings text-lg text-gold-600'></i>
                                    <h3 class="text-sm font-black text-slate-900">المدينة</h3>
                                </div>
                                <select id="sidebar-city-select" name="city" data-selected-city="<?= (int) $selectedCityId ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-200">
                                    <option value="">كل المدن</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= (int) ($city['id'] ?? 0) ?>" data-governorate-id="<?= (int) ($city['governorate_id'] ?? 0) ?>" <?= ((int) ($city['id'] ?? 0)) === $selectedCityId ? 'selected' : '' ?>>
                                            <?= htmlspecialchars((string) ($city['name'] ?? '')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Category / Department -->
                        <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="mb-3 flex items-center gap-2">
                                <i class='bx bx-category-alt text-lg text-gold-600'></i>
                                <h3 class="text-sm font-black text-slate-900">القسم</h3>
                            </div>
                            <div class="space-y-2">
                                <?php foreach ($categories as $category):
                                    $categoryId = (int) ($category['id'] ?? 0);
                                    $categoryName = (string) ($category['name'] ?? '');
                                    $categoryCount = (int) ($category['jobs_count'] ?? 0);
                                ?>
                                    <label class="filter-chip flex cursor-pointer items-center justify-between rounded-xl px-3 py-2.5 <?= $selectedCategoryId === $categoryId ? 'bg-gold-50 ring-1 ring-gold-300' : 'hover:bg-slate-50' ?>">
                                        <span class="flex items-center gap-2.5 text-xs sm:text-sm font-bold text-slate-700">
                                            <input type="radio" name="category" value="<?= $categoryId ?>" class="hidden peer" <?= $selectedCategoryId === $categoryId ? 'checked' : '' ?> />
                                            <i class='bx bx-category-alt text-base text-gold-600'></i>
                                            <?= htmlspecialchars($categoryName) ?>
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500"><?= $categoryCount ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Salary -->
                        <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="mb-3 flex items-center gap-2">
                                <i class='bx bx-money text-lg text-gold-600'></i>
                                <h3 class="text-sm font-black text-slate-900">الراتب</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="mb-1 block text-[10px] font-bold text-slate-500">الحد الأدنى</label>
                                        <input type="number" name="salary_min" value="<?= $salaryMin ?: '' ?>" placeholder="0" min="0" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-200" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[10px] font-bold text-slate-500">الحد الأقصى</label>
                                        <input type="number" name="salary_max" value="<?= $salaryMax ?: '' ?>" placeholder="20000" min="0" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-200" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Experience -->
                        <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="mb-3 flex items-center gap-2">
                                <i class='bx bx-trophy text-lg text-gold-600'></i>
                                <h3 class="text-sm font-black text-slate-900">الخبرة</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <?php
                                $experiences = [
                                    '' => ['الكل', 'bx-list-ul'],
                                    'entry' => ['مبتدئ', 'bx-seedling'],
                                    'junior' => ['1-2 سنة', 'bx-leaf'],
                                    'mid' => ['2-3 سنوات', 'bx-sprout'],
                                    'senior' => ['3-5 سنوات', 'bx-tree'],
                                    'lead' => ['+5 سنوات', 'bx-tachometer'],
                                ];
                                foreach ($experiences as $val => $exp):
                                ?>
                                    <label class="filter-chip flex cursor-pointer items-center gap-1.5 rounded-xl px-2 py-2 text-[11px] sm:text-xs font-bold text-slate-700 <?= $selectedExperience === $val ? 'bg-gold-50 ring-1 ring-gold-300' : 'bg-slate-50 hover:bg-slate-100' ?>">
                                        <input type="radio" name="experience" value="<?= $val ?>" class="hidden" <?= $selectedExperience === $val ? 'checked' : '' ?> />
                                        <i class='bx <?= $exp[1] ?> text-sm text-gold-600'></i>
                                        <span class="truncate"><?= $exp[0] ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Job Type -->
                        <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="mb-3 flex items-center gap-2">
                                <i class='bx bx-briefcase-alt-2 text-lg text-gold-600'></i>
                                <h3 class="text-sm font-black text-slate-900">نوع الوظيفة</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <?php
                                $types = [
                                    '' => ['الكل', 'bx-grid-alt'],
                                    'full-time' => ['دوام كامل', 'bx-briefcase'],
                                    'part-time' => ['جزئي', 'bx-time-five'],
                                    'contract' => ['عقد', 'bx-file'],
                                    'temporary' => ['مؤقت', 'bx-calendar'],
                                    'internship' => ['تدريب', 'bx-graduation'],
                                    'remote' => ['عن بعد', 'bx-home-heart'],
                                ];
                                foreach ($types as $val => $t):
                                ?>
                                    <label class="filter-chip flex cursor-pointer items-center gap-1.5 rounded-xl px-2 py-2 text-[11px] sm:text-xs font-bold text-slate-700 <?= $selectedJobType === $val ? 'bg-navy-800 text-white' : 'bg-slate-50 hover:bg-slate-100' ?>">
                                        <input type="radio" name="job_type" value="<?= $val ?>" class="hidden" <?= $selectedJobType === $val ? 'checked' : '' ?> />
                                        <i class='bx <?= $t[1] ?> text-sm'></i>
                                        <span class="truncate"><?= $t[0] ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-gold flex-1 flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-xs sm:text-sm font-black text-white">
                                <i class='bx bx-filter-alt text-lg'></i> تطبيق
                            </button>
                            <a href="<?= htmlspecialchars(url('/jobs')) ?>" class="flex items-center justify-center gap-1 rounded-2xl bg-slate-100 px-4 py-3 text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-200" title="إعادة ضبط">
                                <i class='bx bx-revision text-lg'></i>
                            </a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- JOB LIST -->
            <div class="space-y-4 sm:space-y-5">

                <!-- Toolbar -->
                <div class="flex flex-col gap-2 rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-600">
                        <i class='bx bx-list-ul text-lg sm:text-xl text-gold-600'></i>
                        عرض <span class="text-slate-900"><?= count($jobs) ?></span> وظيفة
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-[11px] sm:text-xs font-bold text-slate-500">ترتيب:</label>
                        <select class="rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-2 text-[11px] sm:text-xs font-bold text-slate-700 focus:border-gold-400 focus:outline-none">
                            <option>الأحدث</option>
                            <option>الأعلى راتباً</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($jobs)): ?>
                    <div class="rounded-[20px] sm:rounded-[28px] border-2 border-dashed border-slate-300 bg-white p-8 sm:p-12 text-center shadow-sm">
                        <div class="mx-auto flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-2xl sm:rounded-3xl bg-gold-100 text-3xl sm:text-4xl text-gold-700">
                            <i class='bx bx-search-alt'></i>
                        </div>
                        <h2 class="mt-4 sm:mt-5 text-xl sm:text-2xl font-black text-slate-900">لا توجد وظائف مطابقة</h2>
                        <p class="mt-2 text-xs sm:text-sm text-slate-500">حاول تغيير كلمات البحث أو تعديل الفلاتر.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jobs as $idx => $job):
                        $jobId = (int) ($job['id'] ?? 0);
                        $jobTitle = $job['title'] ?? 'وظيفة';
                        $companyName = $job['company_name'] ?? $job['company'] ?? 'شركة';
                        $companyLogo = $job['company_logo'] ?? '';
                        $companyLogoUrl = $companyLogo !== '' ? asset((string) $companyLogo) : '';
                        $location = $job['location'] ?? $job['governorate_name'] ?? $job['city_name'] ?? 'غير محدد';
                        $jobType = (string) ($job['job_type'] ?? 'full-time');
                        $type = jobTypeLabel($jobType);
                        $typeIcon = jobTypeIcon($jobType);
                        $typeColor = jobTypeColor($jobType);
                        $salary = formatSalary($job['salary_min'] ?? null, $job['salary_max'] ?? null, $job['salary_currency'] ?? 'SAR', $job['salary_negotiable'] ?? 0);
                        $createdAt = $job['created_at'] ?? date('Y-m-d H:i:s');
                        $description = $job['description'] ?? 'تفاصيل الوظيفة متاحة في صفحة التفاصيل.';
                        $views = (int) ($job['views_count'] ?? 0);
                        $applications = (int) ($job['applications_count'] ?? 0);
                        $firstLetter = mb_substr($companyName, 0, 1, 'UTF-8');
                        $isRecent = strtotime($createdAt) > strtotime('-1 day');
                        $applyUrl = buildJobApplyUrl($job);
                        $useDirectApply = !in_array(strtolower((string) ($job['application_method'] ?? 'internal')), ['internal'], true);
                    ?>
                        <article class="job-card fade-in group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm hover:border-gold-200" style="animation-delay: <?= $idx * 80 ?>ms">
                            <span class="absolute top-0 inset-x-0 h-1 bg-gradient-to-l from-gold-400 via-gold-500 to-transparent opacity-0 transition group-hover:opacity-100"></span>

                            <div class="flex flex-col gap-3 sm:gap-4">
                                <!-- Top Section -->
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="relative flex-shrink-0">
                                        <?php if ($companyLogoUrl !== ''): ?>
                                            <img src="<?= htmlspecialchars($companyLogoUrl) ?>" alt="<?= htmlspecialchars($companyName) ?>" class="h-12 w-12 rounded-xl object-cover ring-2 ring-gold-100 shadow-sm sm:h-16 sm:w-16 sm:rounded-2xl" />
                                        <?php else: ?>
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-navy-800 to-navy-700 text-lg font-black text-gold-400 shadow-lg sm:h-16 sm:w-16 sm:rounded-2xl sm:text-2xl">
                                                <?= htmlspecialchars($firstLetter) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($isRecent): ?>
                                            <span class="absolute -top-1 -left-1 flex h-4 w-4 sm:h-5 sm:w-5 items-center justify-center rounded-full bg-emerald-500 text-[8px] sm:text-[10px] text-white ring-2 ring-white" title="جديد">
                                                <i class='bx bxs-zap'></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-base sm:text-xl font-black text-slate-900 leading-snug">
                                                <a href="<?= htmlspecialchars(url('/jobs/' . $jobId)) ?>" class="transition hover:text-gold-700">
                                                    <?= htmlspecialchars($jobTitle) ?>
                                                </a>
                                            </h3>
                                        </div>

                                        <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] sm:text-sm text-slate-600">
                                            <a href="#" class="flex items-center gap-1 font-bold text-slate-800 hover:text-gold-700">
                                                <i class='bx bx-building text-gold-600'></i>
                                                <?= htmlspecialchars($companyName) ?>
                                            </a>
                                            <span class="text-slate-300">|</span>
                                            <span class="flex items-center gap-1">
                                                <i class='bx bx-map text-gold-600'></i>
                                                <?= htmlspecialchars($location) ?>
                                            </span>
                                        </div>

                                        <div class="mt-2.5 flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] sm:text-xs font-black ring-1 <?= $typeColor ?>">
                                                <i class='bx <?= $typeIcon ?>'></i>
                                                <?= htmlspecialchars($type) ?>
                                            </span>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-navy-50 px-2.5 py-1 text-[10px] sm:text-xs font-black text-navy-800 ring-1 ring-navy-200">
                                                <i class='bx bx-money'></i>
                                                <?= htmlspecialchars($salary) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <p class="line-clamp-2 text-xs sm:text-sm leading-6 text-slate-500">
                                    <?= htmlspecialchars($description) ?>
                                </p>

                                <!-- Footer -->
                                <div class="flex flex-col gap-3 border-t border-slate-100 pt-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[10px] sm:text-xs font-bold text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <i class='bx bx-time-five text-gold-500'></i> <?= timeAgo((string) $createdAt) ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class='bx bx-show-alt text-gold-500'></i> <?= number_format($views) ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class='bx bx-user-plus text-gold-500'></i> <?= number_format($applications) ?> متقدم
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="<?= htmlspecialchars(url('/jobs/' . $jobId . '/save')) ?>" class="inline-block">
                                            <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-xl border transition-colors <?= in_array($jobId, $savedJobIds ?? [], true) ? 'border-gold-300 bg-gold-50 text-gold-700' : 'border-slate-200 bg-white text-slate-500 hover:border-gold-300 hover:text-gold-600' ?>" aria-label="حفظ الوظيفة" title="<?= in_array($jobId, $savedJobIds ?? [], true) ? 'تم الحفظ' : 'حفظ الوظيفة' ?>">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="<?= in_array($jobId, $savedJobIds ?? [], true) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                                            </button>
                                        </form>
                                        <a href="<?= htmlspecialchars(url('/jobs/' . $jobId)) ?>" class="flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[11px] sm:text-sm font-black text-slate-800 transition hover:bg-slate-50 sm:flex-none">
                                            <i class='bx bx-detail'></i> التفاصيل
                                        </a>
                                        <?php if (!$isEmployer): ?>
                                            <?php if ($useDirectApply): ?>
                                                <a href="<?= htmlspecialchars($applyUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn-navy flex flex-1 items-center justify-center gap-1.5 rounded-xl px-3 py-2 text-[11px] sm:text-sm font-black text-white transition hover:-translate-y-0.5 sm:flex-none">
                                                    <i class='bx bx-send'></i> تقديم
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= htmlspecialchars(url('/jobs/' . $jobId)) ?>" class="btn-navy flex flex-1 items-center justify-center gap-1.5 rounded-xl px-3 py-2 text-[11px] sm:text-sm font-black text-white transition hover:-translate-y-0.5 sm:flex-none">
                                                    <i class='bx bx-send'></i> تقديم
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Pagination -->
                <?php $jobsBottomAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
                <?php if ($jobsBottomAd !== ''): ?>
                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                        <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
                    </div>
                    <div class="p-2 sm:p-3">
                        <?= $jobsBottomAd ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($totalPages > 1): ?>
                    <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        if ($endPage - $startPage < 4) {
                            if ($startPage === 1) {
                                $endPage = min($totalPages, $startPage + 4);
                            } else {
                                $startPage = max(1, $endPage - 4);
                            }
                        }
                    ?>
                    <div class="mt-6 sm:mt-8 flex justify-center">
                        <nav class="inline-flex items-center gap-1 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm">
                            <?php if ($page > 1): ?>
                                <a href="<?= htmlspecialchars(jobsPageUrl($page - 1, $jobsPaginationParams)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="السابق">
                                    <i class='bx bx-chevron-right text-xl'></i>
                                </a>
                            <?php else: ?>
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300">
                                    <i class='bx bx-chevron-right text-xl'></i>
                                </span>
                            <?php endif; ?>

                            <?php if ($startPage > 1): ?>
                                <a href="<?= htmlspecialchars(jobsPageUrl(1, $jobsPaginationParams)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-100">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="flex h-9 w-9 items-center justify-center text-slate-400">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <a href="<?= htmlspecialchars(jobsPageUrl($i, $jobsPaginationParams)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black <?= $i === $page ? 'btn-gold text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="flex h-9 w-9 items-center justify-center text-slate-400">...</span>
                                <?php endif; ?>
                                <a href="<?= htmlspecialchars(jobsPageUrl($totalPages, $jobsPaginationParams)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-100"><?= $totalPages ?></a>
                            <?php endif; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="<?= htmlspecialchars(jobsPageUrl($page + 1, $jobsPaginationParams)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="التالي">
                                    <i class='bx bx-chevron-left text-xl'></i>
                                </a>
                            <?php else: ?>
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300">
                                    <i class='bx bx-chevron-left text-xl'></i>
                                </span>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script>
    function toggleFilters() {
        const panel = document.getElementById('filtersPanel');
        panel.classList.toggle('hidden');
    }

    function applyGovernorateCityFilter() {
        const governorateSelect = document.getElementById('sidebar-governorate-select');
        const citySelect = document.getElementById('sidebar-city-select');

        if (!governorateSelect || !citySelect) {
            return;
        }

        const allCityOptions = Array.from(citySelect.options).filter(function (option) {
            return option.value !== '';
        });

        function renderCities(selectedGovernorateId) {
            const selectedGovernorate = Number(selectedGovernorateId) || 0;
            const selectedCity = Number(citySelect.dataset.selectedCity || 0);
            const filteredOptions = allCityOptions.filter(function (option) {
                const optionGovernorate = Number(option.dataset.governorateId || 0);
                return !selectedGovernorate || optionGovernorate === selectedGovernorate;
            });

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'كل المدن';

            const nextOptions = [placeholder].concat(filteredOptions.map(function (option) {
                return option.cloneNode(true);
            }));

            citySelect.innerHTML = '';
            nextOptions.forEach(function (option) {
                citySelect.appendChild(option);
            });

            const validSelectedCity = filteredOptions.some(function (option) {
                return Number(option.value) === selectedCity;
            });

            citySelect.value = validSelectedCity ? String(selectedCity) : '';
            citySelect.dataset.selectedCity = validSelectedCity ? String(selectedCity) : '0';
        }

        governorateSelect.addEventListener('change', function () {
            renderCities(this.value);
        });

        renderCities(governorateSelect.value);
    }

    document.addEventListener('DOMContentLoaded', function () {
        applyGovernorateCityFilter();

        const forms = document.querySelectorAll('form[action*="/jobs"]');

        forms.forEach(function (form) {
            const radios = form.querySelectorAll('input[type="radio"]');
            radios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    form.submit();
                });
            });

            const selects = form.querySelectorAll('select');
            selects.forEach(function (select) {
                select.addEventListener('change', function () {
                    form.submit();
                });
            });
        });
    });
</script>

</body>
</html>