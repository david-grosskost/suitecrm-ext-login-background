<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * Shows the admin page inside SuiteCRM's page (header, styles, footer), as
 * the legacy admin pages are: what the controller does for a view of its own.
 * SettingsPage.php loads SuiteCRM's SugarView before this file.
 */
final class PageView extends \SugarView
{
    public function __construct(private readonly string $title, private readonly string $body)
    {
        parent::__construct();
    }

    public function display(): void
    {
        $administration = \translate('LBL_MODULE_NAME', 'Administration');
        $link = '<a href="index.php?module=Administration&amp;action=index">'
            . htmlspecialchars(is_string($administration) ? $administration : 'Administration', ENT_QUOTES, 'UTF-8') . '</a>';
        echo \getClassicModuleTitle('Administration', [$link, htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8')], false);
        echo $this->body;
    }
}
