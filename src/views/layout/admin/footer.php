        </main>
    </div>
</div>

<footer class="safe-bottom w-full overflow-hidden border-t border-slate-200 bg-white/80 py-5 backdrop-blur-xl">
    <div class="mx-auto flex w-full max-w-[1600px] min-w-0 flex-col items-center gap-3 px-4 text-center text-sm font-bold text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:text-right">
        <p class="max-w-full break-words leading-6">© <?= date('Y') ?> <?= htmlspecialchars(config('site_name', 'بوابة وظائف مصر | Wazaef Misr')) ?>. جميع الحقوق محفوظة.</p>
        <div class="flex w-full flex-wrap items-center justify-center gap-x-5 gap-y-2 lg:w-auto lg:justify-end">
            <a href="<?= htmlspecialchars(url('/')) ?>" class="whitespace-nowrap transition hover:text-gold-600">زيارة الموقع</a>
        </div>
    </div>
</footer>
</body>
</html>

