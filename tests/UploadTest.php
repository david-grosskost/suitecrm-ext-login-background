<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Storage;
use SuiteCrmExt\LoginBackground\Upload;

/** The file sent by the admin form, checked by its content. */
final class UploadTest extends TestCase
{
    private const array SENT = ['error' => UPLOAD_ERR_OK, 'tmp_name' => '/tmp/php1234'];

    public function testAPngIsAccepted(): void
    {
        self::assertSame(['type' => 'image/png'], Upload::check(self::SENT, Images::png()));
    }

    public function testAJpegIsAccepted(): void
    {
        self::assertSame(['type' => 'image/jpeg'], Upload::check(self::SENT, Images::jpeg()));
    }

    public function testATextNamedLikeAnImageIsRefused(): void
    {
        self::assertSame(['error' => 'LBL_LB_BAD_TYPE'], Upload::check(self::SENT, Images::fake()));
    }

    public function testAFileSuiteCrmRefusedIsNotKept(): void
    {
        self::assertSame(['error' => 'LBL_LB_UPLOAD_FAILED'], Upload::check(self::SENT, false));
    }

    public function testTooBigForTheExtension(): void
    {
        self::assertSame(['error' => 'LBL_LB_TOO_BIG'], Upload::check(self::SENT, str_repeat('x', Storage::MAX_BYTES + 1)));
    }

    public function testNoFileChosen(): void
    {
        self::assertSame(['error' => 'LBL_LB_NO_FILE'], Upload::check(['error' => UPLOAD_ERR_NO_FILE, 'tmp_name' => ''], false));
        self::assertSame(['error' => 'LBL_LB_NO_FILE'], Upload::check(null, false));
    }

    public function testTooBigForTheServer(): void
    {
        self::assertSame(['error' => 'LBL_LB_TOO_BIG'], Upload::check(['error' => UPLOAD_ERR_INI_SIZE, 'tmp_name' => ''], false));
        self::assertSame(['error' => 'LBL_LB_TOO_BIG'], Upload::check(['error' => UPLOAD_ERR_FORM_SIZE, 'tmp_name' => ''], false));
    }

    public function testAnotherUploadError(): void
    {
        self::assertSame(['error' => 'LBL_LB_UPLOAD_FAILED'], Upload::check(['error' => UPLOAD_ERR_PARTIAL, 'tmp_name' => ''], false));
    }
}
