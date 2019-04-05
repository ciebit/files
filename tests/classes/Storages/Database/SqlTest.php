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
use Ciebit\Labels\Collection as LabelsCollection;
use Ciebit\Labels\Label;
use Ciebit\Labels\Status as LabelStatus;
use Ciebit\Labels\Storages\Database\Sql as LabelSql;
use DateTime;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

class SqlTest extends TestCase
{
    /** @var array */
    private static $sqlData;

    public static function setUpBeforeClass(): void
    {
        self::$sqlData = array_filter(explode(";", file_get_contents(__DIR__.'/../../../../database/data-example.sql')));
    }

    private function getImage(): Image
    {
        $image = (new Image('Image Name File', 'url-image.png', 'image/png', 1000, 600, Status::ACTIVE()))
        ->setDateTime(new DateTime('2019-02-18 09:29:00'))
        ->setDescription('Description image')
        ->setSize(1442)
        ->setViews(23)
        ->setId('7');

        $variations = (new VariationsCollection)
        ->add('thumbnail', new Variation('url-image-thumbnail.png', 300, 200, 612))
        ->add('larger', new Variation('url-image-larger.png', 600, 300, 879))
        ;

        $image->setVariations($variations);

        return $image;
    }

    private function getPdf(): Pdf
    {
        return (new Pdf('PDF Name File', 'url-file.pdf', Status::ACTIVE()))
        ->setDateTime(new DateTime('2019-02-18 09:21:00'))
        ->setDescription('Description file PDF')
        ->setSize(2048)
        ->setViews(33)
        ->setId('5');
    }

    private function getDatabase(): Sql
    {
        $pdo = BuildPdo::build();
        $labelStorage = new LabelSql($pdo);
        return new Sql($pdo, $labelStorage);
    }

    private function setDatabaseDefault(): void
    {
        $pdo = BuildPdo::build();
        $pdo->exec('DELETE FROM `cb_files`');
        $pdo->exec('DELETE FROM `cb_files_labels`');
        foreach (self::$sqlData as $sql) {
            $pdo->exec($sql);
        }
    }

    protected function setUp(): void
    {
        $this->setDatabaseDefault();
    }

    public function testDestroy(): void
    {
        $database = $this->getDatabase();
        $unknown = new Unknown('File Name', 'file-url', 'audio/mp3', Status::ACTIVE());
        $unknown->setId('1');
        $database->destroy($unknown);
        $file = $database->addFilterById('=', '1')->findOne();
        $this->assertNull($file);
    }

    public function testFind(): void
    {
        $database = $this->getDatabase();
        $file = $database->findOne();
        $this->assertInstanceOf(File::class, $file);
    }

    public function testFindDataIntegrity(): void
    {
        $database = $this->getDatabase();
        $file = $database->findOne();
        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals(1, $file->getId());
        $this->assertEquals('Title File 1', $file->getName());
        $this->assertEquals('Description File 1', $file->getDescription());
        $this->assertEquals('url-file-1.jpg', $file->getUrl());
        $this->assertEquals(10, $file->getSize());
        $this->assertEquals(0, $file->getViews());
        $this->assertEquals('image/jpg', $file->getMimetype());
        $this->assertEquals('2018-05-26 10:33:22', $file->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(3, $file->getStatus()->getValue());
        $this->assertEquals(600, $file->getWidth());
        $this->assertEquals(150, $file->getHeight());
        $this->assertEquals('1', $file->getLabelsId()[0]);
        $this->assertEquals('2', $file->getLabelsId()[1]);
    }

    public function testFindWithFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::ACTIVE());
        $file = $database->findOne();
        $this->assertEquals(Status::ACTIVE(), $file->getStatus());
    }

    public function testFindWithFilterById(): void
    {
        $id = 2;
        $database = $this->getDatabase();
        $database->addFilterById('=', $id+0);
        $file = $database->findOne();
        $this->assertEquals($id, $file->getId());
    }

    public function testFindWithOrderBy(): void
    {
        $database = $this->getDatabase();
        $database->addOrderBy('id', 'DESC');
        $file = $database->findOne();
        $this->assertEquals(4, $file->getId());
    }

    public function testFindWithFilterByLabelId(): void
    {
        $id = 2;
        $database = $this->getDatabase();
        $database->addFilterByLabelId('=', $id+0);
        $file = $database->findOne();
        $this->assertEquals(1, $file->getId());
    }

    public function testFindWithFilterByMultiplesIds(): void
    {
        $database = $this->getDatabase();
        $database->addFilterById('=', ...[2,3,4]);
        $files = $database->findAll();

        $filesArray = $files->getArrayObject();
        $this->assertEquals(2, $filesArray->offsetGet(0)->getId());
        $this->assertEquals(3, $filesArray->offsetGet(1)->getId());
        $this->assertEquals(4, $filesArray->offsetGet(2)->getId());
    }

    public function testFindAll(): void
    {
        $database = $this->getDatabase();
        $files = $database->findAll();
        $this->assertInstanceOf(Collection::class, $files);
        $this->assertCount(4, $files);
    }

    public function testFindAllFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::ACTIVE());
        $files = $database->findAll();
        $this->assertCount(2, $files->getIterator());
        $this->assertEquals(Status::ACTIVE(), $files->getArrayObject()->offsetGet(0)->getStatus());
    }

    public function testFindAllFilterById(): void
    {
        $id = 3;
        $database = $this->getDatabase();
        $database->addFilterById('=', $id+0);
        $files = $database->findAll();
        $this->assertCount(1, $files->getIterator());
        $this->assertEquals($id, $files->getArrayObject()->offsetGet(0)->getId());
    }

    public function testIsolation(): void
    {
        $database1 = $this->getDatabase();
        $database2 = clone $database1;

        $database1->addFilterByUrl('=', 'test');
        $database2->addFilterById('=', 3, 4);
        $files = $database2->findAll();
        $this->assertCount(2, $files->getIterator());
    }

    public function testFindAllFilterByLabelId(): void
    {
        $id = 1;
        $database = $this->getDatabase();
        $database->addFilterByLabelId('=', $id+0);
        $files = $database->findAll();
        $this->assertCount(1, $files);
        $file = $files->getArrayObject()->offsetGet(0);
        $this->assertEquals(1, $file->getId());
        $this->assertCount(2, $file->getLabelsId());
    }

    public function testGetTotalItemsOfLastFindWithoutLimitations(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByUrl('LIKE', '%.jpg')
        ->setLimit(1);

        $file = $database->findAll();
        $this->assertCount(1, $file);
        $this->assertEquals(3, $database->getTotalItemsOfLastFindWithoutLimitations());
    }

    public function testSave(): void
    {
        $image1 = $this->getImage()->setId('2');

        $storage = $this->getDatabase();
        $storage->save(clone $image1);
        $image2 = $storage->addFilterById('=', $image1->getId())->findOne();
        $this->assertEquals($image1, $image2);

        $pdf1 = $this->getPdf()->setId('');
        $storage = $this->getDatabase();
        $storage->save($pdf1);
        $pdf2 = $storage->addFilterById('=', $pdf1->getId())->findOne();
        $this->assertEquals($pdf1, $pdf2);
    }

    public function testStorageImage(): void
    {
        $image1 = $this->getImage();

        $storage = $this->getDatabase();
        $storage->store($image1);

        $image2 = $storage->addFilterById('=', $image1->getId())->findOne();

        $this->assertEquals($image1, $image2);
    }

    public function testStoragePdf(): void
    {
        $pdf = $this->getPdf();

        $storage = $this->getDatabase();
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

        $storage = $this->getDatabase();
        $storage->store($unknownFile1);

        $unknownFile2 = $storage->addFilterById('=', $unknownFile1->getId())->findOne();

        $this->assertEquals($unknownFile1, $unknownFile2);
    }

    public function testStorageUnknowWithLabels(): void
    {
        $labels = (new LabelsCollection)
        ->add(
            (new Label(
                'Label 1',
                'label-1',
                LabelStatus::ACTIVE()
            ))->setId('1')
        )->add(
            (new Label(
                'Label 2',
                'label-2',
                LabelStatus::ACTIVE()
            ))->setId('2')
        );

        $unknownFile1 = (new Unknown('Unknown Name File', 'url-file.pdf', 'audio/aac', Status::ACTIVE()))
        ->setDateTime(new DateTime('2019-02-18 09:23:00'))
        ->setDescription('Description file Unknown')
        ->setSize(1024)
        ->setViews(12)
        ->setId(6)
        ->setLabels($labels);

        $storage = $this->getDatabase();
        $storage->store($unknownFile1);

        $unknownFile2 = $storage->addFilterById('=', $unknownFile1->getId())->findOne();

        $this->assertEquals($unknownFile1, $unknownFile2);
    }

    public function testUpdate(): void
    {
        $database = $this->getDatabase();
        $unknown = new Unknown('File Name', 'file-url.mp3', 'audio/mp3', Status::ACTIVE());
        $unknown
        ->setDescription('Description Unknown File')
        ->setSize(2000)
        ->setViews(50)
        ->setDateTime(new DateTime('2019-02-20 10:24:00'))
        ->setId('2')
        ->setLabels(
            (new LabelsCollection)
            ->add(
                (new Label(
                    'Label 3',
                    'label-3',
                    LabelStatus::ACTIVE()
                ))->setId('3')
            )->add(
                (new Label(
                    'Label 4',
                    'label-4',
                    LabelStatus::ACTIVE()
                ))->setId('4')
            )
        );

        $database->update(clone $unknown);
        $file = $database->addFilterById('=', '2')->findOne();
        $this->assertEquals($unknown, $file);
    }
}
