<?php
namespace Ciebit\Files\Test\Images;

use Ciebit\Files\File;
use Ciebit\Files\Images\Image;
use Ciebit\Files\Images\Variations\Collection as VariationsCollection;
use Ciebit\Files\Status;
use Ciebit\Labels\Collection as LabelsCollection;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;

class ImageTest extends TestCase
{
    /** @var int */
    private const HEIGHT = 600;

    /** @var string */
    private const ID = '7';

    /** @var array */
    private const LABELS_ID = ['1', '2'];

    /** @var string */
    private const MIMETYPE = 'image/png';

    /** @var string */
    private const NAME = 'File Name';

    /** @var int */
    private const SIZE = 1024;

    /** @var string */
    private const URL = 'url.png';

    /** @var int */
    private const WIDTH = 1000;

    /** @var int */
    private const VIEWS = 2;


    public function testCreateManual(): void
    {
        $dateTime = new DateTime;

        $image = (new Image(self::NAME, self::URL, self::MIMETYPE, self::WIDTH, self::HEIGHT, Status::ACTIVE()))
        ->setDateTime(clone $dateTime)
        ->setId(self::ID)
        ->setSize(self::SIZE)
        ->setVariations(new VariationsCollection)
        ->setLabelsId(self::LABELS_ID)
        ->setViews(self::VIEWS)
        ;

        $this->assertInstanceOf(File::class, $image);
        $this->assertEquals(self::NAME, $image->getName());
        $this->assertEquals(self::URL, $image->getUrl());
        $this->assertEquals(Status::ACTIVE(), $image->getStatus());
        $this->assertEquals($dateTime, $image->getDateTime());
        $this->assertEquals(self::ID, $image->getId());
        $this->assertEquals(self::WIDTH, $image->getWidth());
        $this->assertEquals(self::HEIGHT, $image->getHeight());
        $this->assertEquals(self::SIZE, $image->getSize());
        $this->assertEquals(
            [
                'height' => self::HEIGHT,
                'width' => self::WIDTH,
                'variations' => []
            ],
            $image->getMetadata()
        );
        $this->assertInstanceOf(VariationsCollection::class, $image->getVariations());
        $this->assertEquals(self::LABELS_ID, $image->getLabelsId());
        $this->assertEquals(self::VIEWS, $image->getViews());
    }
}
