<?php
 $job = $job ?? [
    'id' => 1,
    'title' => 'مطور واجهات أمامية',
    'company' => 'شركة سيف الإلكترونية',
    'location' => 'الرياض',
    'job_type' => 'full-time',
    'salary_min' => 9000,
    'salary_max' => 15000,
    'salary_currency' => 'SAR',
    'salary_negotiable' => 0,
    'description' => 'نبحث عن مطور Frontend مبدع للتعامل مع واجهات المستخدم وتطوير حلول احترافية.',
    'requirements' => 'خبرة لا تقل عن سنتين في HTML, CSS, JavaScript, React. قدرة عالية على التعاون مع فرق المنتج.',
    'benefits' => 'راتب تنافسي، تأمين صحي، إجازات سنوية، فرص نمو.',
    'views_count' => 1850,
    'applications_count' => 24,
    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
];

// التحقق من نوع المستخدم
$currentUser = current_user();
$isEmployer = ($currentUser && ($currentUser['type'] ?? '') === 'employer');

if (!function_exists('formatSalary')) {
    function formatSalary($salaryMin, $salaryMax, $currency = 'SAR', $negotiable = 0): string {
        if (!empty($negotiable)) {
            return 'قابل للتفاوض';
        }
        $currency = strtoupper((string) $currency);
        if ($salaryMin && $salaryMax) {
            return number_format((float) $salaryMin, 0, '.', ',') . ' - ' . number_format((float) $salaryMax, 0, '.', ',') . ' ' . $currency;
        }
        if ($salaryMin) {
            return 'من ' . number_format((float) $salaryMin, 0, '.', ',') . ' ' . $currency;
        }
        if ($salaryMax) {
            return 'حتى ' . number_format((float) $salaryMax, 0, '.', ',') . ' ' . $currency;
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
        ];
        return $map[$value] ?? ucfirst((string) $value);
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

 $jobId = (int) ($job['id'] ?? 0);
 $jobTitle = $job['title'] ?? 'وظيفة';
 $companyName = $job['company_name'] ?? $job['company'] ?? 'شركة';
 $companyLogo = $job['company_logo'] ?? '';
 $companyLogoUrl = $companyLogo !== '' ? asset((string) $companyLogo) : '';
 $location = $job['location'] ?? $job['governorate_name'] ?? $job['city_name'] ?? 'غير محدد';
 $salary = formatSalary($job['salary_min'] ?? null, $job['salary_max'] ?? null, $job['salary_currency'] ?? 'SAR', $job['salary_negotiable'] ?? 0);
 $type = jobTypeLabel((string) ($job['job_type'] ?? 'full-time'));
 $requirements = $job['requirements'] ?? 'لا توجد متطلبات مضافة.';
 $benefits = $job['benefits'] ?? 'مزايا تنافسية متاحة.';
 $benefitsList = [];
 foreach (preg_split('/[\n,؛]+/', (string) $benefits) as $item) {
     $clean = trim((string) $item);
     if ($clean !== '') {
         $benefitsList[] = $clean;
     }
 }
 if ($benefitsList === []) {
     $benefitsList = ['مزايا تنافسية متاحة'];
 }
 $description = $job['description'] ?? 'لا توجد تفاصيل.';
 $createdAt = $job['created_at'] ?? date('Y-m-d H:i:s');
 $views = (int) ($job['views_count'] ?? 0);
 $applications = (int) ($job['applications_count'] ?? 0);
 $applicationMethod = strtolower((string) ($job['application_method'] ?? 'internal'));
 $applyUrl = buildJobApplyUrl($job);
 $useDirectApply = !in_array($applicationMethod, ['internal'], true);
?>
<link href='https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
    .job-card{transition:all .3s cubic-bezier(.4,0,.2,1)}
    .job-card:hover{box-shadow:0 18px 50px -12px rgba(7,27,47,.18);transform:translateY(-2px)}
    .gold-line{background:linear-gradient(90deg,transparent,#d4a64a,transparent)}
    .stat-pill{transition:all .25s ease}
    .stat-pill:hover{background:#f8fafc;transform:translateY(-1px)}
    .apply-section{scroll-margin-top:24px}
    .icon-badge{display:inline-flex;align-items:center;justify-content:center;border-radius:14px}
    .hover-grow{transition:transform .2s ease}
    .hover-grow:hover{transform:scale(1.05)}
    @media (max-width:1023px){.sticky-side{position:static!important}}
    @media (max-width:640px){
        .mobile-header{padding:1rem!important}
        .mobile-action{width:100%!important; justify-content:center!important}
        .mobile-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}
        .mobile-company-row{flex-direction:column!important; align-items:flex-start!important}
        .mobile-info-item{flex-direction:column!important; align-items:flex-start!important; gap:.25rem!important}
        .mobile-form textarea,.mobile-form select,.mobile-form button{font-size:.92rem!important}
        .mobile-list li{padding:.5rem .25rem!important}
    }
</style>

<div class="min-h-screen bg-slate-100" dir="rtl">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <!-- Header -->
        <section class="mobile-header relative overflow-hidden rounded-[28px] bg-gradient-to-l from-[#071b2f] via-[#0d2340] to-[#173d5d] p-6 text-white shadow-[0_24px_60px_rgba(7,27,47,0.28)] sm:p-8 lg:p-10">
            <div class="absolute -top-20 -left-20 h-64 w-64 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-10 h-72 w-72 rounded-full bg-[#173d5d]/40 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-500/15 px-3 py-1 text-xs font-bold text-gold-300 ring-1 ring-gold-500/20">
                            <i class='bx bx-briefcase-alt-2'></i> تفاصيل الوظيفة
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold text-emerald-300 ring-1 ring-emerald-500/20">
                            <i class='bx bx-dot'></i> مفتوح الآن
                        </span>
                    </div>
                    <h1 class="text-2xl font-black leading-tight sm:text-3xl lg:text-4xl"><?= htmlspecialchars($jobTitle) ?></h1>
                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-300">
                        <span class="flex items-center gap-1.5"><i class='bx bx-buildings text-gold-300'></i> <?= htmlspecialchars($companyName) ?></span>
                        <span class="flex items-center gap-1.5"><i class='bx bx-map text-gold-300'></i> <?= htmlspecialchars($location) ?></span>
                        <span class="flex items-center gap-1.5"><i class='bx bx-time text-gold-300'></i> <?= htmlspecialchars($type) ?></span>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:flex-wrap">
                    <a href="<?= htmlspecialchars(url('/jobs')) ?>" class="mobile-action inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/10 hover-grow sm:w-auto">
                        <i class='bx bx-right-arrow-alt'></i> عودة للوظائف
                    </a>
                    <?php if (!$isEmployer): ?>
                        <?php if ($useDirectApply): ?>
                            <a href="<?= htmlspecialchars($applyUrl) ?>" target="_blank" rel="noopener noreferrer" class="mobile-action inline-flex items-center justify-center gap-2 rounded-2xl bg-gold-500 px-6 py-3 text-sm font-black text-slate-900 shadow-lg shadow-gold-500/30 transition hover:bg-gold-400 hover-grow sm:w-auto">
                                <i class='bx bx-send'></i> تقديم الآن
                            </a>
                        <?php else: ?>
                            <a href="#apply" class="mobile-action inline-flex items-center justify-center gap-2 rounded-2xl bg-gold-500 px-6 py-3 text-sm font-black text-slate-900 shadow-lg shadow-gold-500/30 transition hover:bg-gold-400 hover-grow sm:w-auto">
                                <i class='bx bx-send'></i> تقديم الآن
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php $jobShowAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
        <?php if ($jobShowAd !== ''): ?>
        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
            </div>
            <div class="p-2 sm:p-3">
                <?= $jobShowAd ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Grid -->
        <section class="mt-6 grid gap-6 xl:grid-cols-[1.6fr_0.9fr] lg:gap-6">

            <!-- Left Column -->
            <div class="space-y-6">

                <!-- Job Overview Card -->
                <article class="job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="mobile-company-row flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4">
                            <?php if ($companyLogoUrl !== ''): ?>
                                <img src="<?= htmlspecialchars($companyLogoUrl) ?>" alt="<?= htmlspecialchars($companyName) ?>" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-gold-100 shadow-sm" />
                            <?php else: ?>
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-gold-100 to-[#f7efe6] text-2xl font-black text-gold-700 shadow-inner"><?= htmlspecialchars(mb_substr($companyName, 0, 1, 'UTF-8') ?: 'J') ?></div>
                            <?php endif; ?>
                            <div>
                                <h2 class="text-xl font-black text-slate-900 sm:text-2xl"><?= htmlspecialchars($jobTitle) ?></h2>
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-600">
                                    <span class="font-bold text-slate-800"><?= htmlspecialchars($companyName) ?></span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1"><i class='bx bx-map text-slate-400'></i> <?= htmlspecialchars($location) ?></span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1"><i class='bx bx-briefcase text-slate-400'></i> <?= htmlspecialchars($type) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="inline-flex items-center gap-2 self-start rounded-2xl bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> مفتوح الآن
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="mobile-stat-grid mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="stat-pill rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                <i class='bx bx-money text-gold-600'></i> الراتب
                            </div>
                            <div class="mt-2 text-base font-black text-slate-900"><?= htmlspecialchars($salary) ?></div>
                        </div>
                        <div class="stat-pill rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                <i class='bx bx-calendar text-gold-600'></i> نشر منذ
                            </div>
                            <div class="mt-2 text-base font-black text-slate-900"><?= timeAgo((string) $createdAt) ?></div>
                        </div>
                        <div class="stat-pill rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                <i class='bx bx-show text-gold-600'></i> المشاهدات
                            </div>
                            <div class="mt-2 text-base font-black text-slate-900"><?= number_format($views) ?></div>
                        </div>
                        <div class="stat-pill rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                <i class='bx bx-group text-gold-600'></i> المتقدمون
                            </div>
                            <div class="mt-2 text-base font-black text-slate-900"><?= number_format($applications) ?></div>
                        </div>
                    </div>
                </article>

                <!-- Description -->
                <article class="job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="icon-badge h-10 w-10 bg-gold-100 text-gold-700"><i class='bx bx-file text-lg'></i></span>
                        <h3 class="text-xl font-black text-slate-900 sm:text-2xl">وصف الوظيفة</h3>
                    </div>
                    <div class="gold-line h-px w-full opacity-40 mb-5"></div>
                    <p class="text-base leading-9 text-slate-600">
                        <?= nl2br(htmlspecialchars($description)) ?>
                    </p>
                </article>

                <!-- Requirements -->
                <article class="job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="icon-badge h-10 w-10 bg-gold-100 text-gold-700"><i class='bx bx-check-shield text-lg'></i></span>
                        <h3 class="text-xl font-black text-slate-900 sm:text-2xl">المتطلبات</h3>
                    </div>
                    <div class="gold-line h-px w-full opacity-40 mb-5"></div>
                    <ul class="space-y-3 text-base text-slate-600">
                        <?php foreach (explode('.', $requirements) as $item): ?>
                            <?php $clean = trim((string) $item); if ($clean === '') continue; ?>
                            <li class="flex items-start gap-3 rounded-xl p-2 transition hover:bg-slate-50">
                                <span class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-gold-100 text-xs font-black text-gold-700">✓</span>
                                <span class="leading-8"><?= htmlspecialchars($clean) ?>.</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>

                <!-- Benefits -->
                <?php 
                    $benefitsList = [];
                    if (!empty($benefits)) {
                        $benefitsText = (string) $benefits;
                        // حاول الفصل بطرق مختلفة
                        // أولاً: الفاصلة مع مسافة
                        if (strpos($benefitsText, ', ') !== false) {
                            $benefitsList = array_filter(array_map('trim', explode(', ', $benefitsText)), fn($item) => !empty($item));
                        } 
                        // ثانياً: الفاصلة العربية مع مسافة
                        elseif (strpos($benefitsText, '، ') !== false) {
                            $benefitsList = array_filter(array_map('trim', explode('، ', $benefitsText)), fn($item) => !empty($item));
                        }
                        // ثالثاً: الفاصلة المنقوطة
                        elseif (strpos($benefitsText, '؛') !== false) {
                            $benefitsList = array_filter(array_map('trim', explode('؛', $benefitsText)), fn($item) => !empty($item));
                        }
                        // رابعاً: سطر جديد
                        elseif (strpos($benefitsText, "\n") !== false) {
                            $benefitsList = array_filter(array_map('trim', explode("\n", $benefitsText)), fn($item) => !empty($item));
                        }
                        // خامساً: إذا لم يوجد فاصل، افترض أنها ميزة واحدة فقط
                        else {
                            $benefitsList = [trim($benefitsText)];
                        }
                    }
                ?>
                <?php if (!empty($benefitsList)): ?>
                <article class="job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="icon-badge h-10 w-10 bg-emerald-100 text-emerald-700"><i class='bx bx-gift text-lg'></i></span>
                        <h3 class="text-xl font-black text-slate-900 sm:text-2xl">المزايا الوظيفية</h3>
                    </div>
                    <div class="gold-line h-px w-full opacity-40 mb-5"></div>
                    
                    <!-- Benefits Grid -->
                    <div class="grid gap-3 sm:grid-cols-2">
                        <?php foreach ($benefitsList as $benefit): ?>
                            <div class="flex items-start gap-3 rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50/60 to-emerald-50/40 p-4 transition hover:border-emerald-200 hover:shadow-sm">
                                <span class="mt-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white shadow-sm">
                                    <i class='bx bx-check text-base'></i>
                                </span>
                                <span class="text-right text-sm leading-6 text-slate-700 sm:text-base">
                                    <?= htmlspecialchars($benefit) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Benefits Count Badge -->
                    <div class="mt-5 flex items-center justify-center rounded-xl bg-emerald-50 p-3 ring-1 ring-emerald-100">
                        <span class="text-xs font-bold text-emerald-700">
                            <i class='bx bx-star text-emerald-600 ml-1'></i>
                            عدد المزايا: <span class="font-black text-emerald-600"><?= count($benefitsList) ?></span>
                        </span>
                    </div>
                </article>
                <?php endif; ?>
            </div>

            <!-- Right Sidebar -->
            <aside class="space-y-6">
                <div class="sticky-side xl:sticky xl:top-6 space-y-6">

                    <!-- Company Info -->
                    <div class="job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="icon-badge h-10 w-10 bg-[#071b2f] text-gold-300"><i class='bx bx-building-house text-lg'></i></span>
                            <h3 class="text-lg font-black text-slate-900">معلومات الشركة</h3>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="mobile-info-item flex items-center justify-between rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-100">
                                <span class="flex items-center gap-2 text-slate-500"><i class='bx bx-buildings text-slate-400'></i> اسم الشركة</span>
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($companyName) ?></span>
                            </div>
                            <div class="mobile-info-item flex items-center justify-between rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-100">
                                <span class="flex items-center gap-2 text-slate-500"><i class='bx bx-map text-slate-400'></i> الموقع</span>
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($location) ?></span>
                            </div>
                            <div class="mobile-info-item flex items-center justify-between rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-100">
                                <span class="flex items-center gap-2 text-slate-500"><i class='bx bx-briefcase text-slate-400'></i> نوع الوظيفة</span>
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($type) ?></span>
                            </div>
                            <div class="mobile-info-item flex items-center justify-between rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-100">
                                <span class="flex items-center gap-2 text-slate-500"><i class='bx bx-money text-slate-400'></i> الراتب</span>
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($salary) ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if (!$isEmployer): ?>
                        <div id="apply" class="apply-section job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm">
                            <?php if ($useDirectApply): ?>
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="icon-badge h-10 w-10 bg-gold-500 text-slate-900"><i class='bx bx-send text-lg'></i></span>
                                    <h3 class="text-lg font-black text-slate-900">التقديم على الوظيفة</h3>
                                </div>
                                <p class="mt-3 text-sm leading-7 text-slate-600">
                                    ستنتقل مباشرة إلى
                                    <span class="font-bold text-[#071b2f]">
                                        <?= $applicationMethod === 'whatsapp' ? 'واتساب الشركة' : ($applicationMethod === 'phone' ? 'اتصال هاتفي' : ($applicationMethod === 'email' ? 'البريد الإلكتروني' : 'الرابط الخارجي')) ?>
                                    </span>
                                    لإكمال عملية التقديم.
                                </p>
                                <a href="<?= htmlspecialchars($applyUrl) ?>" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-[#0d2236] to-[#173d5d] px-5 py-4 text-sm font-black text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5 hover:shadow-xl">
                                    <i class='bx bx-send'></i> التقديم الآن
                                </a>
                                <p class="mt-3 text-center text-xs text-slate-400">
                                    <i class='bx bx-shield-quarter'></i> تقديم آمن وموثوق
                                </p>
                            <?php else: ?>
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="icon-badge h-10 w-10 bg-gold-500 text-slate-900"><i class='bx bx-send text-lg'></i></span>
                                    <h3 class="text-lg font-black text-slate-900">التقديم على الوظيفة</h3>
                                </div>
                                <form method="POST" action="<?= htmlspecialchars(url('/jobs/' . $jobId . '/apply')) ?>" class="mobile-form mt-4 space-y-4">
                                    <div>
                                        <label class="mb-2 flex items-center gap-1.5 text-sm font-bold text-slate-700">
                                            <i class='bx bx-message-rounded-dots text-gold-600'></i> رسالة شخصية
                                        </label>
                                        <textarea name="cover_letter" rows="5" class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-100" placeholder="اكتب نبذة مختصرة عنك ومؤهلاتك..."></textarea>
                                    </div>
                                    <div>
                                        <label class="mb-2 flex items-center gap-1.5 text-sm font-bold text-slate-700">
                                            <i class='bx bx-file-blank text-gold-600'></i> السيرة الذاتية
                                        </label>
                                        <select name="resume_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-gold-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-100">
                                            <option value="0">اختر سيرتك الذاتية</option>
                                            <option value="1">السيرة الذاتية الأساسية</option>
                                            <option value="2">السيرة الذاتية الاحترافية</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-[#0d2236] to-[#173d5d] px-5 py-4 text-sm font-black text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5 hover:shadow-xl">
                                        <i class='bx bx-paper-plane'></i> إرسال الطلب
                                    </button>
                                    <p class="text-center text-xs text-slate-400">
                                        <i class='bx bx-lock'></i> بياناتك آمنة ولن تُشارك مع أي طرف ثالث
                                    </p>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </section>
    </div>
</div>