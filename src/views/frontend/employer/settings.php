<?php
$user = $user ?? current_user();
$company = $company ?? null;

$companyName = trim((string) ($company['name'] ?? $user['company_name'] ?? (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))));
if ($companyName === '') {
    $companyName = 'شركتي';
}

$companyEmail = trim((string) ($company['email'] ?? $user['email'] ?? ''));
$companyPhone = trim((string) ($company['phone'] ?? $user['phone'] ?? ''));
$companyWebsite = trim((string) ($company['website'] ?? ''));
$companyIndustry = trim((string) ($company['industry'] ?? ''));
$companyDescription = trim((string) ($company['description'] ?? ''));
$companyLogo = trim((string) ($company['logo'] ?? ''));
$governorates = $governorates ?? [];
$cities = $cities ?? [];
$companyGovernorateId = (int) ($company['governorate_id'] ?? 0);
$companyCityId = (int) ($company['city_id'] ?? 0);
?>
<div class="min-h-screen bg-slate-100">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-[30px] bg-gradient-to-r from-[#081d2f] via-[#0e2d46] to-[#163d5a] p-6 text-white shadow-[0_22px_50px_rgba(8,29,47,0.22)]">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-gold-300">إعدادات الحساب</p>
                    <h1 class="mt-2 text-3xl font-black">إعدادات الشركة</h1>
                </div>
                <a href="<?= htmlspecialchars(url('/employer/dashboard')) ?>" class="rounded-2xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/10">العودة إلى اللوحة</a>
            </div>
        </div>

        <form method="POST" action="<?= htmlspecialchars(url('/employer/settings')) ?>" enctype="multipart/form-data" class="space-y-6 rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">اسم الشركة</label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars($companyName) ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" required>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">البريد الإلكتروني</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($companyEmail) ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" required>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">رقم الهاتف</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($companyPhone) ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" required>
                </div>

                <div>
                    <label for="governorate_id" class="mb-2 block text-sm font-bold text-slate-700">المحافظة</label>
                    <select id="governorate_id" name="governorate_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100">
                        <option value="">اختر المحافظة</option>
                        <?php foreach ($governorates as $governorate): ?>
                            <option value="<?= (int) ($governorate['id'] ?? 0) ?>" <?= $companyGovernorateId === (int) ($governorate['id'] ?? 0) ? 'selected' : '' ?>><?= htmlspecialchars((string) ($governorate['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="city_id" class="mb-2 block text-sm font-bold text-slate-700">المدينة</label>
                    <select id="city_id" name="city_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100">
                        <option value="">اختر المدينة</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= (int) ($city['id'] ?? 0) ?>" data-governorate-id="<?= (int) ($city['governorate_id'] ?? 0) ?>" <?= $companyCityId === (int) ($city['id'] ?? 0) ? 'selected' : '' ?>><?= htmlspecialchars((string) ($city['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">الموقع الإلكتروني</label>
                    <input type="url" name="website" value="<?= htmlspecialchars($companyWebsite) ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">نبذة عن الشركة</label>
                    <textarea name="description" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100"><?= htmlspecialchars($companyDescription) ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">النشاط التجاري</label>
                    <input type="text" name="industry" value="<?= htmlspecialchars($companyIndustry) ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100">
                </div>
            </div>

            <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-800">شعار الشركة</p>
                        <p class="mt-1 text-sm text-slate-500">يفضل أن يكون بحجم مناسب للعرض في صفحات الوظائف</p>
                    </div>
                    <label class="cursor-pointer rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                        رفع صورة
                        <input type="file" name="logo" accept="image/*" class="hidden">
                    </label>
                </div>

                <?php if ($companyLogo !== ''): ?>
                    <?php $companyLogoUrl = asset((string) $companyLogo); ?>
                    <div class="mt-4 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3">
                        <img src="<?= htmlspecialchars($companyLogoUrl) ?>" alt="شعار الشركة" class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200">
                        <span class="text-sm font-bold text-slate-700">الشعار الحالي</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="<?= htmlspecialchars(url('/employer/dashboard')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-[#0d2236] to-[#173d5d] px-6 py-3 text-sm font-black text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5">حفظ الإعدادات</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const governorate = document.getElementById('governorate_id');
        const city = document.getElementById('city_id');
        if (!governorate || !city) return;

        const filterCities = () => {
            const governorateId = governorate.value;
            Array.from(city.options).forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }
                option.hidden = governorateId !== '' && option.dataset.governorateId !== governorateId;
            });
            if (city.selectedOptions[0]?.hidden) city.value = '';
        };

        governorate.addEventListener('change', filterCities);
        filterCities();
    });
</script>

