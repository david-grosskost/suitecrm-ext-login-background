<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Storage;

/** The image kept on the instance: one file, replaced or removed. */
final class StorageTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/login-background-' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            unlink($file);
        }
        if (is_dir($this->dir)) {
            rmdir($this->dir);
        }
    }

    public function testNoImageAtFirst(): void
    {
        self::assertNull((new Storage($this->dir))->current());
        self::assertFalse((new Storage($this->dir))->remove());
    }

    public function testTheImageSavedIsTheCurrentOne(): void
    {
        $storage = new Storage($this->dir);
        $storage->save(Images::png(), 'image/png');

        self::assertSame(['path' => $this->dir . '/background.png', 'type' => 'image/png'], $storage->current());
        self::assertFileEquals(Images::png(), $this->dir . '/background.png');
    }

    public function testANewImageReplacesThePreviousOne(): void
    {
        $storage = new Storage($this->dir);
        $storage->save(Images::png(), 'image/png');
        $storage->save(Images::jpeg(), 'image/jpeg');

        self::assertSame('image/jpeg', $storage->current()['type'] ?? null);
        self::assertSame(['background.jpg'], array_map('basename', glob($this->dir . '/*') ?: []));
    }

    public function testRemoveGivesTheUsualBackgroundBack(): void
    {
        $storage = new Storage($this->dir);
        $storage->save(Images::png(), 'image/png');

        self::assertTrue($storage->remove());
        self::assertNull($storage->current());
    }

    public function testAnotherTypeIsRefused(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Storage($this->dir))->save(Images::png(), 'image/gif');
    }
}
