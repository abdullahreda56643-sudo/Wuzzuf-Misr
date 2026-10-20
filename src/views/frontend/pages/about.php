<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <?php $aboutTopAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
    <?php if ($aboutTopAd !== ''): ?>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
            <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
        </div>
        <div class="p-2 sm:p-3">
            <?= $aboutTopAd ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-soft sm:p-10">
        <p class="mb-4 text-sm font-bold tracking-[0.2em] text-gold-600">من نحن</p>
        <h1 class="text-3xl font-black text-navy-900">نبني فرصًا أفضل للباحثين عن العمل</h1>
        <div class="mt-6 space-y-5 text-base leading-8 text-slate-600">
            <p>هذه المنصة تهدف إلى ربط الباحثين عن عمل بالشركات المناسبة عبر تجربة استخدام سهلة ومهنية.</p>
            <p>نحرص على تقديم أدوات عملية، وظائف موثوقة، ونصائح تساعد المستخدمين على تطوير مسارهم المهني بثقة.</p>
            <p>نؤمن بأن فرص العمل تبدأ من التوافق الصحيح بين المهارة والفرصة، لذلك نعمل على تحسين تجربة البحث والتقديم.</p>
        </div>
    </div>

    <?php $aboutBottomAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
    <?php if ($aboutBottomAd !== ''): ?>
    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
            <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
        </div>
        <div class="p-2 sm:p-3">
            <?= $aboutBottomAd ?>
        </div>
    </div>
    <?php endif; ?>
</section>
