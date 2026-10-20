<?php
 $menu = [
    ['label' => 'لوحة التحكم', 'icon' => 'bx-grid-alt', 'url' => '/admin'],
    ['label' => 'إدارة المستخدمين', 'icon' => 'bx-user', 'url' => '/admin/users'],
    ['label' => 'طلبات توظيف الباحثين', 'icon' => 'bx-paper-plane', 'url' => '/admin/employment-requests'],
    ['label' => 'إدارة الشركات', 'icon' => 'bx-building-house', 'url' => '/admin/companies'],
    ['label' => 'إدارة الوظائف', 'icon' => 'bx-briefcase', 'url' => '/admin/jobs'],
    ['label' => 'التصنيفات', 'icon' => 'bx-category', 'url' => '/admin/categories'],
    ['label' => 'المحافظات والمدن', 'icon' => 'bx-map', 'url' => '/admin/locations'],
    ['label' => 'المقالات', 'icon' => 'bx-news', 'url' => '/admin/posts'],
    ['label' => 'الإعلانات', 'icon' => 'bx-purchase-tag-alt', 'url' => '/admin/ads'],
    ['label' => 'التقارير', 'icon' => 'bx-bar-chart-alt-2', 'url' => '/admin/reports'],
    ['label' => 'الإعدادات', 'icon' => 'bx-cog', 'url' => '/admin/settings'],
];

 $currentPath = $_SERVER['REQUEST_URI'] ?? '/admin';
 $currentPath = parse_url($currentPath, PHP_URL_PATH) ?: '/admin';
?>
<div class="mx-auto max-w-[1600px] px-2 pb-4 pt-3 sm:px-4 md:pb-6 md:pt-4 lg:px-8 lg:pb-6 lg:pt-6">
    <div class="flex gap-3 md:gap-4 lg:gap-6">
        <!-- Desktop Sidebar -->
        <aside class="hidden w-60 shrink-0 rounded-2xl border border-slate-200 bg-[#071b2f] p-3 text-white shadow-lift md:w-72 lg:block lg:p-4">
            <div class="mb-4 flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 md:mb-6 md:rounded-2xl md:px-4 md:py-3">
                <div>
                    <p class="text-[8px] font-bold tracking-wider text-gold-300 md:text-[9px] lg:text-[10px]">SYSTEM PANEL</p>
                    <h2 class="mt-1 text-sm font-black text-white md:text-base lg:text-lg">الرئيسية</h2>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gold-500/15 text-gold-300 md:h-10 md:w-10 md:rounded-xl lg:h-11 lg:w-11">
                    <i class="bx bx-shield-quarter text-lg md:text-2xl"></i>
                </div>
            </div>

            <nav class="space-y-1 md:space-y-1.5 lg:space-y-2">
                <?php foreach ($menu as $item): ?>
                    <?php
                    $active = $currentPath === $item['url'] || ($item['url'] !== '/admin' && strpos($currentPath, $item['url']) === 0);
                    $menuUrl = url($item['url']);
                    ?>
                    <a href="<?= htmlspecialchars($menuUrl) ?>" class="nav-link group flex items-center justify-between gap-2 rounded-lg border px-3 py-2.5 text-xs font-semibold transition md:gap-3 md:rounded-xl md:px-3.5 md:py-3 md:text-sm lg:py-3.5 <?= $active ? 'active border-gold-200 bg-gold-500/10 text-gold-300' : 'border-transparent text-slate-100 hover:border-white/10 hover:bg-white/5' ?>">
                        <span class="flex min-w-0 items-center gap-2 md:gap-3">
                            <i class="bx <?= htmlspecialchars($item['icon']) ?> flex-shrink-0 text-base md:text-lg lg:text-xl <?= $active ? 'text-gold-400' : 'text-slate-300 group-hover:text-white' ?>"></i>
                            <span class="truncate"><?= htmlspecialchars($item['label']) ?></span>
                        </span>
                        <i class="bx bx-chevron-left flex-shrink-0 text-sm opacity-50 transition group-hover:opacity-100 md:text-base lg:text-lg <?= $active ? 'text-gold-400 opacity-100' : '' ?>"></i>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="mt-6 rounded-lg border border-gold-500/20 bg-gradient-to-br from-gold-500/15 to-white/5 p-3 md:mt-8 md:rounded-xl md:p-4">
                <p class="text-[8px] font-bold tracking-wider text-gold-300 md:text-[9px] lg:text-[10px]">SYSTEM STATUS</p>
                <div class="mt-2 flex items-center justify-between md:mt-3">
                    <span class="text-xs font-bold text-white md:text-sm">النظام يعمل</span>
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)] md:h-3 md:w-3"></span>
                </div>
            </div>
        </aside>

        <!-- Mobile Overlay -->
        <div id="admin-mobile-overlay" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>

        <!-- Mobile Sidebar -->
        <aside id="admin-mobile-sidebar" class="mobile-sidebar-scroll fixed right-0 top-0 z-50 flex h-[100dvh] w-[280px] max-w-[85vw] translate-x-full flex-col overflow-y-auto border-l border-slate-700 bg-[#071b2f] text-white shadow-2xl transition-transform duration-300 ease-out lg:hidden">
            <!-- Mobile sidebar header -->
            <div class="safe-top sticky top-0 z-10 border-b border-white/10 bg-[#071b2f] px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gold-500/15 text-gold-300">
                            <i class="bx bx-shield-quarter text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold tracking-wider text-gold-300">NAVIGATION</p>
                            <h2 class="mt-0.5 text-sm font-black text-white">القائمة الرئيسية</h2>
                        </div>
                    </div>
                    <button type="button" data-close-mobile-menu class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white transition active:scale-90 hover:bg-white/10" aria-label="إغلاق القائمة">
                        <i class="bx bx-x text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile nav -->
            <nav class="flex-1 space-y-1 px-2.5 py-3">
                <?php foreach ($menu as $item): ?>
                    <?php
                    $active = $currentPath === $item['url'] || ($item['url'] !== '/admin' && strpos($currentPath, $item['url']) === 0);
                    $menuUrl = url($item['url']);
                    ?>
                    <a href="<?= htmlspecialchars($menuUrl) ?>" data-close-mobile-menu class="nav-link group flex items-center justify-between gap-2 rounded-xl border px-3 py-3 text-sm font-semibold transition active:scale-[0.98] <?= $active ? 'active border-gold-200 bg-gold-500/10 text-gold-300' : 'border-transparent text-slate-100 hover:border-white/10 hover:bg-white/5' ?>">
                        <span class="flex min-w-0 items-center gap-3">
                            <i class="bx <?= htmlspecialchars($item['icon']) ?> flex-shrink-0 text-lg <?= $active ? 'text-gold-400' : 'text-slate-300 group-hover:text-white' ?>"></i>
                            <span class="truncate text-[13px] sm:text-sm"><?= htmlspecialchars($item['label']) ?></span>
                        </span>
                        <i class="bx bx-chevron-left flex-shrink-0 text-base opacity-50 transition group-hover:opacity-100 <?= $active ? 'text-gold-400 opacity-100' : '' ?>"></i>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Mobile system status -->
            <div class="safe-bottom sticky bottom-0 border-t border-white/10 bg-[#071b2f] px-2.5 py-3">
                <div class="rounded-xl border border-gold-500/20 bg-gradient-to-br from-gold-500/15 to-white/5 p-3">
                    <p class="text-[9px] font-bold tracking-wider text-gold-300">SYSTEM STATUS</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs font-bold text-white">النظام يعمل</span>
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="min-w-0 flex-1">