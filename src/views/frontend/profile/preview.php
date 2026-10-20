<?php
$previewUrl = $previewUrl ?? '';
$fileUrl = $fileUrl ?? '#';
$isPdf = !empty($isPdf);
$fileName = htmlspecialchars($fileName ?? 'السيرة الذاتية');
$fileExtension = strtoupper((string) ($fileExtension ?? ''));
$canRenderDocx = strtolower((string) ($fileExtension ?? '')) === 'docx';
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fileName ?> | معاينة الملف</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/mammoth@1.8.0/mammoth.browser.min.js"></script>
    <style>
        html, body {
            font-family: 'IBM Plex Sans Arabic', 'Tahoma', 'Segoe UI', sans-serif;
        }
        body { background: #f2f6fb; }
        .frame-wrap { background: #fff; border: 1px solid #dfeaf5; box-shadow: 0 18px 40px rgba(15,39,58,0.10); }
        .docx-content {
            line-height: 1.9;
            color: #0f172a;
            text-align: right;
            direction: rtl;
            font-family: 'IBM Plex Sans Arabic', 'Tahoma', 'Segoe UI', sans-serif;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .docx-content h1, .docx-content h2, .docx-content h3,
        .docx-content h4, .docx-content h5, .docx-content h6 {
            font-weight: 900;
            margin: 1.2rem 0 0.7rem;
            color: #0f172a;
            line-height: 1.35;
        }
        .docx-content h1 { font-size: clamp(1.5rem, 2.4vw, 2rem); }
        .docx-content h2 { font-size: clamp(1.3rem, 2vw, 1.7rem); }
        .docx-content h3 { font-size: clamp(1.15rem, 1.6vw, 1.35rem); }
        .docx-content p {
            margin: 0.55rem 0;
            font-size: clamp(0.92rem, 1vw, 1rem);
        }
        .docx-content ul, .docx-content ol {
            margin: 0.8rem 0 0.8rem 1.2rem;
            padding-inline-start: 1.2rem;
        }
        .docx-content li { margin: 0.25rem 0; }
        .docx-content a { color: #1d4ed8; text-decoration: underline; }
        .docx-content table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            table-layout: fixed;
            display: block;
            overflow-x: auto;
        }
        .docx-content th, .docx-content td {
            border: 1px solid #dbe4ee;
            padding: 0.6rem 0.75rem;
            text-align: right;
            vertical-align: top;
            white-space: normal;
            word-break: break-word;
        }
        .docx-content th {
            background: #f8fafc;
            font-weight: 800;
        }
        .docx-content strong { font-weight: 800; }
        .docx-content em { font-style: italic; }
        .docx-content blockquote {
            border-right: 4px solid #cbd5e1;
            padding-right: 0.9rem;
            color: #475569;
            margin: 1rem 0;
        }
        .docx-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            display: block;
        }
        .docx-content .mammoth-figure {
            margin: 1rem 0;
        }

        @media (max-width: 640px) {
            .preview-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .preview-actions {
                width: 100%;
                justify-content: stretch;
            }
            .preview-actions a {
                flex: 1;
                text-align: center;
            }
            .docx-content {
                padding: 1rem !important;
            }
            .docx-content table {
                font-size: 0.78rem;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100">
    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="preview-header mb-5 flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div>
                <p class="text-xs font-bold tracking-[0.2em] text-slate-500">معاينة الملف</p>
                <h1 class="mt-1 text-xl font-black text-slate-900"><?= $fileName ?></h1>
            </div>
            <a href="<?= htmlspecialchars(url('/profile')) ?>" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100">عودة</a>
        </div>

        <?php if ($isPdf): ?>
            <div class="frame-wrap overflow-hidden rounded-[28px]">
                <iframe src="<?= htmlspecialchars($previewUrl) ?>" title="معاينة الملف" class="h-[80vh] w-full" frameborder="0"></iframe>
            </div>
        <?php elseif ($canRenderDocx): ?>
            <div class="frame-wrap rounded-[28px] p-4 sm:p-6">
                <div id="docxPreview" class="docx-content min-h-[400px] rounded-2xl bg-white p-4 sm:p-6">
                    <div class="flex min-h-[300px] items-center justify-center text-sm font-bold text-slate-500">جاري تحويل الملف إلى معاينة داخل الموقع...</div>
                </div>
                <div class="preview-actions mt-4 flex flex-wrap items-center justify-center gap-3">
                    <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank" rel="noopener noreferrer" class="rounded-xl bg-[#173d5d] px-5 py-3 text-sm font-black text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5">فتح الملف</a>
                    <a href="<?= htmlspecialchars($fileUrl) ?>" download class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">تحميل الملف</a>
                </div>
            </div>
            <script>
                const previewUrl = <?= json_encode($fileUrl, JSON_UNESCAPED_SLASHES) ?>;
                const previewContainer = document.getElementById('docxPreview');

                fetch(previewUrl)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('فشل تحميل الملف');
                        }
                        return response.arrayBuffer();
                    })
                    .then((arrayBuffer) => mammoth.convertToHtml({ arrayBuffer }))
                    .then((result) => {
                        previewContainer.innerHTML = result.value || '<p class="text-sm font-bold text-slate-500">لا يوجد محتوى قابل للعرض في هذا الملف.</p>';
                    })
                    .catch(() => {
                        previewContainer.innerHTML = `
                            <div class="flex min-h-[300px] flex-col items-center justify-center text-center">
                                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-100 text-2xl">⚠️</div>
                                <h2 class="text-xl font-black text-slate-800">تعذر عرض الملف داخل الموقع</h2>
                                <p class="mt-2 text-sm leading-7 text-slate-600">يمكنك فتح الملف أو تحميله مباشرة من الزرين أدناه.</p>
                            </div>
                        `;
                    });
            </script>
        <?php else: ?>
            <div class="frame-wrap rounded-[28px] p-6 sm:p-8">
                <div class="flex min-h-[320px] flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-100 text-2xl">📄</div>
                    <h2 class="text-xl font-black text-slate-800">هذا الملف لا يدعم المعاينة مباشرة</h2>
                    <p class="mt-2 max-w-xl text-sm leading-7 text-slate-600">
                        نوع الملف الحالي هو <?= $fileExtension ?>، ويمكنك فتحه أو تحميله مباشرة.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                        <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank" rel="noopener noreferrer" class="rounded-xl bg-[#173d5d] px-5 py-3 text-sm font-black text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5">فتح الملف</a>
                        <a href="<?= htmlspecialchars($fileUrl) ?>" download class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">تحميل الملف</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
