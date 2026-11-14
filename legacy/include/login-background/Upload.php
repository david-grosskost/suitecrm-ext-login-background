<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The file sent by the admin form: the upload error PHP gives, then its
 * content (read by SuiteCRM's UploadFile, which also checks it was just
 * sent): an image of an accepted type, judged by its bytes, not its name.
 */
final class Upload
{
    /**
     * @param mixed $file The entry of $_FILES
     * @param string|false $data Its content, false when SuiteCRM refused it
     *
     * @return array{type: string}|array{error: string} The type, or the label of the error
     */
    public static function check(mixed $file, string|false $data): array
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
        if ($data === false || $data === '') {
            return ['error' => 'LBL_LB_UPLOAD_FAILED'];
        }
        if (strlen($data) > Storage::MAX_BYTES) {
            return ['error' => 'LBL_LB_TOO_BIG'];
        }

        $type = (new \finfo(FILEINFO_MIME_TYPE))->buffer($data);
        if (!is_string($type) || !isset(Storage::TYPES[$type]) || @getimagesizefromstring($data) === false) {
            return ['error' => 'LBL_LB_BAD_TYPE'];
        }

        return ['type' => $type];
    }
}
