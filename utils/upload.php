<?php
/**
 * File upload utilities.
 */

declare(strict_types=1);

/**
 * Validate and store an uploaded file.
 *
 * @param array $file The $_FILES entry.
 *
 * @return string|null The relative path of the stored file or null on failure.
 */
function handle_upload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['pdf', 'png', 'jpg', 'jpeg'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    if (($file['size'] ?? 0) > $maxSize) {
        return null;
    }

    $uploadDir = __DIR__ . '/../public/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid('', true) . '.' . $ext;
    $targetPath = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return null;
    }

    return '/public/uploads/' . $filename;
}
