<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

/** Small real images for the tests, written once in the temporary folder. */
final class Images
{
    public static function png(): string
    {
        return self::write('png', 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8DwHwAFBQIAX8jx0gAAAABJRU5ErkJggg==');
    }

    public static function jpeg(): string
    {
        return self::write('jpg', '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
    }

    /** A text file named like an image. */
    public static function fake(): string
    {
        $path = sys_get_temp_dir() . '/login-background-fake.png';
        file_put_contents($path, 'not an image');

        return $path;
    }

    private static function write(string $extension, string $base64): string
    {
        $path = sys_get_temp_dir() . "/login-background-test.$extension";
        if (!is_file($path)) {
            file_put_contents($path, (string) base64_decode($base64, true));
        }

        return $path;
    }
}
