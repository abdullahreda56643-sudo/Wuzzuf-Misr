<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$governorates = $governorates ?? [];
$cities = $cities ?? [];
$categories = $categories ?? [];
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">NEW EMPLOYER ACCOUNT</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إنشاء حساب صاحب عمل</h2>
            </div>
            <a href="<?= htmlspecialchars(url('/admin/companies')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700">
                <i class="bx bx-arrow-back"></i> العودة للشركات
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <form method="post" action="<?= htmlspecialchars(url('/admin/companies/create')) ?>" enctype="multipart/form-data" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2 lg:col-span-2">
                    <label for="company_name" class="block text-sm font-black text-slate-700">اسم الشركة</label>
                    <input id="company_name" name="company_name" required type="text" placeholder="اسم الشركة بالكامل" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                </div>
                <div class="space-y-2">
                    <label for="manager_name" class="block text-sm font-black text-slate-700">اسم المسؤول</label>
                    <input id="manager_name" name="manager_name" required type="text" placeholder="اسم المسؤول أو مدير التوظيف" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                </div>
                <div class="space-y-2">
                    <label for="industry" class="block text-sm font-black text-slate-700">النشاط التجاري</label>
                    <select id="industry" name="industry" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                        <option value="">اختر النشاط</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars((string) ($category['name'] ?? '')) ?>"><?= htmlspecialchars((string) ($category['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-black text-slate-700">البريد الإلكتروني</label>
                    <input id="email" name="email" required type="email" placeholder="company@example.com" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                </div>
                <div class="space-y-2">
                    <label for="phone" class="block text-sm font-black text-slate-700">رقم الهاتف</label>
                    <input id="phone" name="phone" required type="tel" placeholder="010 1234 5678" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                </div>
                <div class="space-y-2">
                    <label for="website" class="block text-sm font-black text-slate-700">الموقع الإلكتروني <span class="font-normal text-slate-400">(اختياري)</span></label>
                    <input id="website" name="website" type="url" placeholder="https://example.com" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                </div>
                <div class="space-y-2">
                    <label for="logo" class="block text-sm font-black text-slate-700">شعار الشركة</label>
                    <input id="logo" name="logo" type="file" accept="image/*" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                </div>
                <div class="space-y-2">
                    <label for="governorate_id" class="block text-sm font-black text-slate-700">المحافظة</label>
                    <select id="governorate_id" name="governorate_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                        <option value="">اختر المحافظة</option>
                        <?php foreach ($governorates as $governorate): ?>
                            <option value="<?= (int) ($governorate['id'] ?? 0) ?>"><?= htmlspecialchars((string) ($governorate['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label for="city_id" class="block text-sm font-black text-slate-700">المدينة</label>
                    <select id="city_id" name="city_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                        <option value="">اختر المدينة</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= (int) ($city['id'] ?? 0) ?>" data-governorate-id="<?= (int) ($city['governorate_id'] ?? 0) ?>"><?= htmlspecialchars((string) ($city['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2 lg:col-span-2">
                    <label for="description" class="block text-sm font-black text-slate-700">وصف الشركة</label>
                    <textarea id="description" name="description" rows="5" placeholder="اكتب نبذة قصيرة عن الشركة ونشاطها..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800"></textarea>
                </div>
                <div class="space-y-2 lg:col-span-2">
                    <label for="password" class="block text-sm font-black text-slate-700">كلمة المرور</label>
                    <input id="password" name="password" required type="password" placeholder="كلمة مرور حساب صاحب العمل" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">
                </div>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">سيتم إنشاء الحساب والشركة بحالة قيد التوثيق، ولن يستطيع صاحب العمل إضافة وظائف حتى اعتماد الشركة.</div>
            <div class="flex justify-end gap-3">
                <a href="<?= htmlspecialchars(url('/admin/companies')) ?>" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-black text-slate-700">إلغاء</a>
                <button type="submit" class="gold-btn rounded-2xl px-5 py-3 text-sm font-black text-navy-900">إنشاء حساب صاحب العمل</button>
            </div>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const governorate = document.getElementById('governorate_id');
        const city = document.getElementById('city_id');

        if (!governorate || !city) return;

        const filterCities = () => {
            const governorateId = governorate.value;
            let selectedCityIsVisible = false;

            Array.from(city.options).forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const isVisible = governorateId !== '' && option.dataset.governorateId === governorateId;
                option.hidden = !isVisible;
                if (isVisible && option.selected) {
                    selectedCityIsVisible = true;
                }
            });

            if (!selectedCityIsVisible) {
                city.value = '';
            }
            city.disabled = governorateId === '';
        };

        governorate.addEventListener('change', filterCities);
        filterCities();
    });
</script>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
