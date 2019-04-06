<?php
namespace Ciebit\Files\Test\Helpers;

use Ciebit\Files\Helpers\Replicator;
use Ciebit\Files\Test\Unknown\UnknownTest;
use Ciebit\Files\Test\Images\ImageTest;
use PHPUnit\Framework\TestCase;

class ReplicatorTest extends TestCase
{
    public function testReplicateUnknown(): void
    {
        $dataNewFile = [
            'name' => 'File Replicate',
            'id' => '',
            'size' => 512,
            'views' => 3,
            'datetime' => '2019-04-01 17:58:03'
        ];

        $unknown = UnknownTest::getInstance();
        $replicator = new Replicator;
        $unknown2 = $replicator->replicate($unknown, $dataNewFile);

        $this->assertEquals($dataNewFile['name'], $unknown2->getName());
        $this->assertEquals($dataNewFile['id'], $unknown2->getId());
        $this->assertEquals($dataNewFile['size'], $unknown2->getSize());
        $this->assertEquals($dataNewFile['views'], $unknown2->getViews());
        $this->assertEquals($dataNewFile['datetime'], $unknown2->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals($unknown->getMetadata(), $unknown->getMetadata());
        $this->assertEquals($unknown->getMimetype(), $unknown->getMimetype());
        $this->assertEquals($unknown->getStatus(), $unknown->getStatus());
    }

    public function testReplicateImage(): void
    {
        $dataNewImage = [
            'name' => 'File Replicate',
            'id' => '',
            'size' => 512,
            'width' => 400,
            'height' => 300,
            'views' => 3,
            'datetime' => '2019-04-01 17:58:03'
        ];

        $image = ImageTest::getInstance();
        $replicator = new Replicator;
        $image2 = $replicator->replicate($image, $dataNewImage);

        $this->assertEquals($dataNewImage['name'], $image2->getName());
        $this->assertEquals($dataNewImage['id'], $image2->getId());
        $this->assertEquals($dataNewImage['size'], $image2->getSize());
        $this->assertEquals($dataNewImage['width'], $image2->getWidth());
        $this->assertEquals($dataNewImage['height'], $image2->getHeight());
        $this->assertEquals($dataNewImage['views'], $image2->getViews());
        $this->assertEquals($dataNewImage['datetime'], $image2->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals($image->getMetadata(), $image->getMetadata());
        $this->assertEquals($image->getMimetype(), $image->getMimetype());
        $this->assertEquals($image->getStatus(), $image->getStatus());
    }
}
