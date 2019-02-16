<?php
namespace Ciebit\Files\Test;

use ArrayObject;
use ArrayIterator;
use Ciebit\Files\Collection;
use Ciebit\Files\File;
use Ciebit\Files\Images\Image;
use Ciebit\Files\Status;
use PHPUnit\Framework\TestCase;
use TypeError;

class CollectionTest extends TestCase
{
    public function getFile(): File
    {
        return new Image('Name 01', 'image/jpg', 'url-1.jpg', 1000, 600, new Status(Status::ACTIVE));
    }

    public function testAdd(): void
    {
        $collection = new Collection;
        $collection->add($this->getFile());
        $collection->add($this->getFile(), $this->getFile());

        $this->assertTrue(true);
    }

    public function testAddInvalid(): void
    {
        $this->expectException(TypeError::class);
        $collection = new Collection;
        $collection->add('blue');
    }

    public function testCount(): void
    {
        $collection = new Collection;
        $collection->add($this->getFile(), $this->getFile(), $this->getFile());
        $this->assertCount(3, $collection);
    }

    public function testGetArrayObject(): void
    {
        $collection = new Collection;
        $this->assertInstanceOf(ArrayObject::class, $collection->getArrayObject());
    }

    public function testGetById(): void
    {
        $collection = (new Collection)
        ->add($this->getFile()->setId('1'))
        ->add($this->getFile()->setId('2'))
        ->add($this->getFile()->setId('3'))
        ;

        $this->assertEquals('2', $collection->getById('2')->getId());
    }

    public function testGetIterator(): void
    {
        $collection = new Collection;
        $this->assertInstanceOf(ArrayIterator::class, $collection->getIterator());
    }
}
