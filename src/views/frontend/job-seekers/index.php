<?php
$seekers = $seekers ?? [];
$governorates = $governorates ?? [];
$search = (string) ($search ?? '');
$governorateId = (int) ($governorateId ?? 0);
$currentUser = current_user();
$canRequest = $currentUser && ($currentUser['type'] ?? '') === 'employer';

$genderLabels = ['male' => 'ذكر', 'female' => 'أنثى', 'other' => 'آخر'];
?>

<section class="relative overflow-hidden bg-navy-900 py-16 text-white sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <p class="text-sm font-black tracking-[0.18em] text-gold-300">TALENT DIRECTORY</p>
        <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight sm:text-5xl">اكتشف الباحثين عن عمل المناسبين لفرصتك</h1>
        <p class="mt-4 max-w-2xl text-base font-bold leading-8 text-slate-200">تصفح الملفات المهنية دون عرض أرقام الهاتف. عندما تجد الشخص المناسب، أرسل طلباً للإدارة وسنتولى تزويدك ببيانات التواصل.</p>
    </div>
</section>

<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <form method="GET" action="<?= htmlspecialchars(url('/job-seekers')) ?>" class="mb-8 grid gap-3 rounded-[24px] border border-slate-200 bg-white p-4 shadow-soft md:grid-cols-[1fr_220px_auto]">
        <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث بالاسم أو الخبرة أو المهارة" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 font-bold text-slate-800 outline-none focus:border-gold-500">
        <select name="governorate" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 font-bold text-slate-800 outline-none focus:border-gold-500">
            <option value="0">كل المحافظات</option>
            <?php foreach ($governorates as $governorate): ?>
                <option value="<?= (int) $governorate['id'] ?>" <?= $governorateId === (int) $governorate['id'] ? 'selected' : '' ?>><?= htmlspecialchars($governorate['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="rounded-2xl bg-gold-500 px-5 py-3 font-black text-navy-900 transition hover:bg-gold-400" type="submit">بحث</button>
    </form>

    <?php if (!$canRequest): ?>
        <div class="mb-8 rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4 text-sm font-bold text-sky-800">زر طلب التوظيف متاح للحسابات المسجلة كصاحب عمل فقط.</div>
    <?php endif; ?>

    <?php if (empty($seekers)): ?>
        <div class="rounded-[28px] border border-slate-200 bg-white p-12 text-center shadow-soft"><i class="bx bx-user-x text-5xl text-slate-300"></i><h2 class="mt-4 text-2xl font-black text-navy-900">لا يوجد باحثون مطابقون</h2></div>
    <?php else: ?>
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($seekers as $seeker): ?>
                <?php
                    $name = trim(($seeker['first_name'] ?? '') . ' ' . ($seeker['last_name'] ?? ''));
                    $skills = json_decode((string) ($seeker['skills'] ?? '[]'), true);
                    $skills = is_array($skills) ? $skills : [];
                    $location = trim(($seeker['city_name'] ?? '') . (($seeker['city_name'] ?? '') && ($seeker['governorate_name'] ?? '') ? '، ' : '') . ($seeker['governorate_name'] ?? ''));
                    $profileData = [
                        'الاسم الكامل' => $name,
                        'البريد الإلكتروني' => (string) ($seeker['email'] ?? ''),
                        'المحافظة والمدينة' => $location,
                        'تاريخ الميلاد' => (string) ($seeker['date_of_birth'] ?? ''),
                        'النوع' => $genderLabels[$seeker['gender'] ?? ''] ?? (string) ($seeker['gender'] ?? ''),
                        'المؤهل العلمي' => (string) ($seeker['qualification'] ?? ''),
                        'الخبرة المهنية' => (string) ($seeker['experience'] ?? ''),
                        'المهارات' => implode('، ', array_map('strval', $skills)),
                        'العنوان' => (string) ($seeker['address'] ?? ''),
                        'النبذة' => (string) ($seeker['bio'] ?? ''),
                        'السيرة الذاتية' => (string) ($seeker['resume_title'] ?? ''),
                    ];
                    $profileJson = htmlspecialchars(json_encode($profileData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
                ?>
                <article class="flex flex-col rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft transition hover:-translate-y-1 hover:shadow-lift">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gold-100 text-xl font-black text-gold-700">
                            <?php if (!empty($seeker['avatar'])): ?><img src="<?= htmlspecialchars(asset($seeker['avatar'])) ?>" alt="" class="h-full w-full object-cover"><?php else: ?><?= htmlspecialchars(mb_substr($name, 0, 1, 'UTF-8')) ?><?php endif; ?>
                        </div>
                        <div class="min-w-0"><h2 class="truncate text-xl font-black text-navy-900"><?= htmlspecialchars($name ?: 'باحث عن عمل') ?></h2><p class="mt-1 text-sm font-bold text-slate-500"><?= htmlspecialchars($location ?: 'الموقع غير محدد') ?></p></div>
                    </div>
                    <?php if (!empty($seeker['qualification'])): ?><p class="mt-5 text-sm font-black text-slate-700"><i class="bx bx-certification ml-1 text-gold-600"></i><?= htmlspecialchars($seeker['qualification']) ?></p><?php endif; ?>
                    <?php if (!empty($seeker['experience'])): ?><p class="mt-2 line-clamp-3 text-sm font-bold leading-7 text-slate-600"><?= htmlspecialchars($seeker['experience']) ?></p><?php endif; ?>
                    <?php if (!empty($seeker['bio'])): ?><p class="mt-3 line-clamp-3 text-sm font-bold leading-7 text-slate-600"><?= htmlspecialchars($seeker['bio']) ?></p><?php endif; ?>
                    <?php if (!empty($skills)): ?><div class="mt-4 flex flex-wrap gap-2"><?php foreach (array_slice($skills, 0, 5) as $skill): ?><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600"><?= htmlspecialchars((string) $skill) ?></span><?php endforeach; ?></div><?php endif; ?>
                    <div class="mt-auto pt-5">
                        <button type="button" data-open-profile data-profile="<?= $profileJson ?>" class="mb-2 flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-black text-navy-900 transition hover:border-gold-500 hover:bg-gold-50"><i class="bx bx-show"></i>عرض الملف الكامل</button>
                        <?php if ($canRequest): ?>
                            <button type="button" data-open-request data-seeker-id="<?= (int) $seeker['id'] ?>" data-seeker-name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-navy-900 px-4 py-3 text-sm font-black text-white transition hover:bg-navy-800"><i class="bx bx-paper-plane"></i>طلب توظيف</button>
                        <?php else: ?><a href="<?= htmlspecialchars(url('/login')) ?>" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-navy-900 px-4 py-3 text-sm font-black text-navy-900"><i class="bx bx-lock-open"></i>سجل كصاحب عمل للتواصل</a><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<div id="seeker-profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-navy-900/60 p-4 backdrop-blur-sm" aria-hidden="true">
    <div class="max-h-[90vh] w-full max-w-3xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="seeker-profile-title">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
            <div><p class="text-xs font-black tracking-[0.18em] text-gold-600">FULL PROFILE</p><h2 id="seeker-profile-title" class="mt-1 text-2xl font-black text-navy-900">تفاصيل الباحث عن عمل</h2></div>
            <button type="button" data-close-profile class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-xl text-slate-700 hover:bg-slate-200" aria-label="إغلاق"><i class="bx bx-x"></i></button>
        </div>
        <div id="seeker-profile-content" class="max-h-[calc(90vh-85px)] overflow-y-auto p-5 sm:p-6"></div>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('seeker-profile-modal');
    const content = document.getElementById('seeker-profile-content');
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[character]);
    const renderProfile = (data) => Object.entries(data).filter(([, value]) => String(value ?? '').trim() !== '').map(([label, value]) => `<div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs font-black text-slate-500">${escapeHtml(label)}</dt><dd class="mt-1 whitespace-pre-line break-words text-sm font-bold leading-7 text-slate-800">${escapeHtml(value)}</dd></div>`).join('');
    const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); modal.setAttribute('aria-hidden', 'true'); };
    document.querySelectorAll('[data-open-profile]').forEach((button) => button.addEventListener('click', () => {
        content.innerHTML = `<dl class="grid gap-3 sm:grid-cols-2">${renderProfile(JSON.parse(button.dataset.profile))}</dl>`;
        modal.classList.remove('hidden'); modal.classList.add('flex'); modal.setAttribute('aria-hidden', 'false');
    }));
    modal.querySelector('[data-close-profile]').addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); });
})();
</script>

<?php if ($canRequest): ?>
<div id="request-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-navy-900/60 p-4 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-[28px] bg-white p-6 shadow-2xl">
        <div class="flex items-center justify-between"><h2 class="text-2xl font-black text-navy-900">طلب توظيف</h2><button type="button" data-close-request class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-xl"><i class="bx bx-x"></i></button></div>
        <p class="mt-3 text-sm font-bold text-slate-600">سيصل طلبك إلى الإدارة للتواصل مع <strong id="request-seeker-name" class="text-navy-900"></strong>.</p>
        <form method="POST" action="" id="request-form" class="mt-5"><textarea name="message" rows="5" maxlength="2000" placeholder="اكتب نبذة عن الفرصة أو سبب اهتمامك (اختياري)" class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm font-bold outline-none focus:border-gold-500"></textarea><button class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-gold-500 px-4 py-3 font-black text-navy-900 hover:bg-gold-400" type="submit"><i class="bx bx-send"></i>إرسال الطلب للإدارة</button></form>
    </div>
</div>
<script>
(() => { const modal = document.getElementById('request-modal'); const form = document.getElementById('request-form'); const name = document.getElementById('request-seeker-name'); const close = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); }; document.querySelectorAll('[data-open-request]').forEach((button) => button.addEventListener('click', () => { name.textContent = button.dataset.seekerName; form.action = '<?= htmlspecialchars(url('/job-seekers')) ?>/' + button.dataset.seekerId + '/request'; modal.classList.remove('hidden'); modal.classList.add('flex'); })); document.querySelector('[data-close-request]').addEventListener('click', close); modal.addEventListener('click', (event) => { if (event.target === modal) close(); }); })();
</script>
<?php endif; ?>
