<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$users = $users ?? [];
$search = trim((string) ($search ?? ''));
$page = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$totalUsers = (int) ($totalUsers ?? 0);

$profileFields = [
    'الاسم الكامل' => 'full_name',
    'البريد الإلكتروني' => 'email',
    'الهاتف' => 'phone',
    'الصورة الشخصية' => 'avatar',
    'النوع' => 'type_label',
    'الحالة' => 'status_label',
    'حالة التحقق' => 'verification_label',
    'الجنس' => 'gender',
    'تاريخ الميلاد' => 'date_of_birth',
    'المؤهل' => 'qualification',
    'الخبرة' => 'experience',
    'المحافظة' => 'governorate_name',
    'المدينة' => 'city_name',
    'العنوان' => 'address',
    'نبذة' => 'bio',
    'تاريخ التسجيل' => 'created_at',
    'آخر دخول' => 'last_login',
    'آخر تحديث' => 'updated_at',
];
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">MANAGE USERS</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إدارة المستخدمين</h2>
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft sm:p-5">
        <form method="GET" action="<?= htmlspecialchars(url('/admin/users')) ?>" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex w-full max-w-md items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                <i class="bx bx-search text-lg text-slate-400"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث عن اسم المستخدم أو البريد..." class="w-full bg-transparent text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:outline-none" />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="gold-btn rounded-2xl px-4 py-2.5 text-sm font-black text-navy-900">بحث</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= htmlspecialchars(url('/admin/users')) ?>" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-black text-slate-700">إعادة تعيين</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="mb-4 text-sm font-bold text-slate-600">
            إجمالي النتائج: <span class="text-slate-900"><?= (int) $totalUsers ?></span>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table min-w-full text-right text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-bold">اسم المستخدم</th>
                        <th class="px-4 py-3 font-bold">البريد الإلكتروني</th>
                        <th class="px-4 py-3 font-bold">نوع الحساب</th>
                        <th class="px-4 py-3 font-bold">الحالة</th>
                        <th class="px-4 py-3 font-bold">تاريخ الإنشاء</th>
                        <th class="px-4 py-3 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <?php
                                $userType = (string) ($user['type'] ?? 'job_seeker');
                                $user['type_label'] = $userType === 'employer' ? 'صاحب عمل' : ($userType === 'admin' ? 'إدارة' : 'باحث عن عمل');
                                $user['status_label'] = ($user['status'] ?? 'active') === 'active' ? 'نشط' : 'غير نشط';
                                $user['verification_label'] = !empty($user['is_verified']) ? 'موثق' : 'غير موثق';
                                $user['full_name'] = trim((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
                                $profileData = [];
                                foreach ($profileFields as $label => $key) {
                                    $profileData[$label] = (string) ($user[$key] ?? '');
                                }
                                $profileData['المهارات'] = $user['skills'] ?? '';
                                if (is_string($profileData['المهارات'])) {
                                    $decodedSkills = json_decode($profileData['المهارات'], true);
                                    $profileData['المهارات'] = is_array($decodedSkills) ? implode('، ', $decodedSkills) : $profileData['المهارات'];
                                }
                                if ($userType === 'employer') {
                                    $profileData['بيانات الشركة'] = [
                                        'اسم الشركة' => $user['company_name'] ?? '', 'البريد' => $user['company_email'] ?? '',
                                        'الهاتف' => $user['company_phone'] ?? '', 'الموقع' => $user['company_website'] ?? '',
                                        'النشاط' => $user['company_industry'] ?? '', 'حجم الشركة' => $user['company_size'] ?? '',
                                        'سنة التأسيس' => $user['founded_year'] ?? '', 'الوصف' => $user['company_description'] ?? '',
                                        'المحافظة' => $user['company_governorate_name'] ?? '', 'المدينة' => $user['company_city_name'] ?? '',
                                        'العنوان' => $user['company_address'] ?? '', 'حالة التوثيق' => $user['company_verification_status'] ?? '',
                                        'الشعار' => $user['company_logo'] ?? '', 'صورة الغلاف' => $user['company_cover_image'] ?? '',
                                    ];
                                } else {
                                    $resumePath = trim((string) ($user['resume_file_path'] ?? ''));
                                    $profileData['السيرة الذاتية'] = [
                                        'اسم الملف' => $user['resume_file_name'] ?? '',
                                        '__url' => $resumePath !== '' ? asset($resumePath) : '',
                                    ];
                                }
                                $profileJson = htmlspecialchars(json_encode($profileData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr class="border-t border-slate-200 bg-white">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold-100 text-sm font-black text-gold-700">
                                            <?= htmlspecialchars(strtoupper(mb_substr((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')), 0, 1, 'UTF-8'))) ?>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800"><?= htmlspecialchars(trim((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'بدون اسم')) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars((string) ($user['email'] ?? '')) ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= (($user['type'] ?? 'job_seeker') === 'admin') ? 'bg-purple-100 text-purple-700' : (($user['type'] ?? 'job_seeker') === 'employer' ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-700') ?>">
                                        <?= (($user['type'] ?? 'job_seeker') === 'admin') ? 'إدارة' : (($user['type'] ?? 'job_seeker') === 'employer' ? 'صاحب عمل' : 'باحث عن عمل') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black <?= (($user['status'] ?? 'active') === 'active') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= (($user['status'] ?? 'active') === 'active') ? 'نشط' : 'غير نشط' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars(date('Y-m-d', strtotime((string) ($user['created_at'] ?? 'now')))) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-base text-emerald-700 transition hover:bg-emerald-100" data-user-profile="<?= $profileJson ?>" aria-label="عرض الملف الشخصي" title="عرض الملف الشخصي">
                                            <i class="bx bx-show"></i>
                                        </button>
                                        <a href="<?= htmlspecialchars(url('/admin/users/' . (int) ($user['id'] ?? 0) . '/edit')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-black text-sky-700 transition hover:bg-sky-100">
                                            <i class="bx bx-edit-alt"></i>
                                            تعديل
                                        </a>
                                        <a href="<?= htmlspecialchars(url('/admin/users/' . (int) ($user['id'] ?? 0) . '/delete')) ?>" class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-black text-red-700 transition hover:bg-red-100" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                            <i class="bx bx-trash"></i>
                                            حذف
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">لا يوجد مستخدمون مطابقون للبحث.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

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
            <div class="mt-6 flex justify-center">
                <nav class="inline-flex items-center gap-1 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm">
                    <?php if ($page > 1): ?>
                        <a href="<?= htmlspecialchars(url('/admin/users') . '?q=' . urlencode($search) . '&page=' . ($page - 1)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="السابق">
                            <i class="bx bx-chevron-right text-xl"></i>
                        </a>
                    <?php else: ?>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300">
                            <i class="bx bx-chevron-right text-xl"></i>
                        </span>
                    <?php endif; ?>

                    <?php if ($startPage > 1): ?>
                        <a href="<?= htmlspecialchars(url('/admin/users') . '?q=' . urlencode($search) . '&page=1') ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-100">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="flex h-9 w-9 items-center justify-center text-slate-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="<?= htmlspecialchars(url('/admin/users') . '?q=' . urlencode($search) . '&page=' . $i) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black <?= $i === $page ? 'bg-gold-500 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="flex h-9 w-9 items-center justify-center text-slate-400">...</span>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars(url('/admin/users') . '?q=' . urlencode($search) . '&page=' . $totalPages) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-xs sm:text-sm font-black text-slate-600 hover:bg-slate-100"><?= $totalPages ?></a>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= htmlspecialchars(url('/admin/users') . '?q=' . urlencode($search) . '&page=' . ($page + 1)) ?>" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100" title="التالي">
                            <i class="bx bx-chevron-left text-xl"></i>
                        </a>
                    <?php else: ?>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-300">
                            <i class="bx bx-chevron-left text-xl"></i>
                        </span>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</section>

<div id="user-profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-navy-900/60 p-4 backdrop-blur-sm" aria-hidden="true">
    <div class="max-h-[90vh] w-full max-w-3xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="user-profile-title">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
            <div>
                <p class="text-xs font-bold text-gold-600">USER PROFILE</p>
                <h3 id="user-profile-title" class="mt-1 text-xl font-black text-navy-900">الملف الشخصي</h3>
            </div>
            <button type="button" data-close-profile class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-xl text-slate-600 hover:bg-slate-200" aria-label="إغلاق"><i class="bx bx-x"></i></button>
        </div>
        <div id="user-profile-content" class="max-h-[calc(90vh-80px)] overflow-y-auto p-5 sm:p-6"></div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('user-profile-modal');
        const content = document.getElementById('user-profile-content');
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[character]);
        const renderProfile = (data) => Object.entries(data).map(([label, value]) => {
            if (typeof value === 'object' && value !== null) {
                if (value.__url) {
                    return `<div class="col-span-full rounded-2xl border border-emerald-200 bg-emerald-50 p-4"><h4 class="font-black text-navy-900">${escapeHtml(label)}</h4><p class="mt-1 break-all text-sm font-bold text-slate-700">${escapeHtml(value['اسم الملف'] || 'ملف السيرة الذاتية')}</p><div class="mt-3 flex flex-wrap gap-2"><a href="${escapeHtml(value.__url)}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-black text-white transition hover:bg-emerald-700"><i class="bx bx-show"></i>معاينة</a><a href="${escapeHtml(value.__url)}" download="${escapeHtml(value['اسم الملف'] || 'resume')}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-white px-4 py-2 text-sm font-black text-emerald-700 transition hover:bg-emerald-100"><i class="bx bx-download"></i>تنزيل</a></div></div>`;
                }
                const nestedProfile = renderProfile(value);
                return nestedProfile ? `<div class="col-span-full mt-3 border-t border-slate-200 pt-4"><h4 class="mb-3 font-black text-navy-900">${escapeHtml(label)}</h4><div class="grid gap-3 sm:grid-cols-2">${nestedProfile}</div></div>` : '';
            }
            return value !== null && String(value).trim() !== '' ? `<div class="rounded-2xl bg-slate-50 p-3"><dt class="text-xs font-bold text-slate-500">${escapeHtml(label)}</dt><dd class="mt-1 whitespace-pre-line break-words text-sm font-bold text-slate-800">${escapeHtml(value)}</dd></div>` : '';
        }).join('');
        document.querySelectorAll('[data-user-profile]').forEach((button) => button.addEventListener('click', () => {
            content.innerHTML = `<dl class="grid gap-3 sm:grid-cols-2">${renderProfile(JSON.parse(button.dataset.userProfile))}</dl>`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
        }));
        const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); modal.setAttribute('aria-hidden', 'true'); };
        modal.querySelector('[data-close-profile]').addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); });
    })();
</script>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>

