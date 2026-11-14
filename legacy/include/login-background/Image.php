<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The answer of the loginBackground entry point (Serve.php). The address of
 * the login page has no version: it is sent on, never cached, to the address
 * with the version of the image in use (&v=…), which is cached for a year:
 * the browser downloads an image once, and a new image has a new address.
 * (SuiteCRM's .htaccess takes the ETag away, so a 304 can't be relied on.)
 */
final class Image
{
    /** One year, in seconds: the image of a version never changes. */
    public const int CACHE = 31536000;

    /**
     * @param array{type: string, version: string, data: string}|null $current
     * @param string|null $version The version asked for (&v=…), if any
     *
     * @return array{status: int, headers: array<string, string>, body: string}
     */
    public static function response(?array $current, ?string $ifNoneMatch, ?string $version = null): array
    {
        if ($current === null) {
            return ['status' => 404, 'headers' => ['Cache-Control' => 'no-cache'], 'body' => ''];
        }
        if ($version !== $current['version']) {
            return [
                'status' => 302,
                'headers' => [
                    'Cache-Control' => 'no-cache',
                    'Location' => 'index.php?entryPoint=loginBackground&v=' . rawurlencode($current['version']),
                ],
                'body' => '',
            ];
        }

        $etag = '"' . $current['version'] . '"';
        $headers = [
            'Cache-Control' => 'public, max-age=' . self::CACHE . ', immutable',
            'Pragma' => 'public',
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
