<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$governorates = $governorates ?? [];
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">NEW LOCATION</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إضافة موقع جديد</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/locations')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <form method="post" action="<?= htmlspecialchars(url('/admin/locations/create')) ?>" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="type" class="block text-sm font-black text-slate-700">نوع الموقع</label>
                    <select id="type" name="type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="governorate">محافظة</option>
                        <option value="city">مدينة</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="name" class="block text-sm font-black text-slate-700">اسم الموقع</label>
                    <input id="name" name="name" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="مثل: القاهرة أو الرياض">
                </div>
            </div>

            <div class="space-y-2" id="governorate-wrapper">
                <label for="governorate_id" class="block text-sm font-black text-slate-700">اختر المحافظة</label>
                <select id="governorate_id" name="governorate_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                    <option value="">اختر المحافظة</option>
                    <?php foreach ($governorates as $governorate): ?>
                        <option value="<?= (int) ($governorate['id'] ?? 0) ?>"><?= htmlspecialchars((string) ($governorate['name'] ?? '')) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?= htmlspecialchars(url('/admin/locations')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="gold-btn rounded-2xl px-5 py-3 text-sm font-black text-navy-900">حفظ الموقع</button>
            </div>
        </form>
    </div>
</section>

<script>
    const typeSelector = document.getElementById('type');
    const governorateWrapper = document.getElementById('governorate-wrapper');

    function toggleGovernorateField() {
        const isCity = typeSelector.value === 'city';
        governorateWrapper.style.display = isCity ? 'block' : 'none';
        if (isCity) {
            document.getElementById('governorate_id').setAttribute('required', 'required');
        } else {
            document.getElementById('governorate_id').removeAttribute('required');
        }
    }

    typeSelector.addEventListener('change', toggleGovernorateField);
    toggleGovernorateField();
</script>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
