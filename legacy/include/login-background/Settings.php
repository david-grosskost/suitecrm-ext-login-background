<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The settings of the extension, as names and texts: the config table of
 * SuiteCRM (ConfigTable), or memory in the tests.
 */
interface Settings
{
    /** @return array<string, string> All the settings, by name */
    public function read(): array;

    /**
     * Replaces all the settings (none: all taken away).
     *
     * @param array<string, string> $values
     */
    public function replace(array $values): void;
}
