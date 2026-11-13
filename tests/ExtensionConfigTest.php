<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;

/**
 * config/extension.php, read as SuiteCRM 8 reads it: included with the
 * container whose "extensions" parameter it completes.
 */
final class ExtensionConfigTest extends TestCase
{
    /**
     * The "extensions" parameter once the file is included.
     *
     * @param array<string, mixed> $before
     *
     * @return array<mixed>
     */
    private static function extensions(array $before): array
    {
        $container = new class ($before) {
            /** @param array<string, mixed> $parameters */
            public function __construct(public array $parameters) {}

            public function getParameter(string $name): mixed
            {
                return $this->parameters[$name] ?? null;
            }

            public function setParameter(string $name, mixed $value): void
            {
                $this->parameters[$name] = $value;
            }
        };
        require __DIR__ . '/../config/extension.php';
        $extensions = $container->parameters['extensions'] ?? null;

        return is_array($extensions) ? $extensions : [];
    }

    public function testTheModuleIsDeclaredUnderTheExtensionDirectory(): void
    {
        $module = self::extensions(['extensions' => []])['LoginBackground'] ?? null;

        self::assertIsArray($module);
        self::assertSame('../extensions/LoginBackground/remoteEntry.js', $module['remoteEntry'] ?? null);
        self::assertSame('LoginBackground', $module['remoteName'] ?? null);
        self::assertTrue($module['enabled'] ?? null);
    }

    public function testTheOtherExtensionsAreKept(): void
    {
        $extensions = self::extensions(['extensions' => ['defaultExt' => ['enabled' => false]]]);

        self::assertSame(['defaultExt', 'LoginBackground'], array_keys($extensions));
    }

    public function testTheWebpackNameIsTheExtensionDirectory(): void
    {
        $webpack = (string) file_get_contents(__DIR__ . '/../app/webpack.config.js');

        self::assertStringContainsString("name: 'LoginBackground'", $webpack);
        self::assertStringContainsString("'./Module': './extensions/LoginBackground/app/src/extension.module.ts'", $webpack);
    }
}
