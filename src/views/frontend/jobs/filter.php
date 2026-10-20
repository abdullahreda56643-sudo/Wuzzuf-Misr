<?php
$jobs = $jobs ?? [];
$keyword = trim((string) ($keyword ?? ''));
?>
<div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-black text-slate-900">نتائج البحث</h2>
        <span class="rounded-full bg-gold-100 px-3 py-1 text-xs font-bold text-gold-700">
            <?= empty($jobs) ? '0' : count($jobs) ?> نتيجة
        </span>
    </div>

    <?php if ($keyword !== ''): ?>
        <p class="mb-4 text-sm text-slate-500">نتائج البحث عن: <span class="font-bold text-slate-800"><?= htmlspecialchars($keyword) ?></span></p>
    <?php endif; ?>

    <?php if (empty($jobs)): ?>
        <div class="rounded-[22px] border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gold-100 text-2xl text-gold-700">🔎</div>
            <p class="mt-4 text-lg font-black text-slate-900">لا توجد نتائج</p>
            <p class="mt-2 text-sm text-slate-500">حاول تعديل الفلاتر أو البحث بكلمات مختلفة.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($jobs as $job): ?>
                <?php $jobId = (int) ($job['id'] ?? 0); ?>
                <a href="<?= htmlspecialchars(url('/jobs/' . $jobId)) ?>" class="block rounded-[22px] border border-slate-200 bg-slate-50 p-4 transition hover:border-gold-200 hover:bg-white">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-lg font-black text-slate-900"><?= htmlspecialchars((string) ($job['title'] ?? 'وظيفة')) ?></p>
                            <p class="mt-1 text-sm text-slate-600"><?= htmlspecialchars((string) ($job['company'] ?? 'شركة')) ?></p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">متاح</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
