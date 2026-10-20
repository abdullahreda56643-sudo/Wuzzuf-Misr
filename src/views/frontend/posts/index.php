<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
    <?php $postsTopAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
    <?php if ($postsTopAd !== ''): ?>
    <div class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
            <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
        </div>
        <div class="p-2 sm:p-3">
            <?= $postsTopAd ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="mb-8">
        <p class="text-sm font-bold tracking-[0.22em] text-gold-600">مقالات</p>
        <h1 class="mt-3 text-3xl font-black text-navy-900">نصائح مهنية</h1>
    </div>

    <?php if (!empty($posts)): ?>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($posts as $post): ?>
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                    <div class="mb-4 flex items-center justify-between text-xs text-slate-500">
                        <span><?= htmlspecialchars($post['category'] ?? 'عام') ?></span>
                        <span><?= htmlspecialchars(date('d/m/Y', strtotime($post['published_at'] ?? $post['created_at'] ?? 'now'))) ?></span>
                    </div>
                    <h2 class="text-xl font-extrabold text-navy-900">
                        <?= htmlspecialchars($post['title'] ?? 'عنوان المقال') ?>
                    </h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        <?= htmlspecialchars(mb_substr(strip_tags($post['content'] ?? ''), 0, 180, 'UTF-8')) ?>...
                    </p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            لا توجد مقالات منشورة حالياً.
        </div>
    <?php endif; ?>

    <?php $postsBottomAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
    <?php if ($postsBottomAd !== ''): ?>
    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
            <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
        </div>
        <div class="p-2 sm:p-3">
            <?= $postsBottomAd ?>
        </div>
    </div>
    <?php endif; ?>
</section>
