<?php
$savedJobs = $savedJobs ?? [];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'الوظائف المحفوظة') ?></title>
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
                    <h1 class="mt-1 text-3xl font-black">الوظائف المحفوظة</h1>
                </div>
                <div class="flex gap-3">
                    <a href="<?= htmlspecialchars(url('/dashboard')) ?>" class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-bold transition hover:bg-white/10">لوحة التحكم</a>
                    <a href="<?= htmlspecialchars(url('/profile')) ?>" class="rounded-xl bg-gold-500 px-4 py-2 text-sm font-bold text-navy-900 transition hover:bg-gold-400">الملف الشخصي</a>
                </div>
            </div>
        </div>

        <?php if (empty($savedJobs)): ?>
            <div class="rounded-[28px] border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-2xl">🔖</div>
                <h2 class="mt-5 text-2xl font-black text-navy-900">لا توجد وظائف محفوظة</h2>
                <p class="mt-3 text-sm text-slate-500">يمكنك حفظ الوظائف التي تهمك ثم العودة إليها لاحقاً.</p>
                <a href="<?= htmlspecialchars(url('/jobs')) ?>" class="mt-6 inline-block rounded-2xl bg-gold-500 px-5 py-3 text-sm font-black text-navy-900 transition hover:bg-gold-400">استعراض الوظائف</a>
            </div>
        <?php else: ?>
            <div class="grid gap-5">
                <?php foreach ($savedJobs as $job): ?>
                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="mb-3 inline-flex items-center rounded-full bg-gold-50 px-3 py-1 text-xs font-bold text-gold-700">
                                    <?= htmlspecialchars($job['job_type'] ?? 'full-time') ?>
                                </div>
                                <h3 class="text-2xl font-black text-navy-900"><?= htmlspecialchars($job['title'] ?? 'وظيفة') ?></h3>
                                <p class="mt-2 text-sm font-bold text-slate-600"><?= htmlspecialchars($job['company_name'] ?? 'شركة') ?></p>
                            </div>

                            <div class="flex flex-wrap gap-2 text-sm text-slate-600">
                                <span class="rounded-full bg-slate-100 px-3 py-1.5 font-bold"><?= htmlspecialchars($job['experience_level'] ?? 'غير محدد') ?></span>
                                <?php if (!empty($job['salary_min']) || !empty($job['salary_max'])): ?>
                                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 font-bold text-emerald-700">
                                        <?= htmlspecialchars((($job['salary_min'] ?? 0) > 0 ? $job['salary_min'] : '')) ?>
                                        <?= (!empty($job['salary_min']) && !empty($job['salary_max'])) ? ' - ' : '' ?>
                                        <?= htmlspecialchars((($job['salary_max'] ?? 0) > 0 ? $job['salary_max'] : '')) ?>
                                        <?= htmlspecialchars($job['salary_currency'] ?? 'EGP') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-4">
                            <div class="text-sm text-slate-500">
                                تم الحفظ في: <?= htmlspecialchars(date('d/m/Y', strtotime($job['saved_at'] ?? date('Y-m-d')))) ?>
                            </div>
                            <div class="flex gap-3">
                                <a href="<?= htmlspecialchars(url('/jobs/' . ($job['job_id'] ?? 0))) ?>" class="rounded-xl bg-gold-500 px-4 py-2 text-sm font-bold text-navy-900 transition hover:bg-gold-400">عرض الوظيفة</a>
                                <a href="<?= htmlspecialchars(url('/jobs')) ?>" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100">تصفح المزيد</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

