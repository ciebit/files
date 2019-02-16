<?php
namespace Ciebit\Files\Test\Storages\Database;

use Ciebit\Files\Collection;
use Ciebit\Files\File;
use Ciebit\Files\Pdfs\Pdf;
use Ciebit\Files\Status;
use Ciebit\Files\Storages\Database\Sql;
use Ciebit\Files\Test\BuildPdo;
use PHPUnit\Framework\TestCase;

class SqlTest extends TestCase
{
    public function testFind(): void
    {
        $database = new Sql(BuildPdo::build());
        $file = $database->findOne();
        $this->assertInstanceOf(File::class, $file);
    }

    public function testFindWithFilterByStatus(): void
    {
        $database = new Sql(BuildPdo::build());
        $database->addFilterByStatus('=', Status::ACTIVE());
        $file = $database->findOne();
        $this->assertEquals(Status::ACTIVE(), $file->getStatus());
    }

    public function testFindWithFilterById(): void
    {
        $id = 2;
        $database = new Sql(BuildPdo::build());
        $database->addFilterById('=', $id+0);
        $file = $database->findOne();
        $this->assertEquals($id, $file->getId());
    }

    public function testFindWithFilterByMultiplesIds(): void
    {
        $database = new Sql(BuildPdo::build());
        $database->addFilterById('=', ...[2,3,4]);
        $files = $database->findAll();

        $filesArray = $files->getArrayObject();
        $this->assertEquals(2, $filesArray->offsetGet(0)->getId());
        $this->assertEquals(3, $filesArray->offsetGet(1)->getId());
        $this->assertEquals(4, $filesArray->offsetGet(2)->getId());
    }

    public function testFindAll(): void
    {
        $database = new Sql(BuildPdo::build());
        $files = $database->findAll();
        $this->assertInstanceOf(Collection::class, $files);
        $this->assertCount(4, $files);
    }

    public function testFindAllFilterByStatus(): void
    {
        $database = new Sql(BuildPdo::build());
        $database->addFilterByStatus('=', Status::ACTIVE());
        $files = $database->findAll();
        $this->assertCount(2, $files->getIterator());
        $this->assertEquals(Status::ACTIVE(), $files->getArrayObject()->offsetGet(0)->getStatus());
    }

    public function testFindAllFilterById(): void
    {
        $id = 3;
        $database = new Sql(BuildPdo::build());
        $database->addFilterById('=', $id+0);
        $files = $database->findAll();
        $this->assertCount(1, $files->getIterator());
        $this->assertEquals($id, $files->getArrayObject()->offsetGet(0)->getId());
    }

    public function testStorage(): void
    {
        $pdf = new Pdf('Name File', 'url-file.pdf', Status::ACTIVE());
        $storage = new Sql(BuildPdo::build());
        $storage->store($pdf);
        $this->assertTrue($pdf->getId() > 0);
    }
}
