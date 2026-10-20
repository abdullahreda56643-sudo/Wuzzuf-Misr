<?php
 $user = $user ?? current_user();
 $applications = $applications ?? [];
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
</style>

<div class="min-h-screen bg-slate-100" dir="rtl">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="mb-5 flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500">
            <a href="#" class="flex items-center gap-1.5 hover:text-gold-600 transition">
                <i class='bx bx-home text-base'></i> الرئيسية
            </a>
            <i class='bx bx-chevron-left text-slate-400'></i>
            <a href="<?= htmlspecialchars(url('/employer/dashboard')) ?>" class="hover:text-gold-600 transition">لوحة التحكم</a>
            <i class='bx bx-chevron-left text-slate-400'></i>
            <span class="text-slate-800">المتقدمون</span>
        </nav>

        <!-- Header / Hero -->
        <section class="relative overflow-hidden rounded-[28px] bg-gradient-to-l from-[#071b2f] via-[#0d2340] to-[#173d5d] p-6 text-white shadow-[0_24px_60px_rgba(7,27,47,0.28)] sm:p-8 lg:p-10">
            <div class="absolute -top-20 -left-20 h-64 w-64 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-10 h-72 w-72 rounded-full bg-[#173d5d]/40 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-500/15 px-3 py-1 text-xs font-bold text-gold-300 ring-1 ring-gold-500/20">
                            <i class='bx bx-user-pin'></i> إدارة التوظيف
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold text-emerald-300 ring-1 ring-emerald-500/20">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> نشط
                        </span>
                    </div>
                    <h1 class="text-2xl font-black leading-tight sm:text-3xl lg:text-4xl">
                        المتقدمون <span class="text-gold-400">للوظائف</span>
                    </h1>
                    <p class="mt-3 text-sm leading-7 text-slate-300">
                        مراجعة طلبات التقديم ومتابعة حالة المتقدمين للوظائف الخاصة بشركتك.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= htmlspecialchars(url('/employer/dashboard')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/10 hover-grow">
                        <i class='bx bx-right-arrow-alt'></i> العودة للوحة
                    </a>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="mt-6 job-card rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="icon-badge h-10 w-10 bg-gold-100 text-gold-700"><i class='bx bx-file-find text-lg'></i></span>
                    <div>
                        <p class="text-xs font-bold text-gold-600">ملف المتقدمين</p>
                        <h2 class="text-lg font-black text-slate-900 sm:text-xl">طلبات التقديم</h2>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-amber-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> قيد المراجعة
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> مقبول
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-red-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> مرفوض
                    </span>
                </div>
            </div>
            <div class="gold-line h-px w-full opacity-40 mb-6"></div>

            <?php if (empty($applications)): ?>
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
                    <span class="icon-badge h-16 w-16 mb-4 bg-slate-100 text-slate-400"><i class='bx bx-folder-open text-3xl'></i></span>
                    <h3 class="text-lg font-black text-slate-700">لا توجد طلبات تقديم</h3>
                    <p class="mt-2 text-sm text-slate-500">لم يتقدم أحد لوظائفك حتى الآن. سيتم عرض المتقدمين هنا فور استلام طلباتهم.</p>
                </div>
            <?php else: ?>
                <!-- Desktop Table -->
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500">
                                <th class="px-4 py-3 font-bold">الاسم</th>
                                <th class="px-4 py-3 font-bold">الوظيفة</th>
                                <th class="px-4 py-3 font-bold">تاريخ التقديم</th>
                                <th class="px-4 py-3 font-bold">الحالة</th>
                                <th class="px-4 py-3 font-bold">الملف</th>
                                <th class="px-4 py-3 font-bold">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr class="border-b border-slate-100 last:border-0 transition hover:bg-slate-50">
                                    <td class="px-4 py-4 font-bold text-slate-800">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-xs font-black text-slate-600"><?= htmlspecialchars(mb_substr($application['name'], 0, 1, 'UTF-8')) ?></span>
                                            <?= htmlspecialchars($application['name']) ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($application['job']) ?></td>
                                    <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($application['date']) ?></td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold <?= $application['status'] === 'تمت الموافقة' ? 'bg-emerald-100 text-emerald-700' : ($application['status'] === 'تمت المقابلة' ? 'bg-blue-100 text-blue-700' : ($application['status'] === 'مرفوض' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) ?>">
                                            <span class="h-1.5 w-1.5 rounded-full <?= $application['status'] === 'تمت الموافقة' ? 'bg-emerald-500' : ($application['status'] === 'تمت المقابلة' ? 'bg-blue-500' : ($application['status'] === 'مرفوض' ? 'bg-red-500' : 'bg-amber-500')) ?>"></span>
                                            <?= htmlspecialchars($application['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <?php if ($application['file_path'] !== ''): ?>
                                                <a href="<?= htmlspecialchars($application['file_path'], ENT_QUOTES, 'UTF-8') ?>" download="<?= htmlspecialchars($application['file_name'], ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-1 font-bold text-[#1d4ed8] hover:underline">
                                                    تحميل CV <i class='bx bx-download'></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-500">لا يوجد CV</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <?php if ($application['status_raw'] === 'pending' || $application['status_raw'] === 'reviewed'): ?>
                                                <button type="button" class="action-btn accept-btn inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-200 transition" data-app-id="<?= $application['id'] ?>" data-status="accepted">
                                                    <i class='bx bx-check'></i> قبول
                                                </button>
                                                <button type="button" class="action-btn reject-btn inline-flex items-center gap-1 rounded-lg bg-red-100 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-200 transition" data-app-id="<?= $application['id'] ?>" data-status="rejected">
                                                    <i class='bx bx-x'></i> رفض
                                                </button>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-500">تم الإجراء</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="space-y-4 md:hidden">
                    <?php foreach ($applications as $application): ?>
                        <div class="fade-in rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-sm font-black text-slate-600 shadow-sm ring-1 ring-slate-100"><?= htmlspecialchars(mb_substr($application['name'], 0, 1, 'UTF-8')) ?></span>
                                    <div>
                                        <p class="font-black text-slate-800"><?= htmlspecialchars($application['name']) ?></p>
                                        <p class="text-xs text-slate-500"><?= htmlspecialchars($application['job']) ?></p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold <?= $application['status'] === 'تمت الموافقة' ? 'bg-emerald-100 text-emerald-700' : ($application['status'] === 'تمت المقابلة' ? 'bg-blue-100 text-blue-700' : ($application['status'] === 'مرفوض' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) ?>">
                                    <span class="h-1.5 w-1.5 rounded-full <?= $application['status'] === 'تمت الموافقة' ? 'bg-emerald-500' : ($application['status'] === 'تمت المقابلة' ? 'bg-blue-500' : ($application['status'] === 'مرفوض' ? 'bg-red-500' : 'bg-amber-500')) ?>"></span>
                                    <?= htmlspecialchars($application['status']) ?>
                                </span>
                            </div>
                            <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-xs">
                                <span class="text-slate-500 flex items-center gap-1"><i class='bx bx-calendar text-slate-400'></i> <?= htmlspecialchars($application['date']) ?></span>
                                <div class="flex flex-col gap-2">
                                    <?php if ($application['file_path'] !== ''): ?>
                                        <a href="<?= htmlspecialchars($application['file_path'], ENT_QUOTES, 'UTF-8') ?>" download="<?= htmlspecialchars($application['file_name'], ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-1 font-bold text-[#1d4ed8]">
                                            تحميل CV <i class='bx bx-download'></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-500">لا يوجد CV</span>
                                    <?php endif; ?>
                                    <?php if ($application['status_raw'] === 'pending' || $application['status_raw'] === 'reviewed'): ?>
                                        <div class="flex items-center gap-2 mt-2">
                                            <button type="button" class="action-btn accept-btn flex-1 rounded-lg bg-emerald-100 px-2 py-1.5 text-xs font-bold text-emerald-700" data-app-id="<?= $application['id'] ?>" data-status="accepted">
                                                قبول
                                            </button>
                                            <button type="button" class="action-btn reject-btn flex-1 rounded-lg bg-red-100 px-2 py-1.5 text-xs font-bold text-red-700" data-app-id="<?= $application['id'] ?>" data-status="rejected">
                                                رفض
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionButtons = document.querySelectorAll('.action-btn');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            handleApplicationAction(
                this.getAttribute('data-app-id'),
                this.getAttribute('data-status')
            );
        });
    });
});

function handleApplicationAction(appId, status) {
    const statusLabels = {
        'accepted': 'قبول',
        'rejected': 'رفض',
        'reviewed': 'مراجعة',
        'shortlisted': 'تصفية'
    };
    
    const message = prompt(`هل تريد فعلاً ${statusLabels[status]} هذا الطلب؟\n\n(اختياري: أضف رسالة لإرسالها للمتقدم)`);
    
    if (message === null) return; // User cancelled
    
    const formData = new FormData();
    formData.append('application_id', appId);
    formData.append('status', status);
    formData.append('message', message);
    
    fetch('<?= htmlspecialchars(url('/employer/update-application-status')) ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`تم ${statusLabels[status]} الطلب بنجاح`);
            location.reload();
        } else {
            alert('خطأ: ' + (data.error || 'حدث خطأ'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطأ في الاتصال بالسيرفر');
    });
}
</script>