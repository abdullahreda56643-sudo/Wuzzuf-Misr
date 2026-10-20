<?php
require_once __DIR__ . '/../../layout/admin/header.php';
require_once __DIR__ . '/../../layout/admin/sidebar.php';

$companies = $companies ?? [];
$categories = $categories ?? [];
$governorates = $governorates ?? [];
$cities = $cities ?? [];
?>

<section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-slate-400">NEW JOB</p>
                <h2 class="mt-1 text-2xl font-black text-navy-900">إضافة وظيفة جديدة</h2>
            </div>

            <a href="<?= htmlspecialchars(url('/admin/jobs')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                <i class="bx bx-arrow-back"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-soft sm:p-6">
        <form method="post" action="<?= htmlspecialchars(url('/admin/jobs/create')) ?>" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="company_id" class="block text-sm font-black text-slate-700">الشركة</label>
                    <select id="company_id" name="company_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="">اختر الشركة</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= (int) ($company['id'] ?? 0) ?>"><?= htmlspecialchars((string) ($company['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="status" class="block text-sm font-black text-slate-700">الحالة</label>
                    <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="draft">مسودة</option>
                        <option value="published">منشور</option>
                        <option value="closed">مغلق</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="category_id" class="block text-sm font-black text-slate-700">التصنيف</label>
                    <select id="category_id" name="category_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="">اختر التصنيف</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) ($category['id'] ?? 0) ?>"><?= htmlspecialchars((string) ($category['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="governorate_id" class="block text-sm font-black text-slate-700">المحافظة</label>
                    <select id="governorate_id" name="governorate_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="">اختر المحافظة</option>
                        <?php foreach ($governorates as $governorate): ?>
                            <option value="<?= (int) ($governorate['id'] ?? 0) ?>"><?= htmlspecialchars((string) ($governorate['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="city_id" class="block text-sm font-black text-slate-700">المدينة</label>
                    <select id="city_id" name="city_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                        <option value="">اختر المدينة</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= (int) ($city['id'] ?? 0) ?>" data-governorate-id="<?= (int) ($city['governorate_id'] ?? 0) ?>"><?= htmlspecialchars((string) ($city['name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label for="title" class="block text-sm font-black text-slate-700">عنوان الوظيفة</label>
                <input id="title" name="title" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="أدخل عنوان الوظيفة">
            </div>
                <div class="space-y-2 lg:col-span-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <label for="application_method" class="block text-sm font-black text-slate-700">طريقة التقديم</label>
                        <select id="application_method" name="application_method" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white">
                            <option value="internal">داخل الموقع</option>
                            <option value="whatsapp">واتساب</option>
                            <option value="phone">هاتف</option>
                            <option value="email">بريد إلكتروني</option>
                            <option value="external_link">رابط خارجي</option>
                        </select>

                        <div id="detail-whatsapp" class="mt-4 hidden">
                            <label class="mb-2 block text-sm font-bold text-slate-700">رقم الواتساب</label>
                            <div class="flex items-stretch overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                <span class="flex items-center border-l border-slate-200 bg-slate-50 px-4 text-sm font-black text-slate-600" dir="ltr">+20</span>
                                <input type="tel" name="application_whatsapp" inputmode="numeric" class="min-w-0 flex-1 bg-transparent px-4 py-3 text-sm text-slate-800 focus:outline-none" placeholder="10xxxxxxxx" dir="ltr">
                            </div>
                        </div>

                        <div id="detail-phone" class="mt-4 hidden">
                            <label class="mb-2 block text-sm font-bold text-slate-700">رقم الهاتف</label>
                            <div class="flex items-stretch overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                <span class="flex items-center border-l border-slate-200 bg-slate-50 px-4 text-sm font-black text-slate-600" dir="ltr">+20</span>
                                <input type="tel" name="application_phone" inputmode="numeric" class="min-w-0 flex-1 bg-transparent px-4 py-3 text-sm text-slate-800 focus:outline-none" placeholder="10xxxxxxxx" dir="ltr">
                            </div>
                        </div>

                        <div id="detail-email" class="mt-4 hidden">
                            <label class="mb-2 block text-sm font-bold text-slate-700">البريد الإلكتروني</label>
                            <input type="email" name="application_email" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-gold-500 focus:ring-4 focus:ring-gold-100" placeholder="name@example.com">
                        </div>

                        <div id="detail-link" class="mt-4 hidden">
                            <label class="mb-2 block text-sm font-bold text-slate-700">الرابط الخارجي</label>
                            <input type="url" name="application_link" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-gold-500 focus:ring-4 focus:ring-gold-100" placeholder="https://example.com/apply">
                        </div>
                    </div>
                </div>

            <div class="space-y-2">
                <label for="slug" class="block text-sm font-black text-slate-700">الرابط المختصر</label>
                <input id="slug" name="slug" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="اختياري إذا لم تكتبه سيُنشأ تلقائياً">
            </div>

            <div class="space-y-2">
                <label for="description" class="block text-sm font-black text-slate-700">وصف الوظيفة</label>
                <textarea id="description" name="description" rows="6" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="اكتب وصف الوظيفة..."></textarea>
            </div>

            <div class="space-y-2">
                <label for="requirements" class="block text-sm font-black text-slate-700">المتطلبات</label>
                <textarea id="requirements" name="requirements" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="اكتب متطلبات الوظيفة..."></textarea>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-2">
                    <label for="salary_min" class="block text-sm font-black text-slate-700">الراتب من</label>
                    <input id="salary_min" name="salary_min" type="number" min="0" step="0.01" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="5000">
                </div>
                <div class="space-y-2">
                    <label for="salary_max" class="block text-sm font-black text-slate-700">الراتب إلى</label>
                    <input id="salary_max" name="salary_max" type="number" min="0" step="0.01" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="12000">
                </div>
            </div>

                <div class="md:col-span-2" x-data="benefitsManager()">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-black text-slate-700">المزايا الوظيفية</label>
                            <p class="mt-1 text-xs text-slate-500">أضف المزايا واحدة تلو الأخرى بشكل منظم</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 px-3 py-1"><span class="text-xs font-bold text-emerald-700"><span x-text="benefits.length"></span> مزايا</span></div>
                    </div>
                    <div class="mb-4 flex gap-2">
                        <input type="text" x-model="newBenefit" @keydown.enter.prevent="addBenefit()" class="flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-gold-400 focus:bg-white" placeholder="أضف ميزة (مثل: راتب تنافسي، تأمين صحي...)">
                        <button type="button" @click="addBenefit()" class="rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-600 active:scale-95"><i class="bx bx-plus text-lg"></i></button>
                    </div>
                    <div class="mb-4 space-y-2">
                        <template x-for="(benefit, index) in benefits" :key="index">
                            <div class="flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 p-3">
                                <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white"><i class="bx bx-check text-base"></i></span>
                                <span class="flex-1 text-sm font-bold text-slate-700" x-text="benefit"></span>
                                <button type="button" @click="removeBenefit(index)" class="rounded-lg bg-red-100 p-2 text-red-600 transition hover:bg-red-200" title="حذف"><i class="bx bx-trash text-lg"></i></button>
                            </div>
                        </template>
                    </div>
                    <div x-show="benefits.length === 0" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center"><p class="text-sm text-slate-500">لم تضف أي مزايا حتى الآن</p></div>
                    <input type="hidden" name="benefits" :value="benefits.join(', ')">
                </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?= htmlspecialchars(url('/admin/jobs')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="gold-btn rounded-2xl px-5 py-3 text-sm font-black text-navy-900">حفظ الوظيفة</button>
            </div>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('application_method');
        const governorate = document.getElementById('governorate_id');
        const city = document.getElementById('city_id');
        const mappings = {
            whatsapp: 'detail-whatsapp',
            phone: 'detail-phone',
            email: 'detail-email',
            external_link: 'detail-link',
        };

        if (select) {
            const toggleFields = () => Object.entries(mappings).forEach(([key, id]) => {
                const field = document.getElementById(id);
                if (field) field.classList.toggle('hidden', select.value !== key);
            });

            select.addEventListener('change', toggleFields);
            toggleFields();
        }

        if (governorate && city) {
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
        }
    });

    function benefitsManager() {
        return {
            benefits: [],
            newBenefit: '',
            addBenefit() {
                const benefit = this.newBenefit.trim();
                if (benefit !== '' && !this.benefits.includes(benefit)) {
                    this.benefits.push(benefit);
                    this.newBenefit = '';
                }
            },
            removeBenefit(index) {
                this.benefits.splice(index, 1);
            }
        };
    }
</script>

<?php require_once __DIR__ . '/../../layout/admin/footer.php'; ?>
