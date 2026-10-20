<?php

trait UploadTrait
{
    protected function resolveUploadDirectory(string $folderKey): string
    {
        $directories = config('upload_directories', []);
        $directory = $directories[$folderKey] ?? ROOT_PATH . '/public/assets/uploads/' . $folderKey;

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new RuntimeException('Unable to create the upload directory: ' . $directory);
            }
        }

        return $directory;
    }

    protected function handleFileUpload(array $file, string $folderKey = 'resumes', ?array $allowedExtensions = null, ?int $maxSize = null, string $prefix = 'file'): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'حدثت مشكلة في رفع الملف'];
        }

        if (!isset($file['tmp_name']) || $file['tmp_name'] === '' || !file_exists($file['tmp_name'])) {
            return ['success' => false, 'message' => 'لم يتم العثور على الملف المرفوع'];
        }

        $allowed = $allowedExtensions ?? (array) config('allowed_file_types', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif']);
        $allowed = array_map('strtolower', $allowed);

        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed, true)) {
            return ['success' => false, 'message' => 'نوع الملف غير مسموح في هذا القسم'];
        }

        $limit = $maxSize ?? (int) config('max_upload_size', 5242880);
        if (($file['size'] ?? 0) > $limit) {
            return ['success' => false, 'message' => 'حجم الملف كبير جدًا'];
        }

        $directory = $this->resolveUploadDirectory($folderKey);
        $safeName = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destination = $directory . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination) && !rename($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'تعذر حفظ الملف على الخادم'];
        }

        return [
            'success' => true,
            'message' => 'تم رفع الملف بنجاح',
            'path' => '/assets/uploads/' . $folderKey . '/' . $safeName,
            'full_path' => $destination,
            'filename' => $safeName,
        ];
    }

    protected function deleteUploadedFile(?string $path): bool
    {
        if (empty($path)) {
            return true;
        }

        $cleanPath = ltrim((string) $path, '/');
        $fullPath = ROOT_PATH . '/public/' . $cleanPath;

        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }

        return true;
    }
}
