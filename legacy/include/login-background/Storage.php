<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The image chosen in the administration: one file, background.<jpg|png|webp>,
 * in upload/login-background/ of the instance (persistent volume, in the
 * nightly backup; the upload folder is not served by the web server, the
 * entry point serves it).
 */
final class Storage
{
    /** Accepted types and the extension of the file kept. */
    public const array TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public const int MAX_BYTES = 5 * 1024 * 1024;

    private const string NAME = 'background';

    public function __construct(private readonly string $dir) {}

    /** In SuiteCRM: the upload folder of its configuration, from public/legacy. */
    public static function forSuiteCrm(): self
    {
        /** @var mixed $config */
        $config = $GLOBALS['sugar_config'] ?? [];
        $upload = is_array($config) && is_string($config['upload_dir'] ?? null) ? $config['upload_dir'] : 'upload/';
        if (!str_starts_with($upload, '/')) {
            $upload = (getcwd() ?: '.') . '/' . $upload;
        }

        return new self(rtrim($upload, '/') . '/login-background');
    }

    /** @return array{path: string, type: string}|null The image kept, if any */
    public function current(): ?array
    {
        foreach (self::TYPES as $type => $extension) {
            $path = $this->path($extension);
            if (is_file($path)) {
                return ['path' => $path, 'type' => $type];
            }
        }

        return null;
    }

    /** Keeps $file as the image, in place of the previous one. */
    public function save(string $file, string $type): void
    {
        $extension = self::TYPES[$type] ?? throw new \InvalidArgumentException("Type d'image refusé : $type");
        if (!is_dir($this->dir) && !mkdir($this->dir, 0o775, true) && !is_dir($this->dir)) {
            throw new \RuntimeException("Dossier impossible à créer : {$this->dir}");
        }
        $target = $this->path($extension);
        $temporary = $target . '.new';
        if (!copy($file, $temporary) || !rename($temporary, $target)) {
            @unlink($temporary);
            throw new \RuntimeException("Image impossible à enregistrer : $target");
        }
        foreach (self::TYPES as $other) {
            if ($other !== $extension) {
                @unlink($this->path($other));
            }
        }
    }

    /** Removes the image: the login page gets its own background back. */
    public function remove(): bool
    {
        $current = $this->current();

        return $current !== null && unlink($current['path']);
    }

    private function path(string $extension): string
    {
        return $this->dir . '/' . self::NAME . '.' . $extension;
    }
}
