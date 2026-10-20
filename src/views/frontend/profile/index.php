<?php
$user = $user ?? current_user();
$skills = is_array($skills ?? null) ? $skills : [];
$skillsText = implode(', ', $skills);
$resumes = $resumes ?? [];
$defaultResume = $defaultResume ?? null;
$governorates = $governorates ?? [];
$cities = $cities ?? [];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'الملف الشخصي') ?></title>
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
        .input-shell { border: 1px solid #e2e8f0; border-radius: 1rem; background: #fff; transition: all .2s ease; }
        .input-shell:focus-within { border-color: #c79734; box-shadow: 0 0 0 4px rgba(199, 151, 52, 0.08); }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-[28px] bg-gradient-to-r from-navy-900 to-navy-800 p-6 text-white shadow-[0_18px_45px_rgba(7,27,47,0.18)]">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm text-gold-500">الباحث عن العمل</p>
                    <h1 class="mt-1 text-3xl font-black">الملف الشخصي</h1>
                </div>
                <a href="<?= htmlspecialchars(url('/dashboard')) ?>" class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-bold transition hover:bg-white/10">العودة إلى لوحة التحكم</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="mb-6 rounded-2xl border px-4 py-3 text-sm font-medium <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars(url('/profile')) ?>" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
            <div class="space-y-6">
                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black text-navy-900">البيانات الشخصية</h2>
                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">الاسم الأول</label>
                            <div class="input-shell px-3 py-3">
                                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" class="w-full bg-transparent text-sm text-slate-700 outline-none" placeholder="الاسم الأول" required>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">اسم العائلة</label>
                            <div class="input-shell px-3 py-3">
                                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" class="w-full bg-transparent text-sm text-slate-700 outline-none" placeholder="اسم العائلة" required>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-bold text-slate-700">رقم الهاتف</label>
                            <div class="input-shell px-3 py-3">
                                <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="w-full bg-transparent text-sm text-slate-700 outline-none" placeholder="05xxxxxxxx">
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">المحافظة</label>
                            <div class="input-shell px-3 py-3">
                                <select id="profile-governorate" name="governorate_id" class="w-full bg-transparent text-sm text-slate-700 outline-none">
                                    <option value="0">اختر المحافظة</option>
                                    <?php foreach ($governorates as $governorate): ?>
                                        <option value="<?= (int) $governorate['id'] ?>" <?= (int) ($user['governorate_id'] ?? 0) === (int) $governorate['id'] ? 'selected' : '' ?>><?= htmlspecialchars($governorate['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">المدينة</label>
                            <div class="input-shell px-3 py-3">
                                <select id="profile-city" name="city_id" class="w-full bg-transparent text-sm text-slate-700 outline-none">
                                    <option value="0">اختر المدينة</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= (int) $city['id'] ?>" data-governorate="<?= (int) $city['governorate_id'] ?>" <?= (int) ($user['city_id'] ?? 0) === (int) $city['id'] ? 'selected' : '' ?>><?= htmlspecialchars($city['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-bold text-slate-700">نبذة قصيرة</label>
                            <div class="input-shell px-3 py-3">
                                <textarea name="bio" rows="4" class="w-full resize-none bg-transparent text-sm text-slate-700 outline-none" placeholder="اكتب نبذة عنك"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black text-navy-900">المؤهل والخبرة</h2>
                    <div class="mt-5 space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">المؤهل العلمي</label>
                            <div class="input-shell px-3 py-3">
                                <input type="text" name="qualification" value="<?= htmlspecialchars($user['qualification'] ?? '') ?>" class="w-full bg-transparent text-sm text-slate-700 outline-none" placeholder="مثال: بكالوريوس نظم معلومات">
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">الخبرة المهنية</label>
                            <div class="input-shell px-3 py-3">
                                <textarea name="experience" rows="4" class="w-full resize-none bg-transparent text-sm text-slate-700 outline-none" placeholder="اكتب خبرتك السابقة أو الأنشطة المهنية"><?= htmlspecialchars($user['experience'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black text-navy-900">المهارات</h2>
                    <div class="mt-5">
                        <label class="mb-2 block text-sm font-bold text-slate-700">مهاراتك</label>
                        <div class="input-shell px-3 py-3">
                            <textarea name="skills" rows="3" class="w-full resize-none bg-transparent text-sm text-slate-700 outline-none" placeholder="مثال: PHP, JavaScript, UI/UX, إدارة المشاريع"><?= htmlspecialchars($skillsText) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-navy-900">رفع CV</h2>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center">
                            <input type="file" name="cv" accept=".pdf,.doc,.docx" class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-gold-500 file:px-4 file:py-2 file:text-sm file:font-bold file:text-navy-900">
                        </div>

                        <?php if ($defaultResume): ?>
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                                <p class="text-sm font-bold text-emerald-700">تم رفع ملف CV</p>
                                <a href="<?= htmlspecialchars(url('/cv/preview/' . (int) ($defaultResume['id'] ?? 0))) ?>" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-sm font-bold text-emerald-700 underline">
                                    عرض الملف الحالي
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($resumes)): ?>
                            <div class="space-y-2">
                                <p class="text-sm font-bold text-slate-700">الملفات المرفوعة</p>
                                <?php foreach ($resumes as $resume): ?>
                                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                        <span class="text-sm text-slate-700"><?= htmlspecialchars($resume['file_name'] ?? 'CV') ?></span>
                                        <a href="<?= htmlspecialchars(url('/cv/preview/' . (int) ($resume['id'] ?? 0))) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-gold-700 underline">عرض</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-gold-500 to-gold-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-gold-500/30 transition hover:from-gold-600 hover:to-gold-700">
                        حفظ البيانات
                    </button>
                </div>
            </div>
        </form>
    </div>
<script>
(() => {
    const governorate = document.getElementById('profile-governorate');
    const city = document.getElementById('profile-city');
    if (!governorate || !city) return;
    const filterCities = () => {
        const selectedGovernorate = governorate.value;
        Array.from(city.options).forEach((option, index) => {
            if (index === 0) return;
            option.hidden = option.dataset.governorate !== selectedGovernorate;
        });
        if (city.selectedOptions[0]?.hidden) city.value = '0';
    };
    governorate.addEventListener('change', filterCities);
    filterCities();
})();
</script>
</body>
</html>
