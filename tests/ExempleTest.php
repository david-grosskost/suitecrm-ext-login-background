<?php

declare(strict_types=1);

namespace SuiteCrmExt\Exemple\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SuiteCrmExt\Exemple\Exemple;

final class ExempleTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function names(): iterable
    {
        yield 'sans forme juridique' => ['Dupont', '', 'Dupont'];
        yield 'avec forme juridique' => ['Dupont', 'SARL', 'Dupont SARL'];
        yield 'espaces en trop' => ['  Dupont ', ' SAS ', 'Dupont SAS'];
    }

    #[DataProvider('names')]
    public function testDisplayName(string $name, string $legalForm, string $expected): void
    {
        self::assertSame($expected, Exemple::displayName($name, $legalForm));
    }
}
