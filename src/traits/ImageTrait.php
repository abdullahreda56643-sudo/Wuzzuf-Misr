<?php

trait ImageTrait
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

    protected function handleImageUpload(array $file, string $folderKey = 'users', int $maxWidth = 1600, int $maxHeight = 1600, string $prefix = 'image'): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'حدثت مشكلة في رفع الصورة'];
        }

        if (!isset($file['tmp_name']) || ($file['tmp_name'] === '' || !file_exists($file['tmp_name']))) {
            return ['success' => false, 'message' => 'لم يتم العثور على الملف المرفوع'];
        }

        $allowedTypes = array_map('strtolower', (array) config('allowed_image_types', ['jpg', 'jpeg', 'png', 'gif', 'webp']));
        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedTypes, true)) {
            return ['success' => false, 'message' => 'نوع الصورة غير مسموح. استخدم jpg، jpeg، png، gif أو webp'];
        }

        $maxSize = (int) config('max_upload_size', 5242880);
        if (($file['size'] ?? 0) > $maxSize) {
            return ['success' => false, 'message' => 'حجم الصورة كبير جدًا'];
        }

        $directory = $this->resolveUploadDirectory($folderKey);
        $safeName = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destination = $directory . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination) && !rename($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'تعذر حفظ الصورة على الخادم'];
        }

        $this->resizeImageIfNeeded($destination, $maxWidth, $maxHeight);

        $relativePath = '/assets/uploads/' . $folderKey . '/' . $safeName;

        return [
            'success' => true,
            'message' => 'تم رفع الصورة بنجاح',
            'path' => $relativePath,
            'full_path' => $destination,
            'filename' => $safeName,
        ];
    }

    protected function resizeImageIfNeeded(string $path, int $maxWidth = 1600, int $maxHeight = 1600): bool
    {
        if (!function_exists('getimagesize') || !extension_loaded('gd')) {
            return true;
        }

        if (!file_exists($path)) {
            return false;
        }

        [$width, $height, $type] = getimagesize($path);
        if ($width === false || $height === false) {
            return false;
        }

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return true;
        }

        $source = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => null,
        };

        if ($source === null) {
            return false;
        }

        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) floor(($height / $width) * $newWidth);
        }

        if ($newHeight > $maxHeight) {
            $newHeight = $maxHeight;
            $newWidth = (int) floor(($width / $height) * $newHeight);
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $saved = match ($type) {
            IMAGETYPE_JPEG => imagejpeg($resized, $path, 90),
            IMAGETYPE_PNG => imagepng($resized, $path, 9),
            IMAGETYPE_GIF => imagegif($resized, $path),
            IMAGETYPE_WEBP => imagewebp($resized, $path, 90),
            default => false,
        };

        imagedestroy($source);
        imagedestroy($resized);

        return (bool) $saved;
    }

    protected function deleteImageFile(?string $path): bool
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
