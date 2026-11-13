<?php

declare(strict_types=1);

namespace SuiteCrmExt\Exemple;

/**
 * Example of the template: replace it with the code of the extension.
 * The logic lives in plain classes like this one, tested without SuiteCRM;
 * the SuiteCRM hook (legacy/Extension/…) only calls them.
 */
final class Exemple
{
    /**
     * Name shown for an account, its legal form last: "Dupont SARL".
     */
    public static function displayName(string $name, string $legalForm = ''): string
    {
        $name = trim($name);
        $legalForm = trim($legalForm);

        return $legalForm === '' ? $name : "$name $legalForm";
    }
}
