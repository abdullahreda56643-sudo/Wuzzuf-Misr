<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل صاحب العمل</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['IBM Plex Sans Arabic', 'sans-serif'],
                        display: ['El Messiri', 'sans-serif']
                    },
                    colors: {
                        navy: { 900: '#071b2f', 800: '#0d2440', 700: '#12355d' },
                        gold: { 400: '#f2ce75', 500: '#c79734', 600: '#b47a18', 700: '#8d6115' }
                    },
                    boxShadow: {
                        soft: '0 18px 40px -20px rgba(7, 27, 47, 0.22)'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        body { font-size: 15px; line-height: 1.8; color: #0f172a; }
        h1, h2, h3, h4, h5, h6 { font-family: 'El Messiri', sans-serif; font-weight: 900; line-height: 1.3; letter-spacing: -0.02em; }
        h1 { font-size: clamp(2rem, 2.6vw, 3rem); }
        h2 { font-size: clamp(1.55rem, 2vw, 2.3rem); }
        h3 { font-size: clamp(1.3rem, 1.8vw, 1.75rem); }
        h4 { font-size: clamp(1.1rem, 1.5vw, 1.35rem); }
        p, li, span, a, label, input, textarea, select, button, small, .text-sm, .text-xs { font-weight: 700; }
        p, li, span, a, label, input, textarea, select, button, small { font-size: 0.96rem; }
        .text-sm { font-size: 0.95rem !important; }
        .text-xs { font-size: 0.82rem !important; }
        .field { width: 100%; border-radius: 18px; border: 1px solid #e5e7eb; background: #f8fafc; padding: 0.9rem 1rem; font-size: 0.95rem; color: #0f172a; transition: all 0.2s ease; }
        .field:focus { border-color: #c79734; background: #fff; box-shadow: 0 0 0 4px rgba(199, 151, 52, 0.12); outline: none; }
        .field::placeholder { color: #94a3b8; }
        textarea.field { min-height: 120px; resize: vertical; }
    </style>
</head>
<body class="min-h-screen bg-slate-100">
    <div class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="w-full max-w-5xl overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-soft">
            <div class="grid lg:grid-cols-[0.9fr_1.1fr]">
                <aside class="hidden bg-gradient-to-br from-[#071b2f] via-[#0d2440] to-[#16395d] p-8 text-white lg:flex lg:flex-col lg:justify-between">
                    <div>
                        <div class="mb-8 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-xl font-black text-gold-400">★</div>
                            <div>
                                <div class="text-[0.68rem] font-bold tracking-[0.28em] text-slate-300">JOB PLATFORM</div>
                                <div class="text-2xl font-black">Wazifaty</div>
                            </div>
                        </div>

                        <div class="inline-flex items-center gap-2 rounded-full border border-gold-400/30 bg-gold-500/10 px-3 py-2 text-xs font-bold text-gold-300">
                            <span class="h-2.5 w-2.5 rounded-full bg-gold-400"></span>
                            التوظيف الاحترافي
                        </div>

                        <h1 class="mt-8 text-4xl font-black leading-[1.2]">
                            <span>ابدأ</span><br>
                            <span class="text-gold-300">بتوظيف المواهب</span><br>
                            <span>التي تناسبك</span>
                        </h1>

                        <p class="mt-6 max-w-sm text-base leading-8 text-slate-200">
                            أنشئ شركتك الرقمية وقم بنشر وظائفك بسهولة، وواصل مع أفضل المرشحين في سوق العمل.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                            <div class="text-3xl font-black text-white">10k+</div>
                            <div class="mt-1 text-sm text-slate-200">باحثين جدد</div>
                        </div>
                        <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                            <div class="text-3xl font-black text-white">500+</div>
                            <div class="mt-1 text-sm text-slate-200">شركات ناشطة</div>
                        </div>
                    </div>
                </aside>

                <section class="p-5 sm:p-8 lg:p-10">
                    <div class="mb-8 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-gold-600">مرحباً بك</p>
                            <h2 class="mt-1 text-3xl font-black text-navy-900">تسجيل صاحب العمل</h2>
                        </div>
                        <a href="<?= htmlspecialchars(url('/')) ?>" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50">← الرئيسية</a>
                    </div>

                    <?php if ($flash): ?>
                        <div class="mb-6 rounded-2xl border px-4 py-3 text-sm font-medium <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' ?>">
                            <?= htmlspecialchars($flash['message']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= htmlspecialchars(url('/employer/register')) ?>" class="space-y-5" enctype="multipart/form-data">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-700">اسم الشركة</label>
                                <input type="text" name="company_name" class="field" placeholder="اسم الشركة بالكامل" required>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">اسم المسؤول</label>
                                <input type="text" name="manager_name" class="field" placeholder="اسم المسؤول أو مدير التوظيف" required>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">النشاط التجاري</label>
                                <select name="industry" class="field" required>
                                    <option value="">اختر النشاط</option>
                                    <option value="تكنولوجيا المعلومات">تكنولوجيا المعلومات</option>
                                    <option value="التصميم والهوية">التصميم والهوية</option>
                                    <option value="التسويق والإعلان">التسويق والإعلان</option>
                                    <option value="التعليم">التعليم</option>
                                    <option value="البيع والتجارة">البيع والتجارة</option>
                                    <option value="خدمات مالية">خدمات مالية</option>
                                    <option value="تكنولوجيا طبية">تكنولوجيا طبية</option>
                                    <option value="أخرى">أخرى</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">البريد الإلكتروني</label>
                                <input type="email" name="email" class="field" placeholder="company@example.com" required>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">رقم الهاتف</label>
                                <input type="tel" name="phone" class="field" placeholder="010 1234 5678" required inputmode="numeric">
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">الموقع الإلكتروني <span class="text-slate-400">(اختياري)</span></label>
                                <input type="url" name="website" class="field" placeholder="https://example.com (اختياري)">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">شعار الشركة</label>
                                <input type="file" name="logo" accept="image/*" class="field file:mr-4 file:rounded-xl file:border-0 file:bg-gold-500 file:px-4 file:py-2 file:text-sm file:font-bold file:text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">وصف الشركة</label>
                            <textarea name="description" class="field" placeholder="اكتب نبذة قصيرة عن الشركة ونشاطها..."></textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">كلمة المرور</label>
                            <input type="password" name="password" class="field" placeholder="••••••••••" required>
                        </div>

                        <button type="submit" class="w-full rounded-[18px] bg-gradient-to-r from-[#0d2236] to-[#173d5d] px-5 py-4 text-base font-black text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5">
                            إنشاء حساب صاحب العمل
                        </button>
                    </form>

                    <div class="mt-7 text-center text-sm text-slate-600">
                        لديك حساب بالفعل؟
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="mr-1 font-black text-gold-600 hover:text-gold-700">تسجيل الدخول</a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
