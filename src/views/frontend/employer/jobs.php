<?php
 $user = $user ?? current_user();
 $jobs = $jobs ?? [];
?>
<link href='https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
    .job-card{transition:all .3s cubic-bezier(.4,0,.2,1)}
    .job-card:hover{box-shadow:0 18px 50px -12px rgba(7,27,47,.18);transform:translateY(-2px)}
    .stat-pill{transition:all .25s ease}
    .stat-pill:hover{transform:translateY(-2px);box-shadow:0 10px 25px -5px rgba(0,0,0,.08)}
    .icon-badge{display:inline-flex;align-items:center;justify-content:center;border-radius:14px}
    .hover-grow{transition:transform .2s ease}
    .hover-grow:hover{transform:scale(1.05)}
    .gold-line{background:linear-gradient(90deg,transparent,#d4a64a,transparent)}
    .fade-in { animation: fadeIn .4s ease both; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px);} to {opacity:1; transform: translateY(0);} }
    .job-item:hover { border-color: #e5c288; background: #fff; }
</style>

<div class="min-h-screen bg-slate-100" dir="rtl">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="mb-5 flex flex-wrap items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500">
            <a href="#" class="flex items-center gap-1.5 hover:text-gold-600 transition">
                <i class='bx bx-home text-base'></i> الرئيسية
            </a>
            <i class='bx bx-chevron-left text-slate-400'></i>
            <a href="<?= htmlspecialchars(url('/employer/dashboard')) ?>" class="hover:text-gold-600 transition">لوحة التحكم</a>
            <i class='bx bx-chevron-left text-slate-400'></i>
            <span class="text-slate-800">إدارة الوظائف</span>
        </nav>

        <!-- Header / Hero -->
        <section class="relative overflow-hidden rounded-[28px] bg-gradient-to-l from-[#071b2f] via-[#0d2340] to-[#173d5d] p-6 text-white shadow-[0_24px_60px_rgba(7,27,47,0.28)] sm:p-8 lg:p-10">
            <div class="absolute -top-20 -left-20 h-64 w-64 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-10 h-72 w-72 rounded-full bg-[#173d5d]/40 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-500/15 px-3 py-1 text-xs font-bold text-gold-300 ring-1 ring-gold-500/20">
                            <i class='bx bx-briefcase-alt-2'></i> وظائف الشركة
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold text-emerald-300 ring-1 ring-emerald-500/20">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> نشط
                        </span>
                    </div>
                    <h1 class="text-2xl font-black leading-tight sm:text-3xl lg:text-4xl">
                        إدارة <span class="text-gold-400">الوظائف</span>
                    </h1>
                    <p class="mt-3 text-sm leading-7 text-slate-300">
                        عرض، تعديل ومتابعة جميع الوظائف التي تم نشرها من قبل شركتك.
                    </p>
                </div>
                <div class="flex flex-shrink-0">
                    <a href="<?= htmlspecialchars(url('/employer/jobs/create')) ?>" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gold-500 px-6 py-3 text-sm font-black text-slate-900 shadow-lg shadow-gold-500/30 transition hover:bg-gold-400 hover-grow sm:w-auto">
                        <i class='bx bx-plus-circle'></i> إضافة وظيفة جديدة
                    </a>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="mt-6 job-card rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="icon-badge h-10 w-10 bg-gold-100 text-gold-700"><i class='bx bx-list-ul text-lg'></i></span>
                    <div>
                        <p class="text-xs font-bold text-gold-600">جدول الوظائف</p>
                        <h2 class="text-lg font-black text-slate-900 sm:text-xl">كل الوظائف</h2>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> نشط
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-amber-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> قيد المراجعة
                    </span>
                </div>
            </div>
            <div class="gold-line h-px w-full opacity-40 mb-6"></div>

            <div class="space-y-4">
                <?php if (empty($jobs)): ?>
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center sm:p-12">
                        <span class="icon-badge h-16 w-16 mb-4 bg-slate-100 text-slate-400"><i class='bx bx-folder-open text-3xl'></i></span>
                        <h3 class="text-base font-black text-slate-700 sm:text-lg">لا توجد وظائف مضافة</h3>
                        <p class="mt-2 text-sm text-slate-500">لم تقم بإضافة أي وظائف حتى الآن. ابدأ بإضافة وظيفتك الأولى لجذب المواهب.</p>
                        <a href="<?= htmlspecialchars(url('/employer/jobs/create')) ?>" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-black text-slate-900 hover-grow">
                            <i class='bx bx-plus'></i> أضف وظيفتك الأولى
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="job-item fade-in rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5 transition">
                            <!-- تم استخدام flex-col افتراضياً مع lg:flex-row للشاشات الكبيرة -->
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                
                                <!-- قسم تفاصيل الوظيفة -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-base font-black text-slate-900 sm:text-lg lg:text-xl"><?= htmlspecialchars($job['title']) ?></h3>
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold <?= ($job['status'] === 'نشط') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                            <span class="h-1.5 w-1.5 rounded-full <?= ($job['status'] === 'نشط') ? 'bg-emerald-500' : 'bg-amber-500' ?>"></span>
                                            <?= htmlspecialchars($job['status']) ?>
                                        </span>
                                    </div>
                                    <div class="mt-2 sm:mt-3 flex flex-wrap gap-x-4 gap-y-2 text-xs sm:text-sm text-slate-600">
                                        <span class="inline-flex items-center gap-1.5"><i class='bx bx-map text-gold-600'></i> <?= htmlspecialchars($job['location']) ?></span>
                                        <span class="inline-flex items-center gap-1.5"><i class='bx bx-briefcase text-gold-600'></i> <?= htmlspecialchars($job['type']) ?></span>
                                        <span class="inline-flex items-center gap-1.5"><i class='bx bx-calendar text-gold-600'></i> <?= htmlspecialchars($job['published']) ?></span>
                                    </div>
                                </div>

                                <!-- قسم الأزرار والإجراءات -->
                                <!-- يتم إضافة فاصل علوي على الجوال ليفصله عن التفاصيل بشكل مرتب -->
                                <div class="flex flex-wrap items-center gap-2 border-t border-slate-200 pt-4 lg:w-auto lg:flex-col lg:items-end lg:border-0 lg:pt-0 lg:gap-3 xl:flex-row xl:items-center">
                                    <span class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-white px-3 py-2 text-xs font-bold text-slate-700 border border-slate-200 shadow-sm lg:w-auto">
                                        <i class='bx bx-group text-slate-500'></i> الطلبات: <?= (int) $job['applications'] ?>
                                    </span>
                                    <div class="flex w-full gap-2 lg:w-auto">
                                        <a href="<?= htmlspecialchars(url('/employer/jobs/' . $job['id'] . '/edit')) ?>" class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-black text-slate-700 transition hover:bg-slate-100 hover-grow lg:flex-none">
                                            <i class='bx bx-edit'></i> تعديل
                                        </a>
                                        <a href="<?= htmlspecialchars(url('/employer/applications')) ?>" class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-gradient-to-l from-[#071b2f] to-[#173d5d] px-4 py-2.5 text-xs font-black text-white shadow-md transition hover:-translate-y-0.5 hover:shadow-lg hover-grow lg:flex-none">
                                            <i class='bx bx-user-check'></i> المتقدمون
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>