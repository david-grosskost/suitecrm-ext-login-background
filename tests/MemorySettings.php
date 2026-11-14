<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use SuiteCrmExt\LoginBackground\Settings;

/** The settings in memory, instead of the config table of SuiteCRM. */
final class MemorySettings implements Settings
{
    /** @param array<string, string> $values */
    public function __construct(public array $values = []) {}

    public function read(): array
    {
        return $this->values;
    }

    public function replace(array $values): void
    {
        $this->values = $values;
    }
}
