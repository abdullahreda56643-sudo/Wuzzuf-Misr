    </main>

    <?php
        $footerSocialLinks = config('social_links', ['facebook' => '#', 'twitter' => '#', 'linkedin' => '#', 'instagram' => '#', 'whatsapp' => '#']);
        $whatsappValue = trim((string) ($footerSocialLinks['whatsapp'] ?? ''));
        if ($whatsappValue !== '' && !preg_match('/^https?:\/\//i', $whatsappValue)) {
            $whatsappValue = 'https://wa.me/' . preg_replace('/\D+/', '', $whatsappValue);
        }
        $whatsappUrl = $whatsappValue !== '' ? $whatsappValue : 'https://wa.me/201000000000';
        $whatsappMessage = (string) config('social_whatsapp_message', 'السلام عليكم، أريد معرفة المزيد');
    ?>
    <footer class="site-footer relative mt-16 overflow-hidden bg-navy-900 text-slate-300">
        <!-- ===== Background Layers ===== -->
        <div class="pointer-events-none absolute inset-0 z-0">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-45" style="background-image: url('<?= htmlspecialchars(asset('footer.png')) ?>');"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-[#061827]/85 via-[#071b2f]/80 to-[#061827]/95"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#061827]/80 via-transparent to-[#061827]/70"></div>
            <div class="absolute inset-0 shadow-[inset_0_0_200px_60px_rgba(4,10,20,0.8)]"></div>
        </div>

        <!-- ===== Footer Content ===== -->
        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.25fr]">
                <!-- Brand + Social -->
                <div>
                    <a href="<?= htmlspecialchars(url('/')) ?>" class="inline-flex items-center gap-3">
                        <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-gold-500/30 bg-white/5 shadow-lg shadow-gold-500/10 ring-2 ring-white/10 ring-offset-2 ring-offset-navy-900 transition-transform duration-300 hover:scale-105">
                            <img src="<?= htmlspecialchars(asset('logo.png')) ?>" alt="<?= htmlspecialchars($siteName ?? 'بوابة وظائف مصر | Wazaef Misr') ?>" class="h-full w-full object-cover">
                        </span>
                        <span class="text-lg font-extrabold text-white"><?= htmlspecialchars($siteName ?? 'بوابة وظائف مصر | Wazaef Misr') ?></span>
                    </a>
                    <p class="mt-5 max-w-sm text-sm leading-7 text-slate-400">منصة مهنية موثوقة تساعدك على الوصول إلى الفرصة المناسبة وبناء مستقبل مهني أقوى في مصر والمنطقة العربية.</p>

                    <!-- Social media -->
                    <div class="mt-6 flex items-center gap-3">
                        <a href="<?= htmlspecialchars($footerSocialLinks['facebook'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="group flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-slate-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.5 9.9v-7H8v-2.9h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.2c-1.2 0-1.6.8-1.6 1.6v1.9h2.7l-.4 2.9h-2.3v7A10 10 0 0 0 22 12Z"/></svg>
                        </a>
                        <a href="<?= htmlspecialchars($footerSocialLinks['linkedin'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="group flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-slate-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5A2.5 2.5 0 1 1 0 3.5a2.5 2.5 0 0 1 4.98 0ZM0 8h5v16H0V8Zm7.5 0H12v2.2h.07c.63-1.2 2.17-2.46 4.46-2.46 4.77 0 5.65 3.14 5.65 7.22V24h-5v-7.1c0-1.7-.03-3.88-2.37-3.88-2.37 0-2.73 1.85-2.73 3.76V24h-5V8Z"/></svg>
                        </a>
                        <a href="<?= htmlspecialchars($footerSocialLinks['twitter'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="X" class="group flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-slate-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7.5 8.6L23 22h-6.8l-5.3-6.9L4.8 22H1.7l8-9.2L1 2h6.9l4.8 6.3L18.9 2Zm-2.4 18h1.9L7.6 4H5.6l10.9 16Z"/></svg>
                        </a>
                        <a href="<?= htmlspecialchars($footerSocialLinks['instagram'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="group flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-slate-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.2c3.2 0 3.6 0 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.26.07 1.64.07 4.84s0 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.26.06-1.64.07-4.85.07s-3.58 0-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.2 15.58 2.2 15.2 2.2 12s0-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.2 8.8 2.2 12 2.2Zm0 1.62c-3.15 0-3.5 0-4.74.07-.9.04-1.39.2-1.71.32-.43.17-.74.37-1.06.69-.32.32-.52.63-.69 1.06-.12.32-.28.81-.32 1.71-.06 1.24-.07 1.59-.07 4.74s0 3.5.07 4.74c.04.9.2 1.39.32 1.71.17.43.37.74.69 1.06.32.32.63.52 1.06.69.32.12.81.28 1.71.32 1.24.06 1.59.07 4.74.07s3.5 0 4.74-.07c.9-.04 1.39-.2 1.71-.32.43-.17.74-.37 1.06-.69.32-.32.52-.63.69-1.06.12-.32.28-.81.32-1.71.06-1.24.07-1.59.07-4.74s0-3.5-.07-4.74c-.04-.9-.2-1.39-.32-1.71a2.85 2.85 0 0 0-.69-1.06 2.85 2.85 0 0 0-1.06-.69c-.32-.12-.81-.28-1.71-.32-1.24-.06-1.59-.07-4.74-.07Zm0 2.76a5.42 5.42 0 1 1 0 10.84 5.42 5.42 0 0 1 0-10.84Zm0 1.62a3.8 3.8 0 1 0 0 7.6 3.8 3.8 0 0 0 0-7.6Zm5.6-2.94a1.27 1.27 0 1 1 0 2.54 1.27 1.27 0 0 1 0-2.54Z"/></svg>
                        </a>
                        <a href="<?= htmlspecialchars($footerSocialLinks['youtube'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="group flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-slate-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-400">
                            <i class="bx bxl-youtube text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Explore -->
                <div>
                    <h3 class="flex items-center gap-2 text-sm font-extrabold text-white">
                        <span class="inline-block h-4 w-1 rounded-full bg-gold-500"></span>
                        استكشف
                    </h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li><a href="<?= htmlspecialchars(url('/jobs')) ?>" class="site-footer-link group inline-flex items-center gap-2"><span class="text-gold-500/60 transition group-hover:translate-x-[-2px]">◄</span>الوظائف المتاحة</a></li>
                        <li><a href="<?= htmlspecialchars(url('/companies')) ?>" class="site-footer-link group inline-flex items-center gap-2"><span class="text-gold-500/60 transition group-hover:translate-x-[-2px]">◄</span>الشركات</a></li>
                        <li><a href="<?= htmlspecialchars(url('/posts')) ?>" class="site-footer-link group inline-flex items-center gap-2"><span class="text-gold-500/60 transition group-hover:translate-x-[-2px]">◄</span>النصائح المهنية</a></li>
                    </ul>
                </div>

                <!-- For Employers -->
                <div>
                    <h3 class="flex items-center gap-2 text-sm font-extrabold text-white">
                        <span class="inline-block h-4 w-1 rounded-full bg-gold-500"></span>
                        لأصحاب الأعمال
                    </h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li><a href="<?= htmlspecialchars(url('/jobs/create')) ?>" class="site-footer-link group inline-flex items-center gap-2"><span class="text-gold-500/60 transition group-hover:translate-x-[-2px]">◄</span>نشر وظيفة</a></li>
                        <li><a href="<?= htmlspecialchars(url('/register')) ?>" class="site-footer-link group inline-flex items-center gap-2"><span class="text-gold-500/60 transition group-hover:translate-x-[-2px]">◄</span>إنشاء حساب شركة</a></li>
                        <li><a href="<?= htmlspecialchars(url('/contact')) ?>" class="site-footer-link group inline-flex items-center gap-2"><span class="text-gold-500/60 transition group-hover:translate-x-[-2px]">◄</span>تواصل معنا</a></li>
                    </ul>
                </div>

                <!-- CTA Card -->
                <div class="relative overflow-hidden rounded-2xl border border-gold-500/20 bg-gradient-to-br from-white/[0.06] to-white/[0.02] p-6 backdrop-blur-sm">
                    <div class="absolute -top-12 -left-12 h-32 w-32 rounded-full bg-gold-500/10 blur-2xl"></div>
                    <div class="relative">
                        <p class="text-base font-extrabold text-white">ابدأ رحلتك اليوم</p>
                        <p class="mt-2 text-xs leading-6 text-slate-400">أنشئ حساباً مجانياً وتلقَّ أحدث الفرص المناسبة لخبراتك وطموحك المهني.</p>
                        <a href="<?= htmlspecialchars(url('/register')) ?>" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gold-500 px-4 py-3 text-xs font-extrabold text-navy-900 shadow-lg shadow-gold-500/20 transition hover:bg-gold-400 hover:shadow-gold-500/30">
                            إنشاء حساب مجاني
                            <svg class="h-4 w-4 rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </a>
                        <p class="mt-3 text-center text-[11px] text-slate-500">بدون رسوم • إعداد في دقائق</p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="mt-12 h-px w-full bg-gradient-to-r from-transparent via-gold-500/30 to-transparent"></div>

            <!-- Bottom bar -->
            <div class="mt-6 flex flex-col gap-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <p>© <?= date('Y') ?> <?= htmlspecialchars($siteName ?? 'بوابة وظائف مصر | Wazaef Misr') ?>. جميع الحقوق محفوظة.</p>
                <div class="flex flex-wrap gap-5">
                    <span class="text-slate-500">الخصوصية</span>
                    <span class="text-slate-500">الشروط والأحكام</span>
                    <span class="text-slate-500">ملفات الارتباط</span>
                </div>
            </div>
        </div>
    </footer>
    </div>

    <!-- Floating report and WhatsApp actions -->
    <div class="fixed bottom-4 left-4 z-[120] flex flex-col-reverse items-center gap-3 sm:bottom-6 sm:left-6" dir="ltr">
        <button type="button" id="open-report-modal" aria-label="إبلاغ إدارة الموقع" title="إبلاغ إدارة الموقع" class="report-widget relative flex h-14 w-14 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-rose-500 to-red-700 text-white shadow-2xl shadow-red-500/30 transition hover:scale-110 focus:outline-none focus:ring-4 focus:ring-red-200">
            <i class="bx bx-flag text-2xl"></i>
        </button>

        <!-- WhatsApp Widget -->
        <div class="group relative z-[121]">
        <!-- Animated Pulse Background -->
        <div class="absolute inset-0 rounded-full bg-green-500 opacity-0 animate-pulse group-hover:opacity-30 scale-125"></div>
        <div class="absolute inset-0 rounded-full border-2 border-green-400 opacity-0 group-hover:opacity-40 animate-ping" style="animation-duration: 2s;"></div>
        
        <!-- Tooltip -->
        <div class="absolute left-full ml-4 bottom-1/4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 pointer-events-none transform group-hover:translate-x-0 translate-x-2">
            <div class="bg-gray-900 text-white text-sm font-bold rounded-2xl px-4 py-3 whitespace-nowrap shadow-2xl border border-gray-800 backdrop-blur-md">
                <div class="flex items-center gap-2">
                    <span class="text-green-400 text-lg">✓</span>
                    <span>تواصل معنا عبر WhatsApp</span>
                </div>
                <p class="text-xs text-gray-400 mt-1 font-normal">رد سريع في دقائق</p>
                <div class="absolute right-full -mr-1.5 top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-t-4 border-b-4 border-l-gray-900 border-t-transparent border-b-transparent"></div>
            </div>
        </div>

        <!-- WhatsApp Button -->
        <a href="<?= htmlspecialchars($whatsappUrl . '?text=' . urlencode($whatsappMessage)) ?>" 
           target="_blank" 
           rel="noopener noreferrer"
           aria-label="تواصل معنا عبر WhatsApp"
           class="whatsapp-widget relative flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-green-400 via-green-500 to-green-600 text-white shadow-2xl shadow-green-500/50 transition-all duration-300 hover:scale-125 hover:shadow-2xl hover:shadow-green-400/60 active:scale-95 group/btn border-4 border-white hover:border-green-200">
            <i class="bx bxl-whatsapp text-4xl drop-shadow-lg"></i>
            
            <!-- Floating Badge -->
            <span class="absolute -top-2 -left-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center shadow-lg animate-bounce">!</span>
        </a>
        </div>
    </div>

    <div id="report-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" dir="rtl" role="dialog" aria-modal="true" aria-labelledby="report-modal-title">
        <div class="w-full max-w-lg rounded-3xl border border-white/20 bg-white p-5 shadow-2xl sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black tracking-[0.2em] text-red-500">REPORT CENTER</p>
                    <h2 id="report-modal-title" class="mt-1 text-xl font-black text-slate-900">إبلاغ إدارة الموقع</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">ساعدنا في الحفاظ على محتوى آمن وموثوق.</p>
                </div>
                <button type="button" id="close-report-modal" aria-label="إغلاق نموذج البلاغ" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-900"><i class="bx bx-x text-xl"></i></button>
            </div>

            <form method="POST" action="<?= htmlspecialchars(url('/report')) ?>" class="mt-5 space-y-4">
                <input type="hidden" name="return_to" value="<?= htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') ?>">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-bold text-slate-700">نوع البلاغ
                        <select name="reported_type" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-100">
                            <option value="other">محتوى عام</option>
                            <option value="job">وظيفة</option>
                            <option value="company">شركة</option>
                            <option value="user">مستخدم</option>
                            <option value="ad">إعلان</option>
                        </select>
                    </label>
                    <label class="block text-sm font-bold text-slate-700">سبب البلاغ
                        <select name="reason" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-100">
                            <option value="spam">محتوى مزعج</option>
                            <option value="fraud">احتيال أو طلب أموال</option>
                            <option value="inappropriate">محتوى غير لائق</option>
                            <option value="harassment">تحرش أو إساءة</option>
                            <option value="copyright">انتهاك حقوق النشر</option>
                            <option value="other">سبب آخر</option>
                        </select>
                    </label>
                </div>
                <label class="block text-sm font-bold text-slate-700">رقم المحتوى إن وجد
                    <input type="number" name="reported_id" min="0" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-100" placeholder="مثال: رقم الوظيفة أو الشركة">
                </label>
                <label class="block text-sm font-bold text-slate-700">التفاصيل
                    <textarea name="description" minlength="10" required rows="4" class="mt-2 w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm leading-6 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-100" placeholder="اكتب تفاصيل المشكلة بوضوح..."></textarea>
                </label>
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-500 px-4 py-3 text-sm font-black text-white shadow-lg shadow-red-500/20 transition hover:-translate-y-0.5 hover:shadow-red-500/30"><i class="bx bx-send"></i> إرسال البلاغ</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('report-modal');
            const openButton = document.getElementById('open-report-modal');
            const closeButton = document.getElementById('close-report-modal');
            if (!modal || !openButton || !closeButton) return;

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };
            openButton.addEventListener('click', () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
            closeButton.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeModal();
            });
        });
    </script>

    <!-- WhatsApp Widget Styles and Animations -->
    <style>
        @keyframes whatsapp-float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-12px);
            }
        }
        
        @keyframes whatsapp-glow {
            0%, 100% {
                box-shadow: 0 0 20px 0 rgba(34, 197, 94, 0.4), 0 0 40px 0 rgba(34, 197, 94, 0.2);
            }
            50% {
                box-shadow: 0 0 30px 0 rgba(34, 197, 94, 0.6), 0 0 60px 0 rgba(34, 197, 94, 0.3);
            }
        }
        
        .whatsapp-widget {
            animation: whatsapp-glow 2s ease-in-out infinite;
        }
        
        .whatsapp-widget:hover {
            animation: whatsapp-float 0.5s ease-in-out infinite !important;
        }
        
        .whatsapp-widget i {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            animation: whatsapp-float 1s ease-in-out infinite;
        }
        
        /* Ripple effect on hover */
        .whatsapp-widget::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.6s ease-out;
        }
        
        .whatsapp-widget:hover::before {
            transform: translate(-50%, -50%) scale(1);
        }
        
        /* Responsive sizing */
        @media (max-width: 640px) {
            .whatsapp-widget {
                height: 60px !important;
                width: 60px !important;
            }
            
            .whatsapp-widget i {
                font-size: 32px !important;
            }
        }
        
        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .whatsapp-widget,
            .whatsapp-widget i,
            .animate-bounce {
                animation: none !important;
            }
        }
    </style>
</body>
</html>