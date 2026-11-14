<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground;

/**
 * The settings in SuiteCRM's config table (category, name, value), where
 * Administration keeps its own: in the database, so in the backups, and with
 * no file to write (the scanner of the Module Loader refuses file functions).
 */
final class ConfigTable implements Settings
{
    public const string CATEGORY = 'login_background';

    public function __construct(private readonly \DBManager $db) {}

    public static function forSuiteCrm(): self
    {
        return new self(\DBManagerFactory::getInstance());
    }

    public function read(): array
    {
        $values = [];
        $result = $this->db->query('SELECT name, value FROM config WHERE category = ' . $this->db->quoted(self::CATEGORY));
        while (is_array($row = $this->db->fetchByAssoc($result, false))) {
            if (is_string($row['name'] ?? null) && is_string($row['value'] ?? null)) {
                $values[$row['name']] = $row['value'];
            }
        }

        return $values;
    }

    public function replace(array $values): void
    {
        $category = $this->db->quoted(self::CATEGORY);
        $this->db->query("DELETE FROM config WHERE category = {$category}");
        foreach ($values as $name => $value) {
            $this->db->query("INSERT INTO config (category, name, value) VALUES ({$category}, {$this->db->quoted($name)}, {$this->db->quoted($value)})");
        }
    }
}
