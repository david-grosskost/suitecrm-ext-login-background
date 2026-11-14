<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Page;
use SuiteCrmExt\LoginBackground\Storage;

/** The admin page: what each button does, and what it shows. */
final class PageTest extends TestCase
{
    private MemorySettings $settings;

    private Storage $storage;

    protected function setUp(): void
    {
        $this->settings = new MemorySettings();
        $this->storage = new Storage($this->settings);
    }

    /**
     * @param array<string, string> $post
     *
     * @return list<array{string, string}>
     */
    private function handle(array $post, mixed $file, string|false $data = false): array
    {
        return Page::handle($post, ['lb_image' => $file], $data, $this->storage, ['LBL_LB_SAVED' => 'Image enregistrée']);
    }

    public function testDisplayDoesNothing(): void
    {
        self::assertSame([], $this->handle([], null));
        self::assertNull($this->storage->current());
    }

    public function testSaveKeepsTheImage(): void
    {
        $messages = $this->handle(['lb_save' => '1'], ['error' => UPLOAD_ERR_OK, 'tmp_name' => '/tmp/php1'], Images::png());

        self::assertSame([['notice', 'Image enregistrée']], $messages);
        self::assertSame('image/png', $this->storage->current()['type'] ?? null);
    }

    public function testSaveRefusesWhatIsNotAnImage(): void
    {
        $messages = $this->handle(['lb_save' => '1'], ['error' => UPLOAD_ERR_OK, 'tmp_name' => '/tmp/php1'], Images::fake());

        self::assertSame([['error', 'LBL_LB_BAD_TYPE']], $messages);
        self::assertNull($this->storage->current());
    }

    public function testSaveRefusesAFileSuiteCrmRefused(): void
    {
        $messages = $this->handle(['lb_save' => '1'], ['error' => UPLOAD_ERR_OK, 'tmp_name' => '/tmp/php1'], false);

        self::assertSame([['error', 'LBL_LB_UPLOAD_FAILED']], $messages);
        self::assertNull($this->storage->current());
    }

    public function testRemoveTakesTheImageAway(): void
    {
        $this->storage->save(Images::png(), 'image/png');

        self::assertSame([['notice', 'LBL_LB_REMOVED']], $this->handle(['lb_remove' => '1'], null));
        self::assertNull($this->storage->current());
    }

    public function testThePageWithoutImage(): void
    {
        $html = Page::html(null, [], ['LBL_LB_NONE' => 'Aucune image']);

        self::assertStringContainsString('Aucune image', $html);
        self::assertStringContainsString('enctype="multipart/form-data"', $html);
        self::assertStringNotContainsString('lb_remove', $html);
    }

    public function testThePageWithAnImageShowsItAndCanRemoveIt(): void
    {
        $this->storage->save(Images::png(), 'image/png');

        $html = Page::html($this->storage->current(), [['error', '<b>']], []);

        self::assertStringContainsString('index.php?entryPoint=loginBackground&amp;v=' . ($this->storage->current()['version'] ?? ''), $html);
        self::assertStringContainsString('name="lb_remove"', $html);
        self::assertStringContainsString('&lt;b&gt;', $html);
    }

    /**
     * The labels a language file sets ($mod_strings['KEY'] = 'text';), read
     * from its text: the file itself only fills a variable of SuiteCRM.
     *
     * @return array<string, string>
     */
    private static function labels(string $language): array
    {
        $file = __DIR__ . "/../legacy/Extension/modules/Administration/Ext/Language/$language.login-background.php";
        preg_match_all('/^\$mod_strings\[\x27(\w+)\x27\] = \x27(.+)\x27;$/m', (string) file_get_contents($file), $matches);

        return array_combine($matches[1], $matches[2]);
    }

    public function testEveryLabelExistsInBothLanguages(): void
    {
        foreach (['fr_FR', 'en_us'] as $language) {
            $labels = self::labels($language);
            foreach ([...Page::LABELS, 'LBL_LB_ADMIN_HEADER', 'LBL_LB_ADMIN_HEADER_DESC', 'LBL_LB_ADMIN_DESC'] as $key) {
                self::assertNotEmpty($labels[$key] ?? null, "$language : $key");
            }
        }
    }
}
