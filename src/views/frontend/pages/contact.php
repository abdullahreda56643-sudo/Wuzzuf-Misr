<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <?php $contactTopAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
    <?php if ($contactTopAd !== ''): ?>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
            <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
        </div>
        <div class="p-2 sm:p-3">
            <?= $contactTopAd ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="mt-8 grid gap-8 lg:grid-cols-[1.1fr_1.4fr]">
        <div class="rounded-3xl bg-navy-900 p-8 text-white shadow-lift">
            <p class="text-sm font-bold tracking-[0.2em] text-gold-400">تواصل معنا</p>
            <h1 class="mt-4 text-3xl font-black">نحن هنا لمساعدتك</h1>
            <p class="mt-5 text-sm leading-7 text-slate-300">
                إذا كان لديك سؤال، اقتراح، أو ترغب في التعاون مع المنصة، يمكنك إرسال رسالة مباشرة وسنقوم بالرد في أقرب وقت.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-soft">
            <form method="POST" action="/contact" class="space-y-5">
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-800">الاسم</label>
                    <input type="text" name="name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-right text-slate-700 focus:border-gold-500 focus:outline-none" placeholder="أدخل اسمك" required>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-800">البريد الإلكتروني</label>
                    <input type="email" name="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-right text-slate-700 focus:border-gold-500 focus:outline-none" placeholder="name@example.com" required>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-800">الرسالة</label>
                    <textarea name="message" rows="5" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-right text-slate-700 focus:border-gold-500 focus:outline-none" placeholder="اكتب رسالتك هنا..." required></textarea>
                </div>

                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gold-500 px-6 py-3 text-sm font-extrabold text-navy-900 transition hover:bg-gold-400">
                    إرسال الرسالة
                </button>
            </form>
        </div>
    </div>

    <?php $contactBottomAd = renderAdMarkup($homeAds['middle-jobs'] ?? null, '', 'middle-jobs'); ?>
    <?php if ($contactBottomAd !== ''): ?>
    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
            <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400">AD</span>
        </div>
        <div class="p-2 sm:p-3">
            <?= $contactBottomAd ?>
        </div>
    </div>
    <?php endif; ?>
</section>
