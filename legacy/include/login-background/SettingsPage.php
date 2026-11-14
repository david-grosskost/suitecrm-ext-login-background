<?php

// Admin page of the login background: action LoginBackground of the
// Administration module, mapped to this file by
// legacy/Extension/modules/Administration/Ext/ActionFileMap/login-background.php.
// Run by SuiteCRM's controller, with the Administration labels in
// $mod_strings; administrators only.

declare(strict_types=1);

use SuiteCrmExt\LoginBackground\Page;
use SuiteCrmExt\LoginBackground\PageView;
use SuiteCrmExt\LoginBackground\Storage;

require_once __DIR__ . '/Settings.php';
require_once __DIR__ . '/ConfigTable.php';
require_once __DIR__ . '/Storage.php';
require_once __DIR__ . '/Upload.php';
require_once __DIR__ . '/Page.php';
// SuiteCRM's own view first (the current directory is public/legacy), then ours.
require_once getcwd() . '/include/MVC/View/SugarView.php';
require_once getcwd() . '/include/UploadFile.php';
require_once __DIR__ . '/PageView.php';

/** @var mixed $user */
$user = $GLOBALS['current_user'] ?? null;
if (!$user instanceof User || !is_admin($user)) {
    $denied = translate('ERR_NOT_ADMIN');
    sugar_die(is_string($denied) ? $denied : 'Unauthorized access to administration.');
}

/** @var mixed $labels */
$labels = $GLOBALS['mod_strings'] ?? [];
$labels = is_array($labels) ? $labels : [];

$storage = Storage::forSuiteCrm();
$messages = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read by SuiteCRM, which checks the file was just sent, its size and,
    // for an image, that it holds no code; false when it refuses it.
    $upload = new UploadFile('lb_image');
    $data = isset($_POST['lb_save']) && $upload->confirm_upload() ? $upload->get_file_contents() : false;
    $messages = Page::handle($_POST, $_FILES, is_string($data) ? $data : false, $storage, $labels);
}

$view = new PageView(Page::label($labels, 'LBL_LB_ADMIN_TITLE'), Page::html($storage->current(), $messages, $labels));
$view->init();
$view->process();
