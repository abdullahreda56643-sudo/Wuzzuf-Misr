<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

 $adminUser = current_user();
 $adminName = trim((string) (($adminUser['first_name'] ?? '') . ' ' . ($adminUser['last_name'] ?? '')));
if ($adminName === '') {
    $adminName = 'مدير النظام';
}
 $currentPath = $_SERVER['REQUEST_URI'] ?? '/admin';
 $currentPath = parse_url($currentPath, PHP_URL_PATH) ?: '/admin';
 $siteName = config('site_name', 'بوابة وظائف مصر | Wazaef Misr');
 $flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
 $flashType = $flash['type'] ?? 'success';
 $flashMessage = $flash['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="theme-color" content="#071b2f">
    <title><?= htmlspecialchars($siteName) ?> | لوحة الإدارة</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['IBM Plex Sans Arabic', 'sans-serif'],
                        display: ['El Messiri', 'sans-serif']
                    },
                    colors: {
                        navy: { 900: '#071b2f', 800: '#0d2340', 700: '#12355d', 600: '#1a4278', 500: '#254f8c' },
                        gold: { 50: '#fbf3e1', 100: '#f5e4bc', 200: '#ecce87', 300: '#e2b85a', 400: '#d9ae57', 500: '#c79734', 600: '#b47a18', 700: '#966a16', 800: '#7a5411', 900: '#5f400c' },
                        brand: { 50: '#fbf3e1', 100: '#f5e4bc', 200: '#ecce87', 300: '#e2b85a', 400: '#d9ae57', 500: '#c79734', 600: '#b47a18', 700: '#966a16', 800: '#7a5411', 900: '#5f400c' },
                    },
                    boxShadow: {
                        soft: '0 8px 24px -12px rgba(7, 27, 47, 0.12)',
                        lift: '0 20px 40px -16px rgba(7, 27, 47, 0.22)',
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    <style>
        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
        }
        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background: #f4f7fb;
            color: #0f172a;
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior-y: none;
        }
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'El Messiri', sans-serif;
        }
        .admin-shell {
            min-height: 100vh;
            min-height: 100dvh;
            background:
                linear-gradient(180deg, rgba(7,27,47,0.98) 0%, rgba(7,27,47,0.96) 180px, #f4f7fb 180px, #f4f7fb 100%);
        }
        @media (min-width: 1024px) {
            .admin-shell {
                background:
                    linear-gradient(180deg, rgba(7,27,47,0.98) 0%, rgba(7,27,47,0.96) 220px, #f4f7fb 220px, #f4f7fb 100%);
            }
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }
        .admin-table th, .admin-table td { border-color: #e2e8f0; }
        .gold-btn {
            background: linear-gradient(135deg, #e6c068 0%, #b47a18 100%);
            box-shadow: 0 10px 20px -5px rgba(180, 122, 24, 0.35);
            transition: all 0.25s ease;
        }
        .gold-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px -8px rgba(180, 122, 24, 0.42);
        }
        .gold-btn:active {
            transform: translateY(0);
        }
        .nav-link.active {
            background: rgba(199, 151, 52, 0.12);
            color: #e2b85a;
            border-color: rgba(199, 151, 52, 0.2);
        }
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.06);
        }
        .nav-link:active {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Flash animation */
        @keyframes flashSlide {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .flash-anim {
            animation: flashSlide 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mobile sidebar scroll */
        .mobile-sidebar-scroll {
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }

        /* Touch target minimum */
        @media (max-width: 768px) {
            button, a {
                -webkit-tap-highlight-color: transparent;
            }
        }

        /* Safe area */
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
    </style>
</head>
<body class="bg-slate-100">
<div class="admin-shell">
    <?php if ($flashMessage !== ''): ?>
        <div class="mx-auto max-w-[1600px] px-3 pt-3 sm:px-6 sm:pt-4 lg:px-8">
            <div class="flash-anim flex items-center justify-between gap-3 rounded-xl border px-3 py-2.5 text-xs font-medium shadow-sm sm:rounded-2xl sm:px-4 sm:py-3 sm:text-sm <?= $flashType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' ?>">
                <div class="flex items-center gap-2">
                    <i class="bx <?= $flashType === 'success' ? 'bx-check-circle text-emerald-500' : 'bx-x-circle text-red-500' ?> text-base sm:text-lg flex-shrink-0"></i>
                    <span class="leading-snug"><?= htmlspecialchars($flashMessage) ?></span>
                </div>
                <button type="button" onclick="this.closest('.flash-anim').style.display='none'" class="flex-shrink-0 rounded-lg p-1 transition hover:bg-black/5" aria-label="إغلاق">
                    <i class="bx bx-x text-base"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <header class="safe-top sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
        <div class="mx-auto flex h-14 min-h-14 max-w-[1600px] items-center justify-between gap-1.5 px-2.5 sm:h-16 sm:min-h-16 sm:gap-2 sm:px-4 md:gap-3 md:px-6 lg:h-20 lg:min-h-20 lg:px-8">
            <!-- Left Section: Menu Button + Logo -->
            <div class="flex min-w-0 flex-shrink-0 items-center gap-2 sm:gap-3">
                <button type="button" class="inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-700 transition active:scale-95 active:bg-slate-200 sm:h-10 sm:w-10 lg:hidden" aria-label="فتح القائمة" data-open-mobile-menu>
                    <i class="bx bx-menu text-lg sm:text-xl"></i>
                </button>

                <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gold-200 bg-gold-50 shadow-sm sm:h-11 sm:w-11 lg:h-12 lg:w-12">
                        <img src="<?= htmlspecialchars(asset('logo.png')) ?>" alt="logo" class="h-7 w-7 object-contain sm:h-9 sm:w-9 lg:h-10 lg:w-10">
                    </div>
                    <div class="min-w-0 hidden xs:block sm:block">
                        <p class="truncate text-[8px] font-bold tracking-wider text-gold-600 sm:text-[10px] lg:text-xs">ADMIN</p>
                        <h1 class="truncate text-xs font-black text-navy-900 sm:text-sm md:text-base lg:text-lg">الإدارة</h1>
                    </div>
                </div>
            </div>

            <!-- Center Section: Site Name (Hidden on Mobile) -->
            <div class="hidden flex-1 items-center justify-center px-2 md:flex">
                <div class="truncate rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-center text-xs font-bold text-slate-700 md:px-4 md:py-2 md:text-sm lg:px-5 lg:py-2.5 lg:text-base">
                    <?= htmlspecialchars(mb_substr($siteName, 0, 30, 'UTF-8')) ?>
                </div>
            </div>

            <!-- Right Section: User Menu -->
            <div class="flex flex-shrink-0 items-center gap-1.5 sm:gap-2 md:gap-3">
                <!-- User name - hidden on very small screens -->
                <div class="hidden rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-right sm:block md:px-3 md:py-2">
                    <p class="text-[8px] font-bold tracking-wider text-slate-500 sm:text-[10px]">مرحباً</p>
                    <p class="truncate text-xs font-black text-slate-800 sm:text-sm md:max-w-[160px]"><?= htmlspecialchars(mb_substr($adminName, 0, 20, 'UTF-8')) ?></p>
                </div>

                <!-- Avatar -->
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-gold-300 via-gold-400 to-gold-500 text-xs font-black text-navy-900 shadow-lg shadow-gold-500/20 sm:h-11 sm:w-11 lg:h-12 lg:w-12 lg:rounded-xl lg:text-base">
                    <?= strtoupper(mb_substr($adminName, 0, 1, 'UTF-8')) ?>
                </div>

               
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('admin-mobile-overlay');
            const sidebar = document.getElementById('admin-mobile-sidebar');
            const openButton = document.querySelector('[data-open-mobile-menu]');
            const closeButtons = document.querySelectorAll('[data-close-mobile-menu]');

            function setMobileMenu(open) {
                if (!overlay || !sidebar) return;
                // Sidebar is right-positioned, so translate-x-full hides it (off-screen right)
                sidebar.classList.toggle('translate-x-full', !open);
                sidebar.classList.toggle('translate-x-0', open);
                overlay.classList.toggle('opacity-0', !open);
                overlay.classList.toggle('pointer-events-none', !open);
                document.body.classList.toggle('overflow-hidden', open);
            }

            if (openButton) {
                openButton.addEventListener('click', function () {
                    setMobileMenu(true);
                });
            }

            closeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setMobileMenu(false);
                });
            });

            if (overlay) {
                overlay.addEventListener('click', function () {
                    setMobileMenu(false);
                });
            }

            // Close on Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('translate-x-full')) {
                    setMobileMenu(false);
                }
            });
        });
    </script>