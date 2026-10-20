<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-soft sm:p-10">
        <p class="mb-4 text-sm font-bold tracking-[0.2em] text-gold-600">الصفحة</p>
        <h1 class="text-3xl font-black text-navy-900"><?= htmlspecialchars($title ?? 'الصفحة') ?></h1>

        <?php if (!empty($page)): ?>
            <div class="mt-8 space-y-5 text-base leading-8 text-slate-600">
                <p><?= nl2br(htmlspecialchars($page['content'] ?? 'لا يوجد محتوى متاح حالياً.')) ?></p>
            </div>
        <?php else: ?>
            <p class="mt-8 text-slate-500">لا يوجد محتوى لعرضه.</p>
        <?php endif; ?>
    </div>
</section>
