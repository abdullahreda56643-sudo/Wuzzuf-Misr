<?php
$user = $user ?? current_user();
$durations = $durations ?? [];
?>
<div class="min-h-screen bg-slate-100">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-[28px] bg-gradient-to-r from-[#081d2f] via-[#0e2d46] to-[#163d5a] p-6 text-white shadow-[0_22px_50px_rgba(8,29,47,0.22)]">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-gold-300">إدارة الوظائف</p>
                    <h1 class="mt-2 text-3xl font-black">إضافة وظيفة جديدة</h1>
                </div>
                <a href="<?= htmlspecialchars(url('/employer/jobs')) ?>" class="rounded-2xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/10">العودة إلى الوظائف</a>
            </div>
        </div>

        <form method="POST" action="<?= htmlspecialchars(url('/employer/jobs/create')) ?>" class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-gold-600">بيانات الوظيفة</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-900">معلومات الوظيفة</h2>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">مسودة</span>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">المسمى الوظيفي</label>
                    <input type="text" name="title" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" placeholder="مثال: مطور واجهات أمامية" required>
                </div>

                <div class="md:col-span-2">
                    <div id="application-details-wrap" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <label class="mb-2 block text-sm font-bold text-slate-700">طريقة التقديم</label>
                        <select id="application_method" name="application_method" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100">
                            <option value="internal">داخل الموقع</option>
                            <option value="whatsapp">واتساب</option>
                            <option value="phone">هاتف</option>
                            <option value="email">بريد إلكتروني</option>
                            <option value="external_link">رابط خارجي</option>
                        </select>

                        <div id="detail-whatsapp" class="mt-4 hidden">
                            <label class="mb-2 block text-sm font-bold text-slate-700">رقم الواتساب</label>
                            <div class="flex items-stretch overflow-hidden rounded-2xl border border-slate-200 bg-white focus-within:border-gold-500 focus-within:ring-4 focus-within:ring-gold-100">
                                <span class="flex items-center border-l border-slate-200 bg-slate-50 px-4 text-sm font-black text-slate-600" dir="ltr">+20</span>
                                <input type="tel" name="application_whatsapp" inputmode="numeric" class="min-w-0 flex-1 bg-transparent px-4 py-3 text-sm text-slate-800 focus:outline-none" placeholder="10xxxxxxxx" dir="ltr">
                            </div>
                        </div>

                        <div id="detail-phone" class="mt-4 hidden">
                            <label class="mb-2 block text-sm font-bold text-slate-700">رقم الهاتف</label>
                            <div class="flex items-stretch overflow-hidden rounded-2xl border border-slate-200 bg-white focus-within:border-gold-500 focus-within:ring-4 focus-within:ring-gold-100">
                                <span class="flex items-center border-l border-slate-200 bg-slate-50 px-4 text-sm font-black text-slate-600" dir="ltr">+20</span>
                                <input type="tel" name="application_phone" inputmode="numeric" class="min-w-0 flex-1 bg-transparent px-4 py-3 text-sm text-slate-800 focus:outline-none" placeholder="10xxxxxxxx" dir="ltr">
                            </div>
                        </div>

                        <div id="detail-email" class="mt-4 hidden">
                            <label class="mb-2 block text-sm font-bold text-slate-700">البريد الإلكتروني</label>
                            <input type="email" name="application_email" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" placeholder="name@example.com">
                        </div>

                        <div id="detail-link" class="mt-4 hidden">
                            <label class="mb-2 block text-sm font-bold text-slate-700">الرابط الخارجي</label>
                            <input type="url" name="application_link" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" placeholder="https://example.com/apply">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">القسم</label>
                    <select name="category_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" required>
                        <?php $firstCategoryId = (int) (($categories[0]['id'] ?? 2)); ?>
                        <?php foreach (($categories ?: [['id' => 2, 'name' => 'المبيعات والتسويق']]) as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= ((int) $category['id'] === $firstCategoryId) ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">نوع الوظيفة</label>
                    <select name="type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100">
                        <option value="full-time">دوام كامل</option>
                        <option value="part-time">دوام جزئي</option>
                        <option value="contract">عقد</option>
                        <option value="temporary">مؤقت</option>
                        <option value="internship">تدريب</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">المحافظة</label>
                    <select name="governorate_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" required>
                        <?php $firstGovernorateId = (int) (($governorates[0]['id'] ?? 1)); ?>
                        <?php foreach (($governorates ?: [['id' => 1, 'name' => 'القاهرة']]) as $governorate): ?>
                            <option value="<?= (int) $governorate['id'] ?>" <?= ((int) $governorate['id'] === $firstGovernorateId) ? 'selected' : '' ?>><?= htmlspecialchars($governorate['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">المدينة</label>
                    <select name="city_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" required>
                        <?php $firstCityId = (int) (($cities[0]['id'] ?? 1)); ?>
                        <?php foreach (($cities ?: [['id' => 1, 'name' => 'القاهرة']]) as $city): ?>
                            <option value="<?= (int) $city['id'] ?>" <?= ((int) $city['id'] === $firstCityId) ? 'selected' : '' ?>><?= htmlspecialchars($city['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">الراتب من</label>
                    <input type="number" min="0" name="salary_min" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" placeholder="5000">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">الراتب إلى</label>
                    <input type="number" min="0" name="salary_max" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" placeholder="12000">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">الخبرة</label>
                    <select name="experience_level" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100">
                        <option value="entry">مبتدئ</option>
                        <option value="junior">Junior</option>
                        <option value="mid">متوسط</option>
                        <option value="senior">Senior</option>
                        <option value="lead">Lead</option>
                        <option value="executive">Executive</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">المؤهل</label>
                    <input type="text" name="qualification" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" placeholder="بكالوريوس في نظم المعلومات">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">مدة الإعلان</label>
                    <select name="advert_days" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100">
                        <?php foreach ($durations as $duration): ?>
                            <option value="<?= (int) ($duration['days'] ?? 0) ?>"><?= htmlspecialchars((string) ($duration['name'] ?? (($duration['days'] ?? 0) . ' يوم'))) ?></option>
                        <?php endforeach; ?>
                        <?php if (empty($durations)): ?><option value="30" selected>30 يوم</option><?php endif; ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">الوصف</label>
                    <textarea name="description" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" placeholder="اكتب وصف الوظيفة بالتفصيل..." required></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">المتطلبات</label>
                    <textarea name="requirements" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" placeholder="المهارات والخبرات المطلوبة..."></textarea>
                </div>

                <!-- Dynamic Benefits Manager -->
                <div class="md:col-span-2" x-data="benefitsManager()">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">المزايا الوظيفية</label>
                            <p class="mt-1 text-xs text-slate-500">أضف المزايا واحدة تلو الأخرى بشكل منظم</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 px-3 py-1">
                            <span class="text-xs font-bold text-emerald-700"><span x-text="benefits.length"></span> مزايا</span>
                        </div>
                    </div>

                    <!-- Benefits Input -->
                    <div class="flex gap-2 mb-4">
                        <input 
                            type="text" 
                            x-model="newBenefit" 
                            @keydown.enter="addBenefit()"
                            class="flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-gold-500 focus:outline-none focus:ring-4 focus:ring-gold-100" 
                            placeholder="أضف ميزة (مثل: راتب تنافسي، تأمين صحي...)"
                        >
                        <button 
                            type="button" 
                            @click="addBenefit()"
                            class="rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-600 active:scale-95"
                        >
                            <i class='bx bx-plus text-lg'></i>
                        </button>
                    </div>

                    <!-- Benefits List -->
                    <div class="space-y-2 mb-4">
                        <template x-for="(benefit, index) in benefits" :key="index">
                            <div class="flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 transition hover:border-emerald-300">
                                <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">
                                    <i class='bx bx-check text-base'></i>
                                </span>
                                <span class="flex-1 text-sm font-bold text-slate-700" x-text="benefit"></span>
                                <button 
                                    type="button"
                                    @click="removeBenefit(index)"
                                    class="rounded-lg bg-red-100 p-2 text-red-600 transition hover:bg-red-200"
                                    title="حذف"
                                >
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="benefits.length === 0" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center">
                        <p class="text-sm text-slate-500">لم تضف أي مزايا حتى الآن</p>
                    </div>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="benefits" :value="benefits.join(', ')" />
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="<?= htmlspecialchars(url('/employer/jobs')) ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">إلغاء</a>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-[#0d2236] to-[#173d5d] px-6 py-3 text-sm font-black text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5">إرسال للمراجعة</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('application_method');
        const mappings = {
            whatsapp: 'detail-whatsapp',
            phone: 'detail-phone',
            email: 'detail-email',
            external_link: 'detail-link',
        };

        const toggleFields = () => {
            const value = select.value;
            Object.entries(mappings).forEach(([key, id]) => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.toggle('hidden', key !== value);
                }
            });
        };

        if (select) {
            select.addEventListener('change', toggleFields);
            toggleFields();
        }
    });
</script>

<script>
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