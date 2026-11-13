<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Page;
use SuiteCrmExt\LoginBackground\Storage;

/** The admin page: what each button does, and what it shows. */
final class PageTest extends TestCase
{
    private string $dir;

    private Storage $storage;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/login-background-page-' . bin2hex(random_bytes(4));
        $this->storage = new Storage($this->dir);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            unlink($file);
        }
        if (is_dir($this->dir)) {
            rmdir($this->dir);
        }
    }

    /**
     * @param array<string, mixed> $post
     *
     * @return list<array{string, string}>
     */
    private function handle(array $post, mixed $file, bool $uploaded = true): array
    {
        return Page::handle($post, ['lb_image' => $file], $this->storage, static fn(): bool => $uploaded, ['LBL_LB_SAVED' => 'Image enregistrée']);
    }

    public function testDisplayDoesNothing(): void
    {
        self::assertSame([], $this->handle([], null));
        self::assertNull($this->storage->current());
    }

    public function testSaveKeepsTheImage(): void
    {
        $messages = $this->handle(['lb_save' => '1'], ['error' => UPLOAD_ERR_OK, 'tmp_name' => Images::png()]);

        self::assertSame([['notice', 'Image enregistrée']], $messages);
        self::assertSame('image/png', $this->storage->current()['type'] ?? null);
    }

    public function testSaveRefusesWhatIsNotAnImage(): void
    {
        $messages = $this->handle(['lb_save' => '1'], ['error' => UPLOAD_ERR_OK, 'tmp_name' => Images::fake()]);

        self::assertSame([['error', 'LBL_LB_BAD_TYPE']], $messages);
        self::assertNull($this->storage->current());
    }

    public function testSaveRefusesAFileNotJustSent(): void
    {
        $messages = $this->handle(['lb_save' => '1'], ['error' => UPLOAD_ERR_OK, 'tmp_name' => Images::png()], false);

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

        self::assertStringContainsString('index.php?entryPoint=loginBackground&amp;v=', $html);
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
