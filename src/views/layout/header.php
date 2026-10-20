<?php
Session::start();
$siteName = config('site_name', 'بوابة وظائف مصر | Wazaef Misr');
$currentUser = current_user();
if (($currentUser['type'] ?? 'job_seeker') === 'admin') {
    $dashboardUrl = '/admin';
} elseif (($currentUser['type'] ?? 'job_seeker') === 'employer') {
    $dashboardUrl = '/employer/dashboard';
} else {
    $dashboardUrl = '/dashboard';
}
$socialLinks = config('social_links', [
    'facebook' => '#',
    'twitter' => '#',
    'linkedin' => '#',
    'instagram' => '#',
    'youtube' => '#',
    'whatsapp' => '#',
]);

// روابط القائمة
$navItems = [
    ['label' => 'الرئيسية', 'url' => '/'],
    ['label' => 'الوظائف', 'url' => '/jobs'],
    ['label' => 'الشركات', 'url' => '/companies'],
    ['label' => 'الباحثون عن عمل', 'url' => '/job-seekers'],
    ['label' => 'نصائح مهنية', 'url' => '/posts'],
    ['label' => 'اتصل بنا', 'url' => '/contact'],
];

// أيقونات SVG للقائمة الجانبية (للموبايل)
$navIcons = [
    '/' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
    '/jobs' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
    '/companies' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
    '/job-seekers' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm12 10v-2a4 4 0 00-3-3.87m-1-8.13a4 4 0 010 7.75"/></svg>',
    '/cv-services' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>',
    '/posts' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
    '/contact' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
];

// تحديد الرابط الحالي مع مراعاة مسار التطبيق داخل المجلد الفرعي
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
$currentUrl = explode('?', $currentUrl, 2)[0];
$siteBasePath = parse_url(config('site_url', 'http://localhost/works/project-root'), PHP_URL_PATH);
$siteBasePath = rtrim($siteBasePath ?? '', '/');
if ($siteBasePath !== '' && strpos($currentUrl, $siteBasePath) === 0) {
    $currentUrl = substr($currentUrl, strlen($siteBasePath));
}
$currentUrl = '/' . trim($currentUrl, '/');
if ($currentUrl === '//') {
    $currentUrl = '/';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars(config('site_description', 'بوابة وظائف مصر')) ?>">
    <title><?= htmlspecialchars($siteName) ?></title>

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1439681675419375"
        crossorigin="anonymous"></script>

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
                        navy:   { 900: '#071b2f', 800: '#0d2340', 700: '#12355d', 600: '#1a4278', 500: '#254f8c' },
                        gold:   { 50: '#fbf3e1', 100: '#f5e4bc', 200: '#ecce87', 300: '#e2b85a', 400: '#d9ae57', 500: '#c79734', 600: '#b47a18', 700: '#966a16', 800: '#7a5411', 900: '#5f400c' },
                        /* "brand" = the site's single gold accent family (buttons, links, highlights) */
                        brand:  { 50: '#fbf3e1', 100: '#f5e4bc', 200: '#ecce87', 300: '#e2b85a', 400: '#d9ae57', 500: '#c79734', 600: '#b47a18', 700: '#966a16', 800: '#7a5411', 900: '#5f400c' },
                        /* "accent" = warm secondary highlight used sparingly for glows / live dots */
                        accent: { 50: '#fff8ec', 100: '#ffedc7', 200: '#ffd98a', 300: '#f9c35b', 400: '#f2ac3d', 500: '#e69323', 600: '#c9761a' },
                    },
                    boxShadow: {
                        soft: '0 8px 24px -12px rgba(7, 27, 47, 0.12)',
                        lift: '0 20px 40px -16px rgba(7, 27, 47, 0.22)',
                    },
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        html, body { font-family: 'IBM Plex Sans Arabic', sans-serif; background: #f8fafc; }
        body { font-size: 15px; line-height: 1.8; color: #0f172a; }
        p, li, span, a, label, input, textarea, select, button, small, .text-sm, .text-base, .text-lg, .text-xl { font-size: inherit; }
        h1, h2, h3, h4, h5, h6, .font-display, .display-font { font-family: 'El Messiri', sans-serif; font-weight: 900; line-height: 1.3; letter-spacing: -0.02em; }
        h1 { font-size: clamp(2rem, 2.6vw, 3rem); }
        h2 { font-size: clamp(1.55rem, 2vw, 2.3rem); }
        h3 { font-size: clamp(1.3rem, 1.8vw, 1.75rem); }
        h4 { font-size: clamp(1.1rem, 1.5vw, 1.35rem); }
        h5 { font-size: 1rem; }
        h6 { font-size: 0.92rem; }
        p, .text-body, .text-sm, .text-xs { font-weight: 700; }
        p, .text-body { font-size: 0.96rem; line-height: 1.8; }
        .text-sm { font-size: 0.95rem !important; }
        .text-xs { font-size: 0.82rem !important; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
        }

        /* ============ الهيدر ============ */
        .nav-underline-link {
            position: relative;
            transition: color 0.25s ease;
        }
        .nav-underline-link::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: -6px;
            width: 100%;
            height: 3px;
            border-radius: 2px;
            background: #d8ae57;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-underline-link:hover::after { transform: scaleX(1); }
        .nav-underline-link.active {
            color: #071b2f;
            font-weight: 800;
        }
        .nav-underline-link.active::after { transform: scaleX(1); }

        .gold-btn {
            background: linear-gradient(135deg, #e6c068 0%, #b47a18 100%);
            box-shadow: 0 10px 20px -5px rgba(180, 122, 24, 0.4);
            transition: all 0.3s ease;
        }
        .gold-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(180, 122, 24, 0.5);
        }

        .header-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -15px rgba(7, 27, 47, 0.12);
        }

        .social-icon-link { transition: all 0.3s ease; }

        /* ============ الهيرو ============ */
        .hero-section {
            position: relative;
            overflow: hidden;
            margin-top: -24px;
            padding: 0 0 72px;
            background:
                radial-gradient(120% 100% at 15% 0%, #12355d 0%, #071b2f 45%, #050f1c 100%);
        }
        .hero-bg-image {
            position: absolute;
            inset: auto 0 0 0;
            height: 62%;
            z-index: 0;
            opacity: 0.35;
            pointer-events: none;
            mix-blend-mode: screen;
        }
        .hero-bg-image svg { width: 100%; height: 100%; display: block; }
        .hero-overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(5,15,28,0.55) 0%, rgba(5,15,28,0.35) 35%, rgba(5,15,28,0.92) 100%);
        }
        .hero-grid-pattern {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            opacity: 0.05;
            background-image:
                linear-gradient(#fff 1px, transparent 1px),
                linear-gradient(90deg, #fff 1px, transparent 1px);
            background-size: 46px 46px;
        }
        .hero-glow-1 {
            position: absolute;
            top: -140px;
            right: -100px;
            width: 420px;
            height: 420px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(216,174,87,0.35) 0%, rgba(216,174,87,0) 70%);
            filter: blur(10px);
            z-index: 1;
            pointer-events: none;
        }
        .hero-glow-2 {
            position: absolute;
            bottom: -160px;
            left: -120px;
            width: 460px;
            height: 460px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(37,79,140,0.45) 0%, rgba(37,79,140,0) 70%);
            filter: blur(10px);
            z-index: 1;
            pointer-events: none;
        }

        .fade-up { animation: fadeUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .fade-up-delay-1 { animation-delay: 0.08s; }
        .fade-up-delay-2 { animation-delay: 0.16s; }
        .fade-up-delay-3 { animation-delay: 0.24s; }
        .fade-up-delay-4 { animation-delay: 0.32s; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ============ بار الفلاتر ============ */
        .filter-bar {
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 24px 48px -20px rgba(5, 15, 28, 0.45);
            border: 1px solid rgba(255,255,255,0.6);
        }
        .filter-field {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1 1 0%;
            min-width: 0;
            padding: 12px 16px;
            border-inline-end: 1px solid #eef1f5;
        }
        @media (max-width: 1023px) {
            .filter-field { border-inline-end: none; border-bottom: 1px solid #eef1f5; }
        }
        .filter-field:last-of-type { border-inline-end: none; }
        .field-icon { color: #94a3b8; flex-shrink: 0; }
        .filter-field input,
        .filter-field select {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 13.5px;
            font-weight: 600;
            color: #0f2440;
            appearance: none;
        }
        .filter-field input::placeholder { color: #94a3b8; font-weight: 500; }
        .search-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 30px;
            border-radius: 16px;
            color: #fff;
            font-weight: 800;
            font-size: 14px;
            background: linear-gradient(135deg, #e6c068 0%, #b47a18 100%);
            box-shadow: 0 14px 24px -8px rgba(180, 122, 24, 0.55);
            transition: all 0.25s ease;
        }
        .search-btn:hover { transform: translateY(-2px); box-shadow: 0 18px 28px -8px rgba(180, 122, 24, 0.65); }

        /* ============ بطاقات فئات الوظائف ============ */
        .category-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
            padding: 22px 14px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #eef1f5;
            box-shadow: 0 8px 24px -16px rgba(7,27,47,0.15);
            transition: all 0.25s ease;
        }
        .category-card:hover {
            transform: translateY(-4px);
            border-color: rgba(216,174,87,0.35);
            box-shadow: 0 18px 32px -16px rgba(7,27,47,0.22);
        }
        .category-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        /* ============ بطاقات الوظائف ============ */
        .job-row {
            background: #ffffff;
            border: 1px solid #eef1f5;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 6px 18px -14px rgba(7,27,47,0.15);
            transition: all 0.25s ease;
        }
        .job-row:hover {
            border-color: rgba(216,174,87,0.4);
            box-shadow: 0 16px 32px -16px rgba(7,27,47,0.22);
            transform: translateY(-2px);
        }
        .job-logo {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 900;
            font-size: 18px;
            box-shadow: 0 8px 16px -8px rgba(7,27,47,0.35);
        }
        .job-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 9px;
            font-size: 11.5px;
            font-weight: 700;
        }

        /* ============ الشريط الجانبي ============ */
        .sidebar-card {
            background: #ffffff;
            border: 1px solid #eef1f5;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 24px -18px rgba(7,27,47,0.15);
        }
        .sidebar-card-header {
            padding: 16px 18px;
            border-bottom: 1px solid #f1f5f9;
        }
        .cat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            transition: all 0.2s ease;
        }
        .cat-item:hover { background: #fbf3e1; color: #7a5411; }
        .cat-count {
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            background: #f1f5f9;
            border-radius: 9999px;
            padding: 2px 9px;
        }
        .stat-cell { padding: 18px 12px; text-align: center; }
        .stat-value { font-size: 20px; font-weight: 900; color: #071b2f; }
        .company-mini {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 10px;
            border-radius: 14px;
            transition: background 0.2s ease;
        }
        .company-mini:hover { background: #f8fafc; }
        .company-mini-logo {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 14px;
        }

        /* ============ فلتر سريع بالشريط الجانبي ============ */
        .quick-filter-field {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 11px 14px;
            transition: border-color 0.2s ease;
        }
        .quick-filter-field:focus-within { border-color: #d9ae57; }
        .quick-filter-field select {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 13px;
            font-weight: 700;
            color: #0f2440;
            appearance: none;
        }

        body {
            min-height: 100vh;
            color: #12233d;
            background:
                radial-gradient(circle at 100% 0%, rgba(37, 79, 140, 0.11), transparent 28rem),
                linear-gradient(180deg, #f8fafc 0%, #f3f6fa 100%);
        }
        main { margin-top: 0; }
        .site-header {
            background: rgba(255, 255, 255, 0.84);
            border-bottom: none;
            box-shadow: 0 12px 30px -24px rgba(7, 27, 47, 0.5);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }
        .site-header.scrolled {
            box-shadow: 0 18px 34px -22px rgba(7, 27, 47, 0.45);
            border-bottom: 1px solid rgba(203, 213, 225, 0.72);
        }
        .site-nav {
            padding: 5px;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            background: rgba(248, 250, 252, 0.82);
        }
        .site-nav .nav-underline-link { border-radius: 9999px; padding: 8px 14px; }
        .site-nav .nav-underline-link::after { display: none; }
        .site-nav .nav-underline-link.active { color: #fff; background: #071b2f; box-shadow: 0 7px 14px -9px rgba(7, 27, 47, 0.7); }
        .site-nav .nav-underline-link:hover:not(.active) { background: #fff; color: #071b2f; }

        .mobile-menu-toggle {
            position: relative;
            overflow: hidden;
            width: 42px;
            height: 42px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 16px -12px rgba(15, 23, 42, 0.28);
            transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.2s ease;
        }
        .mobile-menu-toggle:hover {
            border-color: rgba(216, 174, 87, 0.6);
            box-shadow: 0 14px 22px -14px rgba(180, 122, 24, 0.28);
        }
        .mobile-menu-toggle:active {
            transform: scale(0.97);
        }
        .mobile-menu-toggle-box {
            position: relative;
            display: inline-block;
            width: 18px;
            height: 14px;
        }
        .mobile-menu-toggle-line {
            position: absolute;
            left: 0;
            display: block;
            width: 100%;
            height: 2.5px;
            border-radius: 999px;
            background: #0f172a;
            transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.22s ease, top 0.35s cubic-bezier(0.22, 1, 0.36, 1), background 0.25s ease;
            transform-origin: center;
        }
        .mobile-menu-toggle-line:nth-child(1) { top: 0; }
        .mobile-menu-toggle-line:nth-child(2) { top: 6px; }
        .mobile-menu-toggle-line:nth-child(3) { top: 12px; }
        .mobile-menu-toggle.is-open .mobile-menu-toggle-line:nth-child(1) {
            top: 6px;
            transform: rotate(45deg);
            background: #111827;
        }
        .mobile-menu-toggle.is-open .mobile-menu-toggle-line:nth-child(2) {
            opacity: 0;
            transform: scaleX(0.15);
        }
        .mobile-menu-toggle.is-open .mobile-menu-toggle-line:nth-child(3) {
            top: 6px;
            transform: rotate(-45deg);
            background: #111827;
        }

        .mobile-drawer-panel {
            background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.99) 100%);
            border-inline-start: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: -18px 0 45px -18px rgba(15, 23, 42, 0.28);
            transform: translate3d(0, 0, 0);
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            will-change: transform, opacity;
        }
        .mobile-menu-link {
            position: relative;
            overflow: hidden;
            transition: transform 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
            will-change: transform;
        }
        .mobile-menu-link:hover {
            transform: translateX(-2px);
            box-shadow: 0 10px 18px -14px rgba(15, 23, 42, 0.22);
        }
        .mobile-menu-link.active {
            background: linear-gradient(135deg, #071b2f 0%, #12355d 100%);
            color: #fff;
            box-shadow: 0 18px 26px -18px rgba(7, 27, 47, 0.48);
        }
        .mobile-menu-link:not(.active):hover {
            background: rgba(248, 250, 252, 0.95);
        }
        .mobile-menu-link .link-icon {
            transition: transform 0.25s ease, background 0.25s ease;
        }
        .mobile-menu-link:hover .link-icon {
            transform: scale(1.06);
        }
        .mobile-menu-link.active .link-icon {
            background: rgba(216, 174, 87, 0.28);
            color: #f8d98d;
        }

        .hero-section { padding-top: 0; background: radial-gradient(110% 120% at 94% -10%, #266093 0%, #0d2b4c 37%, #061827 72%, #04111d 100%); }
        .filter-bar { border-color: rgba(255, 255, 255, 0.45); box-shadow: 0 28px 56px -28px rgba(1, 17, 30, 0.72); }
        .category-card, .job-row, .sidebar-card { border-color: rgba(203, 213, 225, 0.7); }
        .category-card { box-shadow: 0 12px 26px -22px rgba(7, 27, 47, 0.36); }
        .category-card:hover { box-shadow: 0 22px 32px -22px rgba(7, 27, 47, 0.32); }
        .job-row { box-shadow: 0 12px 28px -24px rgba(7, 27, 47, 0.32); }
        .sidebar-card { box-shadow: 0 16px 30px -26px rgba(7, 27, 47, 0.34); }
        .platform-ribbon {
            margin-top: -32px;
            position: relative;
            z-index: 20;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 20px 46px -28px rgba(7, 27, 47, 0.36);
            backdrop-filter: blur(14px);
        }
        .platform-ribbon-item + .platform-ribbon-item { border-inline-start: 1px solid #e7edf4; }
        .section-kicker { color: #b47a18; letter-spacing: 0.08em; }
        .site-footer { background: #061827; }
        .site-footer-link { color: #94a3b8; transition: color 0.2s ease; }
        .site-footer-link:hover { color: #e6c068; }
        @media (max-width: 767px) {
            .hero-section { padding-top: 0; padding-bottom: 108px; }
            .platform-ribbon { margin-top: -22px; }
            .platform-ribbon-item + .platform-ribbon-item { border-inline-start: 0; border-top: 1px solid #e7edf4; }
        }
    </style>
</head>
<body class="text-navy-900">

    <div x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 10)">

        <!-- الشريط العلوي (Topbar) -->
        <div class="bg-navy-900 text-gray-300 text-xs hidden md:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-9 flex items-center justify-between">

                <!-- يمين: تسجيل الدخول / إنشاء حساب -->
                <div class="flex items-center gap-3">
                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <?php if ($currentUser): ?>
                        <a href="<?= htmlspecialchars(url($dashboardUrl)) ?>" class="hover:text-gold-500 transition-colors font-semibold">لوحة التحكم</a>
                        <span class="opacity-30">|</span>
                        <a href="<?= htmlspecialchars(url('/logout')) ?>" class="hover:text-gold-500 transition-colors">تسجيل الخروج</a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="hover:text-gold-500 transition-colors font-semibold">تسجيل الدخول</a>
                        <span class="opacity-30">|</span>
                        <a href="<?= htmlspecialchars(url('/register')) ?>" class="hover:text-gold-500 transition-colors">إنشاء حساب</a>
                    <?php endif; ?>
                </div>

                <!-- يسار: تواصل معنا + السوشيال ميديا -->
                <div class="flex items-center gap-3">
                    <span class="opacity-60">تواصل معنا:</span>
                    <a href="<?= htmlspecialchars($socialLinks['linkedin'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="لينكدإن" class="social-icon-link w-6 h-6 flex items-center justify-center bg-white/5 rounded-md hover:bg-gold-500 hover:text-navy-900">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14zM8.34 18.34V10.1H5.67v8.24h2.67zM7 8.9a1.55 1.55 0 100-3.1 1.55 1.55 0 000 3.1zM18.34 18.34v-4.4c0-2.36-1.26-3.46-2.94-3.46a2.53 2.53 0 00-2.29 1.26h-.03V10.1H10.4c.04.75 0 8.24 0 8.24h2.68v-4.6c0-.25.02-.5.1-.68.2-.5.66-1.03 1.44-1.03 1.02 0 1.43.78 1.43 1.92v4.4h2.29z"/></svg>
                    </a>
                    <a href="<?= htmlspecialchars($socialLinks['instagram'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="انستغرام" class="social-icon-link w-6 h-6 flex items-center justify-center bg-white/5 rounded-md hover:bg-gold-500 hover:text-navy-900">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c2.72 0 3.06.01 4.12.06 1.06.05 1.79.22 2.43.47.66.26 1.22.6 1.77 1.15.5.5.9 1.11 1.15 1.77.25.64.42 1.37.47 2.43.05 1.06.06 1.4.06 4.12s-.01 3.06-.06 4.12c-.05 1.06-.22 1.79-.47 2.43a4.9 4.9 0 01-1.15 1.77 4.9 4.9 0 01-1.77 1.15c-.64.25-1.37.42-2.43.47-1.06.05-1.4.06-4.12.06s-3.06-.01-4.12-.06c-1.06-.05-1.79-.22-2.43-.47a4.9 4.9 0 01-1.77-1.15 4.9 4.9 0 01-1.15-1.77c-.25-.64-.42-1.37-.47-2.43C2.01 15.06 2 14.72 2 12s.01-3.06.06-4.12c.05-1.06.22-1.79.47-2.43.26-.66.6-1.22 1.15-1.77.5-.5 1.11-.9 1.77-1.15.64-.25 1.37-.42 2.43-.47C8.94 2.01 9.28 2 12 2zm0 5a5 5 0 100 10 5 5 0 000-10zm0 8.2a3.2 3.2 0 110-6.4 3.2 3.2 0 010 6.4zm5.2-8.4a1.2 1.2 0 100-2.4 1.2 1.2 0 000 2.4z"/></svg>
                    </a>
                    <a href="<?= htmlspecialchars($socialLinks['facebook'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="فيسبوك" class="social-icon-link w-6 h-6 flex items-center justify-center bg-white/5 rounded-md hover:bg-gold-500 hover:text-navy-900">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.04C6.5 2.04 2 6.53 2 12.06c0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02Z"/></svg>
                    </a>
                    <a href="<?= htmlspecialchars($socialLinks['youtube'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="يوتيوب" class="social-icon-link w-6 h-6 flex items-center justify-center bg-white/5 rounded-md hover:bg-gold-500 hover:text-navy-900">
                        <i class="bx bxl-youtube text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- الهيدر الرئيسي -->
        <header class="site-header sticky top-0 z-40 transition-all duration-500" :class="{ 'scrolled': scrolled }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[76px] lg:h-[86px]">

                    <!-- الشعار -->
                    <a href="<?= htmlspecialchars(url('/')) ?>" class="flex-shrink-0 flex items-center gap-2 sm:gap-3">
                        <img src="<?= htmlspecialchars(asset('logo.png')) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-16 lg:h-[82px] w-auto max-w-[245px] object-contain transition-transform duration-300 hover:scale-105">
                        <span class="flex max-w-[90px] flex-col border-r border-gold-400/45 pr-2 text-[11px] font-bold leading-5 text-slate-500 xl:hidden"><span class="text-navy-800">بوابتك لمسار</span><span class="text-gold-700">مهني أفضل</span></span>
                        <span class="hidden max-w-[90px] xl:flex flex-col border-r border-gold-400/45 pr-2 text-[11px] font-bold leading-5"><span class="text-navy-800">بوابتك لمسار</span><span class="text-gold-700">مهني أفضل</span></span>
                    </a>

                    <!-- قائمة سطح المكتب (خط سفلي) -->
                    <nav class="site-nav hidden lg:flex items-center gap-1 absolute left-1/2 transform -translate-x-1/2">
                        <?php foreach ($navItems as $item):
                            $isActive = ($currentUrl === $item['url']);
                        ?>
                            <a href="<?= htmlspecialchars(url($item['url'])) ?>"
                               class="nav-underline-link text-[13px] font-bold <?= $isActive ? 'active' : 'text-slate-500 hover:text-navy-800' ?>">
                                <?= $item['label'] ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <!-- زر إضافة وظيفة -->
                    <div class="hidden lg:flex items-center gap-3">
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="group inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-3.5 py-2 text-[13px] font-bold text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-gold-300 hover:bg-gold-50 hover:text-navy-900">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-slate-100 text-slate-700 transition-colors group-hover:bg-gold-100 group-hover:text-gold-700">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                    <path d="M10 17l5-5-5-5"></path>
                                    <path d="M15 12H3"></path>
                                </svg>
                            </span>
                            <span>دخول</span>
                        </a>
                        <a href="<?= htmlspecialchars(url('/employer/jobs/create')) ?>" class="gold-btn group inline-flex items-center gap-2 pl-5 pr-4 py-2.5 rounded-full text-[13px] font-bold text-white">
                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-white/25 text-base leading-none">+</span>
                            <span>أضف وظيفة</span>
                        </a>
                    </div>

                    <!-- زر الموبايل (Hamburger) -->
                    <button
                        @click="open = !open"
                        :aria-expanded="open"
                        class="mobile-menu-toggle lg:hidden relative inline-flex h-12 w-12 items-center justify-center rounded-2xl text-navy-900 transition-all duration-300"
                        :class="{ 'is-open': open }"
                        aria-label="فتح القائمة الجانبية"
                    >
                        <span class="mobile-menu-toggle-box">
                            <span class="mobile-menu-toggle-line"></span>
                            <span class="mobile-menu-toggle-line"></span>
                            <span class="mobile-menu-toggle-line"></span>
                        </span>
                    </button>

                </div>
            </div>
        </header>

        <!-- القائمة الجانبية للموبايل (Drawer) -->
        <div x-show="open" class="fixed inset-0 z-50 lg:hidden" x-cloak>
            <div x-show="open"
                 x-transition:enter="transition-opacity ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-out duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="open = false"
                 class="absolute inset-0 bg-slate-950/45">
            </div>

            <div x-show="open"
                 x-transition:enter="transition-transform ease-out duration-200"
                 x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition-transform ease-out duration-200"
                 x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                 class="mobile-drawer-panel absolute top-2 bottom-2 right-2 w-[88%] max-w-sm rounded-[28px] flex flex-col overflow-hidden"
            >

                <div class="flex items-center justify-between p-5 border-b border-slate-200/80 bg-white/70 backdrop-blur-sm">
                    <img src="<?= htmlspecialchars(asset('logo.png')) ?>" alt="Logo" class="h-14 w-auto max-w-[180px] object-contain">
                    <button @click="open = false" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto p-4 space-y-2">
                    <?php foreach ($navItems as $item):
                        $isActive = ($currentUrl === $item['url']);
                        $icon = $navIcons[$item['url']] ?? '';
                    ?>
                        <a href="<?= htmlspecialchars(url($item['url'])) ?>" @click="open = false"
                           class="mobile-menu-link flex items-center gap-4 p-3.5 rounded-2xl <?= $isActive ? 'active' : 'text-slate-700' ?>">
                            <span class="link-icon flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center <?= $isActive ? 'bg-gold-500/20 text-gold-300' : 'bg-slate-100 text-slate-500' ?>">
                                <?= $icon ?>
                            </span>
                            <span class="font-bold text-base"><?= $item['label'] ?></span>
                            <?php if ($isActive): ?>
                            <span class="mr-auto">
                                <svg class="w-5 h-5 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="p-4 border-t border-slate-200/80 bg-white/70 backdrop-blur-sm space-y-3">
                    <a href="<?= htmlspecialchars(url('/employer/jobs/create')) ?>" class="flex items-center justify-center gap-2 w-full p-4 rounded-2xl font-bold text-white gold-btn shadow-lg shadow-gold-500/20">
                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-white/25 text-base leading-none">+</span>
                        أضف وظيفة الآن
                    </a>
                    <?php if ($currentUser): ?>
                        <a href="<?= htmlspecialchars(url($dashboardUrl)) ?>" class="flex items-center justify-center gap-2 w-full p-4 rounded-2xl font-bold text-navy-800 border border-slate-200 bg-white hover:border-slate-300 transition-colors">
                            لوحة التحكم
                        </a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="flex items-center justify-center gap-2 w-full p-4 rounded-2xl font-bold text-navy-800 border border-slate-200 bg-white hover:border-slate-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            تسجيل الدخول
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <?php
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    $flashType = $flash['type'] ?? 'success';
    $flashMessage = $flash['message'] ?? '';
    $toastColors = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700 shadow-emerald-500/10',
        'error' => 'border-red-200 bg-red-50 text-red-700 shadow-red-500/10',
        'info' => 'border-sky-200 bg-sky-50 text-sky-700 shadow-sky-500/10',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-700 shadow-amber-500/10',
    ];
    $toastIcon = [
        'success' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        'error' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0v1a1 1 0 11-2 0v-1zm0-8a1 1 0 112 0v5a1 1 0 11-2 0V5z" clip-rule="evenodd"/></svg>',
        'info' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 112 0v5a1 1 0 11-2 0V6zm1 9a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>',
        'warning' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.9c.75 1.333-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.668-1.742-3.001l5.58-9.9zM10 6a1 1 0 00-1 1v4a1 1 0 102 0V7a1 1 0 00-1-1zm0 8a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>',
    ];
    ?>
    <?php if ($flashMessage !== ''): ?>
        <div aria-live="polite" class="pointer-events-none fixed inset-x-0 top-4 z-[90] flex justify-center px-4">
            <div class="pointer-events-auto flex max-w-xl items-center gap-3 rounded-2xl border bg-white/90 px-4 py-3 shadow-lg backdrop-blur-sm <?= $toastColors[$flashType] ?? $toastColors['info'] ?>">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/60">
                    <?= $toastIcon[$flashType] ?? $toastIcon['info'] ?>
                </div>
                <div class="text-sm font-bold leading-6">
                    <?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function () {
                const toast = document.currentScript && document.currentScript.previousElementSibling;
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-12px)';
                    toast.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                    setTimeout(() => toast.remove(), 350);
                }
            }, 3000);
        </script>
    <?php endif; ?>
    <main>