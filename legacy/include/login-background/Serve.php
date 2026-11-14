<?php

// Entry point loginBackground (legacy/index.php?entryPoint=loginBackground),
// declared by legacy/Extension/application/Ext/EntryPointRegistry/login-background.php,
// open without login: the login page shows the image before anyone is signed
// in. It serves only the image of the administration, nothing else.

declare(strict_types=1);

use SuiteCrmExt\LoginBackground\Image;
use SuiteCrmExt\LoginBackground\Storage;

require_once __DIR__ . '/Settings.php';
require_once __DIR__ . '/ConfigTable.php';
require_once __DIR__ . '/Storage.php';
require_once __DIR__ . '/Image.php';

$ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? null;
$response = Image::response(Storage::forSuiteCrm()->current(), is_string($ifNoneMatch) ? $ifNoneMatch : null);

while (ob_get_level() > 0) {
    ob_end_clean();
}
http_response_code($response['status']);
foreach ($response['headers'] as $name => $value) {
    header("$name: $value");
}
// print, the image is the answer (echo is refused by the scanner of the
// Module Loader).
print $response['body'];
