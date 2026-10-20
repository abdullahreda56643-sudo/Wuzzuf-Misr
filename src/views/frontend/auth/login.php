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
    <title>تسجيل الدخول | منصة وظيفتي</title>
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

        .card{ background:#fff; }
        .input-shell{ position:relative; }
        .input-shell i{ position:absolute; right:1rem; top:50%; transform:translateY(-50%); font-size:1.1rem; color:#7a92ab; }
        .form-input{
            width:100%; border-radius:16px; border:1px solid #d0e0f0; background:#fff;
            padding:.9rem 1rem .9rem 2.9rem; font-size:.95rem; color:#0f172a; transition:all .2s ease;
        }
        .input-shell input{ padding-right:2.9rem; }
        .form-input:focus{ border-color:#173d5d; outline:none; box-shadow:0 0 0 4px rgba(23,61,93,.12); }
        .form-input::placeholder{ color:#a5b8d0; }

        .nav-btn{ transition: all .2s ease; }

        @media (max-width: 640px) {
            .brand-row {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                gap: 0.85rem;
            }
            .brand-row .home-link { display: none; }
            .brand-row .brand-meta { display:flex; flex-direction:column; align-items:center; }
            .card {
                border-radius: 1.35rem;
                padding: 1.15rem;
            }
            .form-input {
                padding: .8rem .9rem .8rem 2.8rem;
                font-size: .9rem;
            }
            .login-title {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body class="journey-bg min-h-screen text-slate-800 bg-white">
    <div class="min-h-screen flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-xl">

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

            <div class="mb-8 rounded-2xl border border-[#dbeaf8] bg-[#edf5ff] p-5">
                <p class="text-sm font-bold text-[#173d5d]">🚀 منصة وظائف احترافية</p>
                <p class="mt-2 text-sm leading-6 text-slate-700">سجل دخولك للوصول إلى لوحة التحكم، متابعة طلباتك، وإدارة فرص العمل التي تهمك.</p>
            </div>

            <div class="card rounded-[28px] p-6 shadow-premium sm:p-8">
                <?php if ($flash): ?>
                    <div class="mb-6 rounded-2xl border px-4 py-3 text-sm font-medium <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' ?>">
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <div class="mb-6 text-center">
                    <p class="mb-1 font-display text-xs font-bold tracking-[0.2em] text-gold-600">مرحباً بعودتك</p>
                    <h1 class="login-title text-2xl font-black text-slate-900 sm:text-3xl">تسجيل الدخول</h1>
                </div>

                <form method="POST" action="<?= htmlspecialchars(url('/login')) ?>" class="space-y-5" autocomplete="on">
                    <div class="input-shell">
                        <i class='bx bx-envelope'></i>
                        <input id="email" type="email" name="email" required value="" class="form-input" placeholder="name@example.com" autocomplete="email" />
                    </div>

                    <div class="input-shell">
                        <i class='bx bx-lock-alt'></i>
                        <input id="password" type="password" name="password" required class="form-input" placeholder="••••••••••" autocomplete="current-password" />
                    </div>

                    <div class="flex items-center justify-between gap-2 text-xs sm:text-sm">
                        <label class="flex items-center gap-2 text-slate-600">
                            <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#173d5d] focus:ring-[#173d5d]">
                            تذكرني
                        </label>
                        <a href="<?= htmlspecialchars(url('/forgot-password')) ?>" class="font-bold text-[#173d5d] hover:text-[#0f273a]">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="nav-btn w-full rounded-[14px] bg-[linear-gradient(135deg,#0f273a,#173d5d)] px-6 py-3.5 text-sm font-black text-white shadow-[0_14px_24px_rgba(15,39,58,0.28)] transition hover:-translate-y-0.5 hover:shadow-[0_18px_36px_rgba(15,39,58,0.35)]">
                        تسجيل الدخول
                    </button>
                </form>

                <div class="mt-7 border-t border-slate-200 pt-5 text-center text-sm text-slate-600">
                    ليس لديك حساب؟
                    <a href="<?= htmlspecialchars(url('/register')) ?>" class="mr-1 font-black text-[#0f273a] transition hover:text-[#173d5d]">إنشاء حساب جديد</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
