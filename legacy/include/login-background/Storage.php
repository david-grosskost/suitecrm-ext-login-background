<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The background image, kept in the settings (base64, cut in parts that fit
 * the value column of the config table, a text of 64 KB). One image at most:
 * a new one replaces it.
 */
final class Storage
{
    /** Image types accepted, and their extension. */
    public const array TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    /** Heaviest image accepted: 5 MB, enough for a full-screen photo. */
    public const int MAX_BYTES = 5 * 1024 * 1024;

    /** Base64 characters per part. */
    private const int PART = 60000;

    public function __construct(private readonly Settings $settings) {}

    public static function forSuiteCrm(): self
    {
        return new self(ConfigTable::forSuiteCrm());
    }

    /**
     * The image there is, or null: its type, a version that changes with the
     * image (its digest), and its content.
     *
     * @return array{type: string, version: string, data: string}|null
     */
    public function current(): ?array
    {
        $values = $this->settings->read();
        $type = $values['type'] ?? '';
        $count = (int) ($values['parts'] ?? '0');
        if (!isset(self::TYPES[$type]) || $count < 1) {
            return null;
        }

        $encoded = '';
        for ($part = 0; $part < $count; ++$part) {
            $encoded .= $values[self::part($part)] ?? '';
        }
        $data = base64_decode($encoded, true);
        if ($data === false || $data === '') {
            return null;
        }

        return ['type' => $type, 'version' => $values['version'] ?? '', 'data' => $data];
    }

    public function save(string $data, string $type): void
    {
        if (!isset(self::TYPES[$type])) {
            throw new \InvalidArgumentException("Type d'image refusé : $type");
        }

        $parts = str_split(base64_encode($data), self::PART);
        $values = [
            'type' => $type,
            'version' => substr(hash('sha256', $data), 0, 16),
            'parts' => (string) count($parts),
        ];
        foreach ($parts as $index => $part) {
            $values[self::part($index)] = $part;
        }
        $this->settings->replace($values);
    }

    public function remove(): bool
    {
        if ($this->settings->read() === []) {
            return false;
        }
        $this->settings->replace([]);

        return true;
    }

    private static function part(int $index): string
    {
        return sprintf('part_%04d', $index);
    }
}
