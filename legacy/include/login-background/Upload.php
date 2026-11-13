<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * Checks the file sent by the admin form ($_FILES entry): an image JPEG, PNG
 * or WebP of at most 5 MB, recognised by its content, not by its name.
 */
final class Upload
{
    /**
     * @return array{type: string}|array{error: string} The image type, or the label of what is wrong
     */
    public static function check(mixed $file): array
    {
        if (!is_array($file) || !is_int($file['error'] ?? null)) {
            return ['error' => 'LBL_LB_NO_FILE'];
        }

        $error = match ($file['error']) {
            UPLOAD_ERR_OK => null,
            UPLOAD_ERR_NO_FILE => 'LBL_LB_NO_FILE',
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'LBL_LB_TOO_BIG',
            default => 'LBL_LB_UPLOAD_FAILED',
        };
        if ($error !== null) {
            return ['error' => $error];
        }

        $path = $file['tmp_name'] ?? null;
        if (!is_string($path) || !is_file($path)) {
            return ['error' => 'LBL_LB_UPLOAD_FAILED'];
        }
        if (filesize($path) > Storage::MAX_BYTES) {
            return ['error' => 'LBL_LB_TOO_BIG'];
        }

        $type = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        if (!is_string($type) || !isset(Storage::TYPES[$type]) || @getimagesize($path) === false) {
            return ['error' => 'LBL_LB_BAD_TYPE'];
        }

        return ['type' => $type];
    }
}
