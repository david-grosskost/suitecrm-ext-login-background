<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

/** Small real images (1 × 1 pixel), and a text that is not one. */
final class Images
{
    public static function png(): string
    {
        return (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8DwHwAFBQIAX8jx0gAAAABJRU5ErkJggg==', true);
    }

    public static function jpeg(): string
    {
        return (string) base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=', true);
    }

    public static function fake(): string
    {
        return 'not an image';
    }
}
