<?php
namespace Ciebit\Files\Test\Images\Variations;

use ArrayIterator;
use ArrayObject;
use Ciebit\Files\Images\Variations\Variation;
use Ciebit\Files\Images\Variations\Collection;
use PHPUnit\Framework\TestCase;
use TypeError;

use function json_encode;

class CollectionTest extends TestCase
{
    /** @var int */
    private const HEIGHT = 600;

    /** @var int */
    private const SIZE = 1024;

    /** @var string */
    private const URL = 'url.jpg';

    /** @var int */
    private const WIDTH = 1000;

    public function getVariation(): Variation
    {
        return new Variation(self::URL, self::WIDTH, self::HEIGHT, self::SIZE);
    }

    public function testAdd(): void
    {
        $collection = new Collection;
        $collection->add('larger', $this->getVariation());
        $collection->add('thumbnail', new Variation('url2.jpg', 100, 300, 500));

        $variation = $collection->findByKey('larger');

        $this->assertInstanceOf(Variation::class, $variation);
        $this->assertEquals(self::HEIGHT, $variation->getHeight());
    }

    public function testAddInvalidKey(): void
    {
        $this->expectException(TypeError::class);
        $collection = new Collection;
        $collection->add(null, $this->getVariation());
    }

    public function testAddInvalidValue(): void
    {
        $this->expectException(TypeError::class);
        $collection = new Collection;
        $collection->add('larger', 'color');
    }

    public function testCount(): void
    {
        $collection = new Collection;
        $collection->add('larger', $this->getVariation());
        $collection->add('thumbnail', $this->getVariation());

        $this->assertCount(2, $collection);
    }

    public function testFindByKeyNotFound(): void
    {
        $collection = new Collection;
        $collection->add('larger', $this->getVariation());
        $collection->add('thumbnail', $this->getVariation());

        $this->assertNull($collection->findByKey('unknown'));
    }

    public function testGetArrayObject(): void
    {
        $collection = new Collection;
        $this->assertInstanceOf(ArrayObject::class, $collection->getArrayObject());
    }

    public function testGetIterator(): void
    {
        $collection = new Collection;
        $this->assertInstanceOf(ArrayIterator::class, $collection->getIterator());
    }

    public function testJsonSerialize(): void
    {
        $data = [
            'larger' => [
                'url' => 'larger.png',
                'width' => 600,
                'height' => 300,
                'size' => 800
            ],
            'thumbnail' => [
                'url' => 'thumbnail.png',
                'width' => 300,
                'height' => 200,
                'size' => 600
            ]
        ];

        $collection = new Collection;
        $collection->add('larger', new Variation(
            $data['larger']['url'],
            $data['larger']['width'],
            $data['larger']['height'],
            $data['larger']['size']
        ));
        $collection->add('thumbnail', new Variation(
            $data['thumbnail']['url'],
            $data['thumbnail']['width'],
            $data['thumbnail']['height'],
            $data['thumbnail']['size']
        ));

        $this->assertJsonStringEqualsJsonString(json_encode($data), json_encode($collection));
    }
}
