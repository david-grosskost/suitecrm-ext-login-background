<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Image;

final class ImageTest extends TestCase
{
    private const string VERSION = '0123456789abcdef';

    /** @return array{type: string, version: string, data: string} */
    private static function current(): array
    {
        return ['type' => 'image/png', 'version' => self::VERSION, 'data' => Images::png()];
    }

    public function testNotFoundWithoutImage(): void
    {
        $response = Image::response(null, null, self::VERSION);

        self::assertSame(404, $response['status']);
        self::assertSame('', $response['body']);
    }

    public function testTheAddressWithoutVersionSendsOnToTheImageInUse(): void
    {
        $response = Image::response(self::current(), null);

        self::assertSame(302, $response['status']);
        self::assertSame('index.php?entryPoint=loginBackground&v=' . self::VERSION, $response['headers']['Location'] ?? null);
        self::assertSame('no-cache', $response['headers']['Cache-Control']);
        self::assertSame('', $response['body']);
    }

    public function testAnOldVersionSendsOnToTheNewOne(): void
    {
        $response = Image::response(self::current(), null, 'fedcba9876543210');

        self::assertSame(302, $response['status']);
        self::assertStringEndsWith('&v=' . self::VERSION, $response['headers']['Location'] ?? '');
    }

    public function testTheImageOfItsVersionIsCachedForAYear(): void
    {
        $response = Image::response(self::current(), null, self::VERSION);

        self::assertSame(200, $response['status']);
        self::assertSame(Images::png(), $response['body']);
        self::assertSame('image/png', $response['headers']['Content-Type'] ?? null);
        self::assertSame((string) strlen(Images::png()), $response['headers']['Content-Length'] ?? null);
        self::assertSame('public, max-age=31536000, immutable', $response['headers']['Cache-Control']);
        self::assertSame('nosniff', $response['headers']['X-Content-Type-Options'] ?? null);
    }

    public function testNotModifiedWhenTheBrowserHasIt(): void
    {
        $response = Image::response(self::current(), '"' . self::VERSION . '"', self::VERSION);

        self::assertSame(304, $response['status']);
        self::assertSame('', $response['body']);
    }
}
