<?php
namespace Ciebit\Files\Test\Pdfs;

use Ciebit\Files\File;
use Ciebit\Files\Pdfs\Pdf;
use Ciebit\Files\Status;
use PHPUnit\Framework\TestCase;
use DateTime;

class PdfTest extends TestCase
{
    /** @var string */
    private const ID = '2';

    /** @var string */
    private const NAME = 'File Name';

    /** @var int */
    private const SIZE = 1024;

    /** @var string */
    private const URL = 'url.pdf';

    /** @var int */
    private const VIEWS = 3;


    public function testCreateManual(): void
    {
        $dateTime = new DateTime;

        $pdf = (new Pdf(self::NAME, self::URL, Status::ACTIVE()))
        ->setDateTime(clone $dateTime)
        ->setId(self::ID)
        ->setSize(self::SIZE)
        ->setViews(self::VIEWS)
        ;

        $this->assertInstanceOf(File::class, $pdf);
        $this->assertEquals(self::NAME, $pdf->getName());
        $this->assertEquals(self::URL, $pdf->getUrl());
        $this->assertEquals(Status::ACTIVE(), $pdf->getStatus());
        $this->assertEquals($dateTime, $pdf->getDateTime());
        $this->assertEquals(self::ID, $pdf->getId());
        $this->assertEquals(self::SIZE, $pdf->getSize());
        $this->assertEquals(self::VIEWS, $pdf->getViews());
    }
}
