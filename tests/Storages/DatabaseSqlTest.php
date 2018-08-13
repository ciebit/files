<?php
namespace Ciebit\Files\Test\Storages;

use Ciebit\Files\Collection;
use Ciebit\Files\Status;
use Ciebit\Files\File;
use Ciebit\Files\Storages\Database\Sql as DatabaseSql;
use Ciebit\Files\Test\Connection;

class DatabaseSqlTest extends Connection
{
    public function testGet(): void
    {
        $this->database = new DatabaseSql($this->getPdo());
        $file = $this->database->get();
        $this->assertInstanceOf(File::class, $file);
    }

    public function testGetFilterByStatus(): void
    {
        $this->database = new DatabaseSql($this->getPdo());
        $this->database->addFilterByStatus(Status::ACTIVE());
        $file = $this->database->get();
        $this->assertEquals(Status::ACTIVE(), $file->getStatus());
    }

    public function testGetFilterById(): void
    {
        $id = 2;
        $this->database = new DatabaseSql($this->getPdo());
        $this->database->addFilterById($id+0);
        $file = $this->database->get();
        $this->assertEquals($id, $file->getId());
    }

    public function testGetFilterByIds(): void
    {
        $id = 2;
        $this->database = new DatabaseSql($this->getPdo());
        $this->database->addFilterByIds('=', ...[2,3,4]);
        $files = $this->database->getAll();

        $filesArray = $files->getArrayObject();
        $this->assertEquals(2, $filesArray->offsetGet(0)->getId());
        $this->assertEquals(3, $filesArray->offsetGet(1)->getId());
        $this->assertEquals(4, $filesArray->offsetGet(2)->getId());
    }

    public function testGetAll(): void
    {
        $this->database = new DatabaseSql($this->getPdo());
        $files = $this->database->getAll();
        $this->assertInstanceOf(Collection::class, $files);
        $this->assertCount(4, $files->getIterator());
    }

    public function testGetAllFilterByStatus(): void
    {
        $this->database = new DatabaseSql($this->getPdo());
        $this->database->addFilterByStatus(Status::ACTIVE());
        $files = $this->database->getAll();
        $this->assertCount(1, $files->getIterator());
        $this->assertEquals(Status::ACTIVE(), $files->getArrayObject()->offsetGet(0)->getStatus());
    }

    public function testGetAllFilterById(): void
    {
        $id = 3;
        $this->database = new DatabaseSql($this->getPdo());
        $this->database->addFilterById($id+0);
        $files = $this->database->getAll();
        $this->assertCount(1, $files->getIterator());
        $this->assertEquals($id, $files->getArrayObject()->offsetGet(0)->getId());
    }
}
