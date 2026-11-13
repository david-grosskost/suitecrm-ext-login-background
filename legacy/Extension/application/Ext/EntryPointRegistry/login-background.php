<?php

// Fond de la page de connexion (extension login-background): the entry point
// that serves the image, legacy/index.php?entryPoint=loginBackground. Open
// without login (auth false): the login page shows it before sign-in. It
// serves only that image.

/** @var array<string, array{file: string, auth: bool}> $entry_point_registry Set by SuiteCRM */
$entry_point_registry['loginBackground'] = [
    'file' => 'custom/include/login-background/Serve.php',
    'auth' => false,
];
