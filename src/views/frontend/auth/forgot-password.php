<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استعادة كلمة المرور</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Cairo', 'sans-serif'] }, colors: { navy: { 900: '#071b2f' }, gold: { 500: '#c79734' } } } } }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-100">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md rounded-[30px] border border-slate-200 bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.08)]">
            <div class="mb-6 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gold-500/15 text-3xl text-gold-700">🔐</div>
                <h1 class="mt-5 text-2xl font-black text-navy-900">استعادة كلمة المرور</h1>
                <p class="mt-2 text-sm text-slate-500">أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.</p>
            </div>

            <?php if ($flash): ?>
                <div class="mb-5 rounded-2xl border px-4 py-3 text-sm <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= htmlspecialchars(url('/forgot-password')) ?>" class="space-y-5">
                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-navy-800">البريد الإلكتروني</label>
                    <input id="email" type="email" name="email" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-right text-slate-700 focus:border-gold-500 focus:bg-white focus:outline-none" placeholder="name@example.com">
                </div>

                <button type="submit" class="w-full rounded-2xl bg-gold-500 px-5 py-3 text-sm font-black text-navy-900 shadow-lg shadow-gold-500/25 transition hover:bg-gold-400">
                    إرسال رابط الاستعادة
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-slate-600">
                تذكرت كلمة المرور؟
                <a href="<?= htmlspecialchars(url('/login')) ?>" class="mr-1 font-black text-gold-700">تسجيل الدخول</a>
            </div>
        </div>
    </div>
</body>
</html>
