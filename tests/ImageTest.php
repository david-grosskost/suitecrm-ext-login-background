<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Image;

/** What the entry point answers to the login page. */
final class ImageTest extends TestCase
{
    public function testNotFoundWithoutImage(): void
    {
        $response = Image::response(null, null);

        self::assertSame(404, $response['status']);
        self::assertNull($response['file']);
    }

    public function testTheImageWithItsType(): void
    {
        $response = Image::response(['path' => Images::png(), 'type' => 'image/png'], null);

        self::assertSame(200, $response['status']);
        self::assertSame(Images::png(), $response['file']);
        self::assertSame('image/png', $response['headers']['Content-Type'] ?? null);
        self::assertSame('no-cache', $response['headers']['Cache-Control']);
        self::assertSame('nosniff', $response['headers']['X-Content-Type-Options'] ?? null);
    }

    public function testNotModifiedWhenTheBrowserHasIt(): void
    {
        $current = ['path' => Images::png(), 'type' => 'image/png'];
        $etag = Image::response($current, null)['headers']['ETag'] ?? '';

        $response = Image::response($current, $etag);

        self::assertSame(304, $response['status']);
        self::assertNull($response['file']);
    }
}
