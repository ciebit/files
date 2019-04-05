<?php
namespace Ciebit\Files\Test\Unknown;

use Ciebit\Files\File;
use Ciebit\Files\Unknown\Unknown;
use Ciebit\Files\Status;
use PHPUnit\Framework\TestCase;
use DateTime;

class UnknownTest extends TestCase
{
    /** @var string */
    private const ID = '7';

    /** @var string */
    private const MIMETYPE = 'application/ogg';

    /** @var array */
    private const METADATA = [];

    /** @var string */
    private const NAME = 'File Name';

    /** @var int */
    private const SIZE = 1024;

    /** @var string */
    private const URL = 'url.ogg';

    /** @var int */
    private const VIEWS = 2;


    public function testCreateManual(): void
    {
        $dateTime = new DateTime;

        $unknown = (new Unknown(self::NAME, self::URL, self::MIMETYPE, Status::ACTIVE()))
        ->setDateTime(clone $dateTime)
        ->setId(self::ID)
        ->setSize(self::SIZE)
        ->setViews(self::VIEWS)
        ;

        $this->assertInstanceOf(File::class, $unknown);
        $this->assertEquals(self::METADATA, $unknown->getMetadata());
        $this->assertEquals(self::NAME, $unknown->getName());
        $this->assertEquals(self::URL, $unknown->getUrl());
        $this->assertEquals(Status::ACTIVE(), $unknown->getStatus());
        $this->assertEquals($dateTime, $unknown->getDateTime());
        $this->assertEquals(self::ID, $unknown->getId());
        $this->assertEquals(self::SIZE, $unknown->getSize());
        $this->assertEquals(self::VIEWS, $unknown->getViews());
    }
}
