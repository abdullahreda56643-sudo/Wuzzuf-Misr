<?php
$applications = $applications ?? [];
$statusLabels = [
    'pending' => ['label' => 'قيد المراجعة', 'class' => 'bg-amber-100 text-amber-700'],
    'reviewed' => ['label' => 'تمت المراجعة', 'class' => 'bg-blue-100 text-blue-700'],
    'shortlisted' => ['label' => 'مستبعد', 'class' => 'bg-emerald-100 text-emerald-700'],
    'rejected' => ['label' => 'مرفوض', 'class' => 'bg-red-100 text-red-700'],
    'accepted' => ['label' => 'مقبول', 'class' => 'bg-green-100 text-green-700'],
    'withdrawn' => ['label' => 'ملغي', 'class' => 'bg-slate-200 text-slate-700'],
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'الوظائف التي تقدم إليها') ?></title>
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
                    <h1 class="mt-1 text-3xl font-black">الوظائف التي تقدم إليها</h1>
                </div>
                <div class="flex gap-3">
                    <a href="<?= htmlspecialchars(url('/dashboard')) ?>" class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-bold transition hover:bg-white/10">لوحة التحكم</a>
                    <a href="<?= htmlspecialchars(url('/profile')) ?>" class="rounded-xl bg-gold-500 px-4 py-2 text-sm font-bold text-navy-900 transition hover:bg-gold-400">الملف الشخصي</a>
                </div>
            </div>
        </div>

        <?php if (empty($applications)): ?>
            <div class="rounded-[28px] border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-2xl">📄</div>
                <h2 class="mt-5 text-2xl font-black text-navy-900">لم تتقدم لأي وظيفة بعد</h2>
                <p class="mt-3 text-sm text-slate-500">ابدأ بتصفح الوظائف المناسبة لك وتقدم لها مباشرة.</p>
                <a href="<?= htmlspecialchars(url('/jobs')) ?>" class="mt-6 inline-block rounded-2xl bg-gold-500 px-5 py-3 text-sm font-black text-navy-900 transition hover:bg-gold-400">استعراض الوظائف</a>
            </div>
        <?php else: ?>
            <div class="grid gap-5">
                <?php foreach ($applications as $application): ?>
                    <?php $status = $statusLabels[$application['status'] ?? 'pending'] ?? $statusLabels['pending']; ?>
                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="mb-3 inline-flex items-center rounded-full bg-gold-50 px-3 py-1 text-xs font-bold text-gold-700">
                                    <?= htmlspecialchars($application['job_type'] ?? 'full-time') ?>
                                </div>
                                <h3 class="text-2xl font-black text-navy-900"><?= htmlspecialchars($application['title'] ?? 'وظيفة') ?></h3>
                                <p class="mt-2 text-sm font-bold text-slate-600"><?= htmlspecialchars($application['company_name'] ?? 'شركة') ?></p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full px-3 py-1.5 text-xs font-bold <?= htmlspecialchars($status['class']) ?>"><?= htmlspecialchars($status['label']) ?></span>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-4 border-t border-slate-200 pt-4 md:flex-row md:items-center md:justify-between">
                            <div class="text-sm text-slate-500">
                                تاريخ التقديم: <?= htmlspecialchars(date('d/m/Y', strtotime($application['applied_at'] ?? date('Y-m-d')))) ?>
                            </div>
                            <div class="flex gap-3">
                                <a href="<?= htmlspecialchars(url('/jobs/' . ($application['job_id'] ?? 0))) ?>" class="rounded-xl bg-gold-500 px-4 py-2 text-sm font-bold text-navy-900 transition hover:bg-gold-400">عرض الوظيفة</a>
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

