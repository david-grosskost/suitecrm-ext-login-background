<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Image;

final class ImageTest extends TestCase
{
    private const string VERSION = '0123456789abcdef';

    public function testNotFoundWithoutImage(): void
    {
        $response = Image::response(null, null);

        self::assertSame(404, $response['status']);
        self::assertSame('', $response['body']);
    }

    public function testTheImageWithItsType(): void
    {
        $response = Image::response(['type' => 'image/png', 'version' => self::VERSION, 'data' => Images::png()], null);

        self::assertSame(200, $response['status']);
        self::assertSame(Images::png(), $response['body']);
        self::assertSame('image/png', $response['headers']['Content-Type'] ?? null);
        self::assertSame((string) strlen(Images::png()), $response['headers']['Content-Length'] ?? null);
        self::assertSame('no-cache', $response['headers']['Cache-Control']);
        self::assertSame('nosniff', $response['headers']['X-Content-Type-Options'] ?? null);
    }

    public function testNotModifiedWhenTheBrowserHasIt(): void
    {
        $current = ['type' => 'image/png', 'version' => self::VERSION, 'data' => Images::png()];

        $response = Image::response($current, '"' . self::VERSION . '"');

        self::assertSame(304, $response['status']);
        self::assertSame('', $response['body']);
    }
}
