<?php
namespace Ciebit\Files\Test\Storages\Database;

use Ciebit\Files\Collection;
use Ciebit\Files\File;
use Ciebit\Files\Images\Image;
use Ciebit\Files\Images\Variations\Collection as VariationsCollection;
use Ciebit\Files\Images\Variations\Variation;
use Ciebit\Files\Pdfs\Pdf;
use Ciebit\Files\Unknown\Unknown;
use Ciebit\Files\Status;
use Ciebit\Files\Storages\Database\Sql;
use Ciebit\Files\Test\BuildPdo;
use DateTime;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

class SqlTest extends TestCase
{
    private function setDatabaseDefault(): void
    {
        $pdo = $database = BuildPdo::build();
        $pdo->query('DELETE FROM `cb_files`');
        $pdo->query(file_get_contents(__DIR__.'/../../../../database/data-example.sql'));
    }

    public function testDestroy(): void
    {
        $this->setDatabaseDefault();
        $database = new Sql(BuildPdo::build());
        $unknown = new Unknown('File Name', 'file-url', 'audio/mp3', Status::ACTIVE());
        $unknown->setId('1');
        $database->destroy($unknown);
        $file = $database->addFilterById('=', '1')->findOne();
        $this->assertNull($file);
    }

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
        $this->setDatabaseDefault();
        $database = new Sql(BuildPdo::build());
        $files = $database->findAll();
        $this->assertInstanceOf(Collection::class, $files);
        $this->assertCount(4, $files);
    }

    public function testFindAllFilterByStatus(): void
    {
        $this->setDatabaseDefault();
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

    public function testStorageImage(): void
    {
        $image1 = (new Image('Image Name File', 'url-image.png', 'image/png', 1000, 600, Status::ACTIVE()))
        ->setDateTime(new DateTime('2019-02-18 09:29:00'))
        ->setDescription('Description image')
        ->setSize(1442)
        ->setViews(23)
        ->setId(7);

        $variations = (new VariationsCollection)
        ->add('thumbnail', new Variation('url-image-thumbnail.png', 300, 200, 612))
        ->add('larger', new Variation('url-image-larger.png', 600, 300, 879))
        ;

        $image1->setVariations($variations);

        $this->setDatabaseDefault();
        $storage = new Sql(BuildPdo::build());
        $storage->store($image1);

        $image2 = $storage->addFilterById('=', $image1->getId())->findOne();

        $this->assertEquals($image1, $image2);
    }

    public function testStoragePdf(): void
    {
        $pdf = (new Pdf('PDF Name File', 'url-file.pdf', Status::ACTIVE()))
        ->setDateTime(new DateTime('2019-02-18 09:21:00'))
        ->setDescription('Description file PDF')
        ->setSize(2048)
        ->setViews(33)
        ->setId(5);

        $this->setDatabaseDefault();
        $storage = new Sql(BuildPdo::build());
        $storage->store($pdf);

        $pdf2 = $storage->addFilterById('=', $pdf->getId())->findOne();

        $this->assertEquals($pdf, $pdf2);
    }

    public function testStorageUnknow(): void
    {
        $unknownFile1 = (new Unknown('Unknown Name File', 'url-file.pdf', 'audio/aac', Status::ACTIVE()))
        ->setDateTime(new DateTime('2019-02-18 09:23:00'))
        ->setDescription('Description file Unknown')
        ->setSize(1024)
        ->setViews(12)
        ->setId(6);

        $this->setDatabaseDefault();
        $storage = new Sql(BuildPdo::build());
        $storage->store($unknownFile1);

        $unknownFile2 = $storage->addFilterById('=', $unknownFile1->getId())->findOne();

        $this->assertEquals($unknownFile1, $unknownFile2);
    }
}
