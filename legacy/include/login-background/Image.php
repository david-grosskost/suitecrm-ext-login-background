<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The answer of the entry point that serves the image (Serve.php): 404
 * without image, 304 when the browser already has this one, 200 with it
 * otherwise. The browser asks again each time (no-cache), so a new image
 * shows at the next visit of the login page.
 */
final class Image
{
    /**
     * @param array{path: string, type: string}|null $current
     *
     * @return array{status: int, headers: array<string, string>, file: string|null}
     */
    public static function response(?array $current, ?string $ifNoneMatch): array
    {
        if ($current === null || !is_file($current['path'])) {
            return ['status' => 404, 'headers' => ['Cache-Control' => 'no-cache'], 'file' => null];
        }

        $etag = '"' . hash('sha256', (string) filemtime($current['path']) . '-' . (string) filesize($current['path'])) . '"';
        $headers = [
            'Cache-Control' => 'no-cache',
            'ETag' => $etag,
            'X-Content-Type-Options' => 'nosniff',
        ];
        if ($ifNoneMatch !== null && trim($ifNoneMatch) === $etag) {
            return ['status' => 304, 'headers' => $headers, 'file' => null];
        }

        return [
            'status' => 200,
            'headers' => $headers + [
                'Content-Type' => $current['type'],
                'Content-Length' => (string) filesize($current['path']),
            ],
            'file' => $current['path'],
        ];
    }
}
