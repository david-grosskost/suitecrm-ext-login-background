<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Storage;

final class StorageTest extends TestCase
{
    public function testNoImageAtFirst(): void
    {
        $storage = new Storage(new MemorySettings());

        self::assertNull($storage->current());
        self::assertFalse($storage->remove());
    }

    public function testTheImageSavedIsTheCurrentOne(): void
    {
        $storage = new Storage(new MemorySettings());
        $storage->save(Images::png(), 'image/png');

        $current = $storage->current();
        self::assertNotNull($current);
        self::assertSame('image/png', $current['type']);
        self::assertSame(Images::png(), $current['data']);
        self::assertSame(16, strlen($current['version']));
    }

    public function testABigImageIsCutInPartsThatFitTheConfigTable(): void
    {
        $settings = new MemorySettings();
        $data = random_bytes(200 * 1024);
        (new Storage($settings))->save($data, 'image/jpeg');

        self::assertSame('5', $settings->values['parts'] ?? null);
        foreach ($settings->values as $value) {
            self::assertLessThanOrEqual(65535, strlen($value));
        }
        self::assertSame($data, (new Storage($settings))->current()['data'] ?? null);
    }

    public function testANewImageReplacesThePreviousOne(): void
    {
        $storage = new Storage(new MemorySettings());
        $storage->save(Images::png(), 'image/png');
        $first = $storage->current()['version'] ?? '';
        $storage->save(Images::jpeg(), 'image/jpeg');

        $current = $storage->current();
        self::assertNotNull($current);
        self::assertSame('image/jpeg', $current['type']);
        self::assertNotSame($first, $current['version']);
    }

    public function testRemoveGivesTheUsualBackgroundBack(): void
    {
        $settings = new MemorySettings();
        $storage = new Storage($settings);
        $storage->save(Images::png(), 'image/png');

        self::assertTrue($storage->remove());
        self::assertNull($storage->current());
        self::assertSame([], $settings->values);
    }

    public function testDamagedSettingsShowNoImage(): void
    {
        self::assertNull((new Storage(new MemorySettings(['type' => 'image/png', 'parts' => '1', 'part_0000' => '%%%'])))->current());
        self::assertNull((new Storage(new MemorySettings(['type' => 'image/gif', 'parts' => '1', 'part_0000' => 'AAAA'])))->current());
    }

    public function testAnotherTypeIsRefused(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Storage(new MemorySettings()))->save(Images::png(), 'image/gif');
    }
}
