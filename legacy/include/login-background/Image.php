<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The answer of the loginBackground entry point (Serve.php): the image, or
 * 404 without one, or 304 when the browser has it already. Never cached
 * without asking (no-cache): a new image shows at once.
 */
final class Image
{
    /**
     * @param array{type: string, version: string, data: string}|null $current
     *
     * @return array{status: int, headers: array<string, string>, body: string}
     */
    public static function response(?array $current, ?string $ifNoneMatch): array
    {
        if ($current === null) {
            return ['status' => 404, 'headers' => ['Cache-Control' => 'no-cache'], 'body' => ''];
        }

        $etag = '"' . $current['version'] . '"';
        $headers = [
            'Cache-Control' => 'no-cache',
            'ETag' => $etag,
            'X-Content-Type-Options' => 'nosniff',
        ];
        if ($ifNoneMatch !== null && trim($ifNoneMatch) === $etag) {
            return ['status' => 304, 'headers' => $headers, 'body' => ''];
        }

        return [
            'status' => 200,
            'headers' => $headers + [
                'Content-Type' => $current['type'],
                'Content-Length' => (string) strlen($current['data']),
            ],
            'body' => $current['data'],
        ];
    }
}
