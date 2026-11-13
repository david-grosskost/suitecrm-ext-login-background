<?php

// Fond de la page de connexion (extension login-background): declares its
// Angular module to SuiteCRM 8, which loads it when the interface starts
// (module federation). The module (public/, built from app/ by make
// front@build) is under extensions/LoginBackground/ in SuiteCRM: the slug
// in PascalCase. Enabled: the extension's files are there only when it is active
// (EXTENSIONS of the instance).

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Container;

if (!isset($container)) {
    return;
}

/** @var Container $container */
$extensions = $container->getParameter('extensions');
$extensions = is_array($extensions) ? $extensions : [];

$extensions['LoginBackground'] = [
    'remoteEntry' => '../extensions/LoginBackground/remoteEntry.js',
    'remoteName' => 'LoginBackground',
    'enabled' => true,
    'extension_name' => 'Fond de la page de connexion',
    'extension_uri' => 'https://github.com/david-grosskost/suitecrm-ext-login-background',
    'description' => 'Image de fond de la page de connexion, choisie dans l\'administration.',
    'version' => '0.1.0',
    'author' => 'David Grosskost',
    'author_uri' => 'https://david-grosskost.fr',
    'license' => 'proprietary',
];

$container->setParameter('extensions', $extensions);
