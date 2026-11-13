<?php

declare(strict_types=1);

namespace SuiteCrmExt\LoginBackground\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\LoginBackground\Upload;

/** The file sent by the admin form, checked by its content. */
final class UploadTest extends TestCase
{
    public function testAPngIsAccepted(): void
    {
        self::assertSame(['type' => 'image/png'], Upload::check(['error' => UPLOAD_ERR_OK, 'tmp_name' => Images::png()]));
    }

    public function testAJpegIsAccepted(): void
    {
        self::assertSame(['type' => 'image/jpeg'], Upload::check(['error' => UPLOAD_ERR_OK, 'tmp_name' => Images::jpeg()]));
    }

    public function testATextNamedLikeAnImageIsRefused(): void
    {
        self::assertSame(['error' => 'LBL_LB_BAD_TYPE'], Upload::check(['error' => UPLOAD_ERR_OK, 'tmp_name' => Images::fake()]));
    }

    public function testNoFileChosen(): void
    {
        self::assertSame(['error' => 'LBL_LB_NO_FILE'], Upload::check(['error' => UPLOAD_ERR_NO_FILE, 'tmp_name' => '']));
        self::assertSame(['error' => 'LBL_LB_NO_FILE'], Upload::check(null));
    }

    public function testTooBigForTheServer(): void
    {
        self::assertSame(['error' => 'LBL_LB_TOO_BIG'], Upload::check(['error' => UPLOAD_ERR_INI_SIZE, 'tmp_name' => '']));
        self::assertSame(['error' => 'LBL_LB_TOO_BIG'], Upload::check(['error' => UPLOAD_ERR_FORM_SIZE, 'tmp_name' => '']));
    }

    public function testAnotherUploadError(): void
    {
        self::assertSame(['error' => 'LBL_LB_UPLOAD_FAILED'], Upload::check(['error' => UPLOAD_ERR_PARTIAL, 'tmp_name' => '']));
    }
}
