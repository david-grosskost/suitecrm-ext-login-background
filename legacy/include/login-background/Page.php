<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The admin page (Administration → Fond de la page de connexion): the image
 * in use, a field to choose another one, a button to remove it. What it does
 * and shows, without SuiteCRM: SettingsPage.php plugs it in.
 */
final class Page
{
    /** Legacy action of the page, in the Administration module (ActionFileMap). */
    public const string ACTION = 'LoginBackground';

    /** Texts of the page, in the Administration labels. */
    public const array LABELS = [
        'LBL_LB_ADMIN_TITLE',
        'LBL_LB_PAGE_INTRO',
        'LBL_LB_CURRENT',
        'LBL_LB_NONE',
        'LBL_LB_IMAGE',
        'LBL_LB_IMAGE_HELP',
        'LBL_LB_SAVE',
        'LBL_LB_REMOVE',
        'LBL_LB_SAVED',
        'LBL_LB_REMOVED',
        'LBL_LB_NO_FILE',
        'LBL_LB_TOO_BIG',
        'LBL_LB_BAD_TYPE',
        'LBL_LB_UPLOAD_FAILED',
    ];

    /**
     * What a request does: nothing on display, the image kept on
     * "Enregistrer", removed on "Retirer l'image".
     *
     * @param array<array-key, mixed> $post
     * @param array<array-key, mixed> $files
     * @param callable(string): bool $isUpload Whether a path is a file just sent (is_uploaded_file)
     * @param array<array-key, mixed> $labels
     *
     * @return list<array{string, string}> Messages (type, text)
     */
    public static function handle(array $post, array $files, Storage $storage, callable $isUpload, array $labels): array
    {
        if (isset($post['lb_remove'])) {
            $storage->remove();

            return [['notice', self::label($labels, 'LBL_LB_REMOVED')]];
        }
        if (!isset($post['lb_save'])) {
            return [];
        }

        $file = $files['lb_image'] ?? null;
        $checked = Upload::check($file);
        if (isset($checked['error'])) {
            return [['error', self::label($labels, $checked['error'])]];
        }
        $path = is_array($file) && is_string($file['tmp_name'] ?? null) ? $file['tmp_name'] : '';
        if (!$isUpload($path)) {
            return [['error', self::label($labels, 'LBL_LB_UPLOAD_FAILED')]];
        }
        $storage->save($path, $checked['type']);

        return [['notice', self::label($labels, 'LBL_LB_SAVED')]];
    }

    /**
     * The form, in SuiteCRM's legacy styles (edit view, buttons), with a
     * preview of the image in use.
     *
     * @param array{path: string, type: string}|null $current
     * @param list<array{string, string}> $messages
     * @param array<array-key, mixed> $labels
     */
    public static function html(?array $current, array $messages, array $labels): string
    {
        $e = static fn(string $text): string => htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $l = static fn(string $key): string => $e(self::label($labels, $key));

        $html = '';
        foreach ($messages as [$type, $text]) {
            $class = $type === 'error' ? 'error' : 'msg';
            $html .= "<p class=\"{$class}\">{$e($text)}</p>\n";
        }

        $action = self::ACTION;
        $accept = implode(',', array_keys(Storage::TYPES));
        $max = Storage::MAX_BYTES;
        if ($current === null) {
            $preview = "<em>{$l('LBL_LB_NONE')}</em>";
            $remove = '';
        } else {
            // The modification time in the address: a new image is not taken from the cache.
            $version = (string) (filemtime($current['path']) ?: 0);
            $preview = "<img src=\"index.php?entryPoint=loginBackground&amp;v={$version}\" alt=\"\" style=\"max-width: 480px; max-height: 270px; border: 1px solid #ccc;\">";
            $remove = "<input type=\"submit\" class=\"button\" name=\"lb_remove\" value=\"{$l('LBL_LB_REMOVE')}\">";
        }

        return $html . <<<HTML
            <p>{$l('LBL_LB_PAGE_INTRO')}</p>
            <form method="post" enctype="multipart/form-data" action="index.php?module=Administration&amp;action={$action}">
                <input type="hidden" name="module" value="Administration">
                <input type="hidden" name="action" value="{$action}">
                <input type="hidden" name="MAX_FILE_SIZE" value="{$max}">
                <table class="edit view" width="100%" cellspacing="1" cellpadding="0" border="0">
                    <tr>
                        <td scope="row" width="25%">{$l('LBL_LB_CURRENT')}</td>
                        <td>{$preview}</td>
                    </tr>
                    <tr>
                        <td scope="row"><label for="lb_image">{$l('LBL_LB_IMAGE')}</label></td>
                        <td>
                            <input type="file" id="lb_image" name="lb_image" accept="{$accept}">
                            <span>{$l('LBL_LB_IMAGE_HELP')}</span>
                        </td>
                    </tr>
                </table>
                <input type="submit" class="button primary" name="lb_save" value="{$l('LBL_LB_SAVE')}">
                {$remove}
            </form>
            HTML;
    }

    /** @param array<array-key, mixed> $labels */
    public static function label(array $labels, string $key): string
    {
        $value = $labels[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : $key;
    }
}
