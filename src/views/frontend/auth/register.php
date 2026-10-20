<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$siteName = config('site_name', 'بوابة وظائف مصر | Wazaef Misr');
$siteLogo = asset('logo.png');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد | منصة وظيفتي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['IBM Plex Sans Arabic', 'sans-serif'],
                        display: ['El Messiri', 'sans-serif']
                    },
                    boxShadow: {
                        premium: '0 30px 80px rgba(7,27,42,0.35)',
                        soft: '0 12px 32px rgba(15, 23, 42, 0.08)'
                    },
                    colors: {
                        navy: { 900: '#071b2a', 800: '#0f273a', 700: '#17324a' },
                        gold: { 300: '#f8dd9d', 400: '#f3cf78', 500: '#d9a94e', 600: '#c58d2d', 700: '#9f6d20' }
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
        h1, h2, h3, h4, h5, h6, .font-display { font-family: 'El Messiri', sans-serif; font-weight: 900; line-height: 1.3; letter-spacing: -0.02em; }
        h1 { font-size: clamp(2rem, 2.6vw, 3rem); }
        h2 { font-size: clamp(1.55rem, 2vw, 2.3rem); }
        h3 { font-size: clamp(1.3rem, 1.8vw, 1.75rem); }
        h4 { font-size: clamp(1.1rem, 1.5vw, 1.35rem); }
        p, li, span, a, label, input, textarea, select, button, small, .text-sm, .text-xs { font-weight: 700; }
        p, li, span, a, label, input, textarea, select, button, small { font-size: 0.96rem; }
        .text-sm { font-size: 0.95rem !important; }
        .text-xs { font-size: 0.82rem !important; }

        .journey-bg{
            background-color:#f4f8fc;
            background-image:
                radial-gradient(circle at 18% 8%, rgba(23,61,93,0.06), transparent 38%),
                radial-gradient(circle at 85% 92%, rgba(15,39,58,0.04), transparent 42%);
        }

        .seal{
            background: linear-gradient(135deg, #173d5d, #0f273a);
            box-shadow: 0 10px 24px rgba(15,39,58,0.18), inset 0 0 0 3px rgba(255,255,255,0.26);
            color: #ffffff;
        }

        /* ===== Stepper / journey path ===== */
        .path-track{ position:relative; height:2px; background:rgba(15,39,58,0.16); border-radius:999px; }
        .path-fill{ position:absolute; inset-inline-start:0; top:0; height:2px; background:linear-gradient(90deg,#0f273a,#173d5d); border-radius:999px; transition:width .45s cubic-bezier(.4,0,.2,1); }
        .path-node{
            width:2.15rem; height:2.15rem; border-radius:999px;
            display:flex; align-items:center; justify-content:center;
            font-family:'Tajawal',sans-serif; font-weight:800; font-size:.8rem;
            background:#e8eff7; border:2px solid #d0e0f0; color:#5a7a9a;
            transition:all .35s ease; position:relative; z-index:1; flex:0 0 auto;
        }
        .path-node[data-state="current"]{
            background: linear-gradient(135deg, #0f273a, #173d5d);
            border-color:transparent; color:#fff;
            box-shadow: 0 0 0 6px rgba(15,39,58,0.14), 0 8px 18px rgba(15,39,58,0.25);
        }
        .path-node[data-state="done"]{ background:#173d5d; border-color:#d9a94e; color:#fff; }
        .path-label{ font-size:.68rem; color:#7a92ab; transition:color .3s ease; }
        .path-label[data-state="current"]{ color:#0f273a; font-weight:800; }

        /* ===== Card & steps ===== */
        .card{ background:#fff; }
        .step-panel{ animation: stepIn .35s ease; }
        @keyframes stepIn{ from{ opacity:0; transform:translateY(8px); } to{ opacity:1; transform:translateY(0); } }

        .role-pick{
            border:1.5px solid #d0e0f0; background:#fff; transition:all .2s ease;
            min-height: 11.5rem;
        }
        .role-pick:hover{ border-color:#173d5d; box-shadow:0 10px 24px rgba(23,61,93,0.18); transform:translateY(-2px); }
        .role-icon{
            display:flex; align-items:center; justify-content:center;
            width:4.25rem; height:4.25rem; border-radius:1.25rem;
            background:linear-gradient(135deg,#edf5ff,#e3eefb); color:#173d5d;
            border:1px solid rgba(23,61,93,0.08); box-shadow:inset 0 0 0 1px rgba(255,255,255,0.5);
        }
        .role-icon i{ font-size:2rem; line-height:1; }
        .role-icon.employer{ background:linear-gradient(135deg,#f8fafc,#edf3fa); color:#0f273a; }

        @media (max-width: 640px) {
            .journey-bg {
                background-image: radial-gradient(circle at 20% 10%, rgba(23,61,93,0.05), transparent 28%), radial-gradient(circle at 80% 90%, rgba(15,39,58,0.04), transparent 36%);
            }
            .brand-row {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                gap: 0.75rem;
            }
            .brand-row > div {
                margin: 0 auto;
            }
            .brand-row .home-link {
                display: none;
            }
            .brand-row .brand-meta {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .brand-row .brand-meta p:last-child {
                max-width: 100%;
                font-size: 1rem;
                line-height: 1.5;
            }
            .card {
                border-radius: 1.4rem;
                padding: 1.1rem;
            }
            .role-pick {
                min-height: 10.5rem;
                padding: 0.9rem;
            }
            .role-icon {
                width: 3.25rem;
                height: 3.25rem;
                border-radius: 1rem;
            }
            .role-icon i {
                font-size: 1.45rem;
            }
            .step-panel h2 {
                font-size: 1.05rem;
            }
            .nav-btn {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .form-input {
                padding: .8rem .9rem;
                font-size: .9rem;
            }
        }

        /* ===== Inputs ===== */
        .input-shell{ position:relative; }
        .input-shell svg{ position:absolute; right:1rem; top:50%; transform:translateY(-50%); width:1.05rem; height:1.05rem; color:#7a92ab; }
        .form-input{
            width:100%; border-radius:14px; border:1px solid #d0e0f0; background:#fff;
            padding:.9rem 1rem; font-size:.95rem; color:#0f172a; transition:all .2s ease;
        }
        .input-shell input, .input-shell select{ padding-right:2.85rem; }
        .form-input:focus{ border-color:#173d5d; outline:none; box-shadow:0 0 0 4px rgba(23,61,93,.12); }
        .form-input::placeholder{ color:#a5b8d0; }
        select.form-input{
            appearance:none;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2315244d' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat:no-repeat; background-position:left 1rem center; background-size:1rem; padding-left:2.6rem;
        }

        .nav-btn{ transition: all .2s ease; }
    </style>
</head>
<body class="journey-bg min-h-screen text-slate-800 bg-white">
    <div class="min-h-screen flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-xl">

            <!-- Brand -->
            <div class="brand-row mb-8 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="<?= htmlspecialchars($siteLogo) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-12 w-12 rounded-xl border border-slate-200 bg-white object-contain p-1 shadow-sm">
                    <div class="brand-meta min-w-0">
                        <p class="font-display text-[0.62rem] font-bold tracking-[0.22em] text-[#173d5d]">بوابة وظائف مصر</p>
                        <p class="max-w-[220px] text-lg font-black leading-6 text-[#0f273a]"><?= htmlspecialchars($siteName) ?></p>
                    </div>
                </div>
                <a href="<?= htmlspecialchars(url('/')) ?>" class="home-link text-sm font-bold text-[#173d5d] transition hover:text-[#0f273a] hover:underline">← الرئيسية</a>
            </div>
            
            <!-- Platform Description -->
            <div class="mb-8 rounded-2xl border border-[#dbeaf8] bg-[#edf5ff] p-5">
                <p class="text-sm font-bold text-[#173d5d]">🚀 منصة وظائف احترافية</p>
                <p class="mt-2 text-sm leading-6 text-slate-700">تربطك بأفضل الفرص الوظيفية وتساعدك على بناء مستقبل مهني مشرق. انضم لآلاف الموظفين والشركات الذين يثقون بنا.</p>
            </div>

            <!-- Journey stepper -->
            <div id="stepperWrap" class="mb-6 hidden">
                <div class="mb-2 flex items-center justify-between" id="stepperNodes"></div>
                <div class="path-track"><div class="path-fill" id="pathFill" style="width:0%"></div></div>
                <p class="mt-3 text-center font-display text-xs font-bold tracking-wide text-gold-300" id="stepLabel"></p>
            </div>

            <!-- Card -->
            <div class="card rounded-[28px] p-6 shadow-premium sm:p-8">

                <?php if ($flash): ?>
                    <div class="mb-6 rounded-2xl border px-4 py-3 text-sm font-medium <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' ?>">
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <!-- ROLE SELECTION SCREEN -->
                <div id="roleScreen">
                    <p class="mb-1 font-display text-xs font-bold tracking-[0.2em] text-gold-600">ابدأ رحلتك</p>
                    <h1 class="mb-6 text-2xl font-black text-slate-900 sm:text-3xl">إزاي حابب تنضم لينا؟</h1>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <button type="button" data-pick="job_seeker" class="role-pick rounded-2xl p-3 text-right sm:p-5">
                            <span class="role-icon mb-3 sm:mb-4">
                                <i class='bx bx-user'></i>
                            </span>
                            <span class="block text-sm font-black text-slate-900 sm:text-base">باحث عن عمل</span>
                            <span class="mt-1 block text-[0.7rem] leading-5 text-slate-500 sm:text-sm">سجّل وابدأ رحلتك نحو فرصتك القادمة</span>
                        </button>
                        <button type="button" data-pick="employer" class="role-pick rounded-2xl p-3 text-right sm:p-5">
                            <span class="role-icon employer mb-3 sm:mb-4">
                                <i class='bx bx-building-house'></i>
                            </span>
                            <span class="block text-sm font-black text-slate-900 sm:text-base">صاحب عمل</span>
                            <span class="mt-1 block text-[0.7rem] leading-5 text-slate-500 sm:text-sm">انشر وظائفك وابدأ باكتساب المواهب</span>
                        </button>
                    </div>

                    <p class="mt-7 text-center text-sm text-slate-600">
                        لديك حساب بالفعل؟
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="mr-1 font-black text-[#0f273a] transition hover:text-[#173d5d]">تسجيل الدخول</a>
                    </p>
                </div>

                <!-- JOB SEEKER WIZARD -->
                <form id="jobSeekerForm" method="POST" action="<?= htmlspecialchars(url('/register')) ?>" class="hidden" autocomplete="on">
                    <input type="hidden" name="type" value="job_seeker">
                    <button type="button" data-back class="mb-5 inline-flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-navy-900 transition">→ تغيير نوع الحساب</button>

                    <div class="step-panel" data-jsstep="basic">
                        <h2 class="mb-1 text-xl font-black text-slate-900">البيانات الأساسية</h2>
                        <p class="mb-5 text-sm text-slate-500">هنستخدمها في التعريف بك للشركات.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="input-shell">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                                <input type="text" name="first_name" required class="form-input" placeholder="الاسم الأول" autocomplete="given-name">
                            </div>
                            <div class="input-shell">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                                <input type="text" name="last_name" required class="form-input" placeholder="اسم العائلة" autocomplete="family-name">
                            </div>
                        </div>
                    </div>

                    <div class="step-panel hidden" data-jsstep="contact">
                        <h2 class="mb-1 text-xl font-black text-slate-900">وسائل التواصل</h2>
                        <p class="mb-5 text-sm text-slate-500">عشان الشركات تقدر توصلك بسهولة.</p>
                        <div class="space-y-4">
                            <div class="input-shell">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                                <input type="email" name="email" required class="form-input" placeholder="example@gmail.com" autocomplete="email">
                            </div>
                            <div class="input-shell">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72 12.34 12.34 0 0 0 .7 4.45 2 2 0 0 1-.46 2.24L4.5 9.5a16 16 0 0 0 10 10l1.09-1.09a2 2 0 0 1 2.24-.46 12.34 12.34 0 0 0 4.45.7A2 2 0 0 1 22 16.92Z"/></svg>
                                <input type="tel" name="phone" class="form-input" placeholder="010 1234 5678" autocomplete="tel" inputmode="numeric">
                            </div>
                        </div>
                    </div>

                    <div class="step-panel hidden" data-jsstep="security">
                        <h2 class="mb-1 text-xl font-black text-slate-900">تأمين الحساب</h2>
                        <p class="mb-5 text-sm text-slate-500">اختر كلمة مرور قوية لحماية حسابك.</p>
                        <div class="input-shell">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>
                            <input type="password" name="password" required class="form-input" placeholder="••••••••••" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between gap-3">
                        <button type="button" data-prev class="nav-btn hidden rounded-[14px] border border-slate-300 px-5 py-3 text-sm font-black text-slate-700 hover:bg-slate-100 transition">السابق</button>
                        <button type="button" data-next class="nav-btn mr-auto rounded-[14px] bg-[linear-gradient(135deg,#0f273a,#173d5d)] px-6 py-3 text-sm font-black text-white shadow-[0_14px_24px_rgba(15,39,58,0.28)] transition hover:-translate-y-0.5 hover:shadow-[0_18px_36px_rgba(15,39,58,0.35)]">التالي ←</button>
                        <button type="submit" data-submit class="nav-btn mr-auto hidden w-full rounded-[14px] bg-[linear-gradient(135deg,#0f273a,#173d5d)] px-6 py-3.5 text-sm font-black text-white shadow-[0_14px_24px_rgba(15,39,58,0.28)] transition hover:-translate-y-0.5 hover:shadow-[0_18px_36px_rgba(15,39,58,0.35)]">إنشاء الحساب ✓</button>
                    </div>
                </form>

                <!-- EMPLOYER WIZARD -->
                <form id="employerForm" method="POST" action="<?= htmlspecialchars(url('/employer/register')) ?>" class="hidden" autocomplete="on" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="employer">
                    <button type="button" data-back class="mb-5 inline-flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-navy-900 transition">→ تغيير نوع الحساب</button>

                    <div class="step-panel" data-emstep="company">
                        <h2 class="mb-1 text-xl font-black text-slate-900">بيانات الشركة</h2>
                        <p class="mb-5 text-sm text-slate-500">التفاصيل الأساسية اللي هتظهر للباحثين.</p>
                        <div class="space-y-4">
                            <div class="input-shell">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1M14 9h1M9 13h1M14 13h1"/></svg>
                                <input type="text" name="company_name" class="form-input" placeholder="اسم الشركة بالكامل" required>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="input-shell">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                                    <input type="text" name="manager_name" class="form-input" placeholder="اسم مدير التوظيف" required>
                                </div>
                                <div class="input-shell">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                                    <select name="industry" class="form-input" required>
                                        <option value="">النشاط التجاري</option>
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
                        </div>
                    </div>

                    <div class="step-panel hidden" data-emstep="contact">
                        <h2 class="mb-1 text-xl font-black text-slate-900">وسائل التواصل</h2>
                        <p class="mb-5 text-sm text-slate-500">عشان الباحثين والدعم يقدروا يوصلولكم.</p>
                        <div class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="input-shell">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                                    <input type="email" name="email" class="form-input" placeholder="company@example.com" required>
                                </div>
                                <div class="input-shell">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72 12.34 12.34 0 0 0 .7 4.45 2 2 0 0 1-.46 2.24L4.5 9.5a16 16 0 0 0 10 10l1.09-1.09a2 2 0 0 1 2.24-.46 12.34 12.34 0 0 0 4.45.7A2 2 0 0 1 22 16.92Z"/></svg>
                                    <input type="tel" name="phone" class="form-input" placeholder="010 1234 5678" required inputmode="numeric">
                                </div>
                            </div>
                            <div class="input-shell">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20"/></svg>
                                <input type="url" name="website" class="form-input" placeholder="https://example.com (اختياري)" aria-label="الموقع الإلكتروني اختياري">
                            </div>
                        </div>
                    </div>

                    <div class="step-panel hidden" data-emstep="identity">
                        <h2 class="mb-1 text-xl font-black text-slate-900">الهوية البصرية</h2>
                        <p class="mb-5 text-sm text-slate-500">شعار ونبذة قصيرة يخلوا ملفكم أكثر احترافية.</p>
                        <div class="space-y-4">
                            <div>
                                <label class="mb-2 block text-xs font-bold text-slate-500">شعار الشركة</label>
                                <input type="file" name="logo" accept="image/*" class="form-input file:ml-4 file:rounded-xl file:border-0 file:bg-[linear-gradient(135deg,#0f273a,#173d5d)] file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                            </div>
                            <textarea name="description" rows="3" class="form-input" placeholder="اكتب نبذة قصيرة عن الشركة ونشاطها..."></textarea>
                        </div>
                    </div>

                    <div class="step-panel hidden" data-emstep="security">
                        <h2 class="mb-1 text-xl font-black text-slate-900">تأمين الحساب</h2>
                        <p class="mb-5 text-sm text-slate-500">اختر كلمة مرور قوية لحماية حساب الشركة.</p>
                        <div class="input-shell">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>
                            <input type="password" name="password" class="form-input" placeholder="••••••••••" required>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between gap-3">
                        <button type="button" data-prev class="nav-btn hidden rounded-[14px] border border-slate-300 px-5 py-3 text-sm font-black text-slate-700 hover:bg-slate-100 transition">السابق</button>
                        <button type="button" data-next class="nav-btn mr-auto rounded-[14px] bg-[linear-gradient(135deg,#0f273a,#173d5d)] px-6 py-3 text-sm font-black text-white shadow-[0_14px_24px_rgba(15,39,58,0.28)] transition hover:-translate-y-0.5 hover:shadow-[0_18px_36px_rgba(15,39,58,0.35)]">التالي ←</button>
                        <button type="submit" data-submit class="nav-btn mr-auto hidden w-full rounded-[14px] bg-[linear-gradient(135deg,#0f273a,#173d5d)] px-6 py-3.5 text-sm font-black text-white shadow-[0_14px_24px_rgba(15,39,58,0.28)] transition hover:-translate-y-0.5 hover:shadow-[0_18px_36px_rgba(15,39,58,0.35)]">إنشاء الحساب ✓</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        const roleScreen = document.getElementById('roleScreen');
        const stepperWrap = document.getElementById('stepperWrap');
        const stepperNodes = document.getElementById('stepperNodes');
        const pathFill = document.getElementById('pathFill');
        const stepLabelEl = document.getElementById('stepLabel');

        const configs = {
            job_seeker: {
                form: document.getElementById('jobSeekerForm'),
                attr: 'jsstep',
                steps: [
                    { key: 'basic', label: 'البيانات' },
                    { key: 'contact', label: 'التواصل' },
                    { key: 'security', label: 'الأمان' }
                ]
            },
            employer: {
                form: document.getElementById('employerForm'),
                attr: 'emstep',
                steps: [
                    { key: 'company', label: 'الشركة' },
                    { key: 'contact', label: 'التواصل' },
                    { key: 'identity', label: 'الهوية' },
                    { key: 'security', label: 'الأمان' }
                ]
            }
        };

        let activeRole = null;
        let activeIndex = 0;

        function renderStepper() {
            const cfg = configs[activeRole];
            stepperNodes.innerHTML = '';
            cfg.steps.forEach((s, i) => {
                const state = i < activeIndex ? 'done' : (i === activeIndex ? 'current' : 'upcoming');
                const node = document.createElement('div');
                node.className = 'path-node';
                node.dataset.state = state;
                node.textContent = state === 'done' ? '✓' : (i + 1);
                stepperNodes.appendChild(node);
            });
            const pct = cfg.steps.length > 1 ? (activeIndex / (cfg.steps.length - 1)) * 100 : 0;
            pathFill.style.width = pct + '%';
            stepLabelEl.textContent = `الخطوة ${activeIndex + 1} من ${cfg.steps.length} — ${cfg.steps[activeIndex].label}`;
        }

        function showStep(role, index) {
            const cfg = configs[role];
            cfg.form.querySelectorAll('[data-' + cfg.attr + ']').forEach(panel => {
                panel.classList.toggle('hidden', panel.dataset[cfg.attr] !== cfg.steps[index].key);
            });
            const prevBtn = cfg.form.querySelector('[data-prev]');
            const nextBtn = cfg.form.querySelector('[data-next]');
            const submitBtn = cfg.form.querySelector('[data-submit]');
            prevBtn.classList.toggle('hidden', index === 0);
            const isLast = index === cfg.steps.length - 1;
            nextBtn.classList.toggle('hidden', isLast);
            submitBtn.classList.toggle('hidden', !isLast);
            renderStepper();
        }

        function startRole(role) {
            activeRole = role;
            activeIndex = 0;
            roleScreen.classList.add('hidden');
            Object.values(configs).forEach(c => c.form.classList.add('hidden'));
            configs[role].form.classList.remove('hidden');
            stepperWrap.classList.remove('hidden');
            showStep(role, 0);
        }

        function backToRoleScreen() {
            stepperWrap.classList.add('hidden');
            Object.values(configs).forEach(c => c.form.classList.add('hidden'));
            roleScreen.classList.remove('hidden');
            activeRole = null;
            activeIndex = 0;
        }

        document.querySelectorAll('[data-pick]').forEach(btn => {
            btn.addEventListener('click', () => startRole(btn.dataset.pick));
        });

        document.querySelectorAll('[data-back]').forEach(btn => {
            btn.addEventListener('click', backToRoleScreen);
        });

        document.querySelectorAll('[data-next]').forEach(btn => {
            btn.addEventListener('click', () => {
                const cfg = configs[activeRole];
                const panel = cfg.form.querySelector('[data-' + cfg.attr + '="' + cfg.steps[activeIndex].key + '"]');
                const invalid = panel.querySelector(':invalid');
                if (invalid) { invalid.reportValidity(); return; }
                if (activeIndex < cfg.steps.length - 1) {
                    activeIndex++;
                    showStep(activeRole, activeIndex);
                }
            });
        });

        document.querySelectorAll('[data-prev]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (activeIndex > 0) {
                    activeIndex--;
                    showStep(activeRole, activeIndex);
                }
            });
        });
    </script>
</body>
</html>