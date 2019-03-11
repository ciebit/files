<?php
namespace Ciebit\Files\Storages\Database;

use Ciebit\Files\Collection;
use Ciebit\Files\Builders\Context as Builder;
use Ciebit\Files\File;
use Ciebit\Files\Status;
use Ciebit\Files\Storages\Database\Database;
use Ciebit\Files\Storages\Storage;
use Ciebit\Labels\Collection as LabelsCollection;
use Ciebit\Labels\Storages\Storage as LabelStorage;
use Ciebit\SqlHelper\Sql as SqlHelper;
use DateTime;
use Exception;
use PDO;

use function array_column;
use function array_map;
use function array_merge;
use function array_unique;
use function explode;
use function intval;
use function is_array;

class Sql implements Database
{
    /** @var string */
    private const FIELD_ID = 'id';

    /** @var string */
    private const FIELD_LABEL_FILE_ID = 'file_id';

    /** @var string */
    private const FIELD_LABEL_ID = 'id';

    /** @var string */
    private const FIELD_LABEL_LABEL_ID = 'label_id';

    /** @var string */
    private const FIELD_DATETIME = 'datetime';

    /** @var string */
    private const FIELD_DESCRIPTION = 'description';

    /** @var string */
    private const FIELD_METADATA = 'metadata';

    /** @var string */
    private const FIELD_MIMETYPE = 'mimetype';

    /** @var string */
    private const FIELD_NAME = 'name';

    /** @var string */
    private const FIELD_SIZE = 'size';

    /** @var string */
    private const FIELD_STATUS = 'status';

    /** @var string */
    private const FIELD_URL = 'url';

    /** @var string */
    private const FIELD_VIEWS = 'views';

    /** @var int **/
    static private $counterKey = 0;

    /** @var LabelStorage */
    private $labelStorage;

    /** @var PDO */
    private $pdo;

    /** @var SqlHelper */
    private $sqlHelper;

    /** @var string */
    private $table;

    /** @var string */
    private $tableAssociationLabel;

    /** @var int */
    private $totalRecords;

    public function __construct(PDO $pdo, LabelStorage $labelStorage)
    {
        $this->labelStorage = $labelStorage;
        $this->pdo = $pdo;
        $this->sqlHelper = new SqlHelper;
        $this->table = 'cb_files';
        $this->tableAssociationLabel = 'cb_files_labels';
        $this->totalRecords = 0;
    }

    public function __clone()
    {
        $this->sqlHelper = clone $this->sqlHelper;
    }

    private function addFilter(string $fieldName, int $type, string $operator, ...$value): self
    {
        $field = "`{$this->table}`.`{$fieldName}`";
        $this->sqlHelper->addFilterBy($field, $type, $operator, ...$value);
        return $this;
    }

    public function addFilterByDateTime(string $operator, DateTime ...$dateTime): Storage
    {
        $dateTimeString = array_map(
            function($item){ return $item->format('Y-m-d H:i:s'); },
            $dateTime
        );
        $this->addFilter(self::FIELD_DATETIME, PDO::PARAM_STR, $operator, ...$dateTimeString);
        return $this;
    }

    public function addFilterByDescription(string $operator, string ...$descriptions): Storage
    {
        $this->addFilter(self::FIELD_DESCRIPTION, PDO::PARAM_STR, $operator, ...$descriptions);
        return $this;
    }

    public function addFilterById(string $operator, string ...$ids): Storage
    {
        $ids = array_map('intval', $ids);
        $this->addFilter(self::FIELD_ID, PDO::PARAM_INT, $operator, ...$ids);
        return $this;
    }

    public function addFilterByLabelId(string $operator, string ...$ids): Storage
    {
        $tableAssociation = "`{$this->tableAssociationLabel}`";
        $fieldLabelId = '`'. self::FIELD_LABEL_LABEL_ID .'`';
        $fieldFileId = '`'. self::FIELD_LABEL_FILE_ID .'`';
        $fieldId = '`'. self::FIELD_ID .'`';

        $ids = array_map('intval', $ids);
        $this->sqlHelper->addFilterBy("{$tableAssociation}.{$fieldLabelId}", PDO::PARAM_INT, $operator, ...$ids);
        $this->sqlHelper->addSqlJoin(
            "INNER JOIN {$this->tableAssociationLabel}
            ON {$this->tableAssociationLabel}.{$fieldFileId} = {$this->table}.{$fieldId}"
        );
        return $this;
    }

    public function addFilterByMimetype(string $operator, string ...$mimetypes): Storage
    {
        $this->addFilter(self::FIELD_MIMETYPE, PDO::PARAM_STR, $operator, ...$mimetypes);
        return $this;
    }

    public function addFilterByName(string $operator, string ...$names): Storage
    {
        $this->addFilter(self::FIELD_NAME, PDO::PARAM_STR, $operator, ...$names);
        return $this;
    }

    public function addFilterBySize(string $operator, int ...$sizes): Storage
    {
        $this->addFilter(self::FIELD_SIZE, PDO::PARAM_INT, $operator, ...$sizes);
        return $this;
    }

    public function addFilterByStatus(string $operator, Status ...$status): Storage
    {
        $statusInt = array_map(function($status){
            return (int) $status->getValue();
        }, $status);
        $this->addFilter(self::FIELD_STATUS, PDO::PARAM_INT, $operator, ...$statusInt);
        return $this;
    }

    public function addFilterByUrl(string $operator, string ...$urls): Storage
    {
        $this->addFilter(self::FIELD_URL, PDO::PARAM_STR, $operator, ...$urls);
        return $this;
    }

    public function addFilterByViews(string $operator, int ...$views): Storage
    {
        $this->addFilter(self::FIELD_VIEWS, PDO::PARAM_INT, $operator, ...$views);
        return $this;
    }

    /** @throws Exception */
    public function destroy(File $file): Storage
    {
        try {
            $this->pdo->beginTransaction();
            $this->destroyAssociationLabels($file);
            $this->destroyFile($file);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return $this;
    }

    private function destroyAssociationLabels(File $file): self
    {
        $fieldFileId = self::FIELD_LABEL_FILE_ID;

        $statement = $this->pdo->prepare(
            "DELETE FROM {$this->tableAssociationLabel} WHERE `{$fieldFileId}` = :id"
        );

        $statement->bindValue(':id', $file->getId(), PDO::PARAM_INT);

        if (! $statement->execute()) {
            throw new Exception('ciebit.files.storages.destroy', 3);
        }

        return $this;

    }

    private function destroyFile(File $file): self
    {
        $fieldId = self::FIELD_ID;

        $statement = $this->pdo->prepare(
            "DELETE FROM {$this->table} WHERE `{$fieldId}` = :id"
        );

        $statement->bindValue(':id', $file->getId(), PDO::PARAM_INT);

        if (! $statement->execute()) {
            throw new Exception('ciebit.files.storages.destroy', 3);
        }

        unset($file);

        return $this;
    }

    private function extractLabelsId(array $data): array
    {
        $labelsId = [];

        foreach ($data as $ids) {
            $labelsId = array_merge($labelsId, explode(',', $ids));
        }

        return array_unique($labelsId);
    }

    /** @throws Exception */
    public function findAll(): Collection
    {
        $fieldId = self::FIELD_ID;
        $fieldFileId = self::FIELD_LABEL_FILE_ID;
        $fieldLabelId = self::FIELD_LABEL_LABEL_ID;

        $statement = $this->pdo->prepare(
            $sql = "SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()},
            (
                SELECT GROUP_CONCAT(`{$this->tableAssociationLabel}`.`{$fieldLabelId}`)
                FROM  `{$this->tableAssociationLabel}`
                WHERE `{$this->tableAssociationLabel}`.`{$fieldFileId}` = `{$this->table}`.`{$fieldId}`
            )  as `labels_id`
            FROM `{$this->table}`
            {$this->sqlHelper->generateSqlJoin()}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            GROUP BY `{$this->table}`.`{$fieldId}`
            {$this->sqlHelper->generateSqlLimit()}
        ");

        $this->sqlHelper->bind($statement);

        if ($statement->execute() === false) {
            echo $sql;
            throw new Exception('ciebit.stories.storages.get_error', 2);
        }

        $this->totalRecords = $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();

        $fileData = $statement->fetchAll(PDO::FETCH_ASSOC);
        $labelsId = $this->extractLabelsId(array_column($fileData, 'labels_id'));
        if (! empty($labelsId)) {
            $labels = (clone $this->labelStorage)->addFilterById('=', ...$labelsId)->findAll();
        }

        $collection = new Collection;
        $builder = new Builder;

        foreach ($fileData as $data) {
            $file = $builder->setData($data)->build();
            $collection->add($file);

            if (isset($labels) && ! empty($data['labels_id'])) {
                $dataLabelsId = explode(',', $data['labels_id']);
                $labelsCollection = new LabelsCollection;
                foreach ($dataLabelsId as $labelId) {
                    $labelsCollection->add($labels->getById($labelId));
                }

                $file->setLabels($labelsCollection);
            }
        }

        return $collection;
    }

    /** @throws Exception */
    public function findOne(): ?File
    {
        $storage = clone $this;
        $labels = $storage->setLimit(1)->findAll();

        if (count($labels) == 0) {
            return null;
        }

        return $labels->getArrayObject()->offsetGet(0);
    }

    private function getFields(): string
    {
        return "`{$this->table}`.`". self::FIELD_ID .'`,'
            . "`{$this->table}`.`". self::FIELD_NAME .'`,'
            . "`{$this->table}`.`". self::FIELD_DESCRIPTION .'`,'
            . "`{$this->table}`.`". self::FIELD_URL .'`,'
            . "`{$this->table}`.`". self::FIELD_SIZE .'`,'
            . "`{$this->table}`.`". self::FIELD_VIEWS .'`,'
            . "`{$this->table}`.`". self::FIELD_MIMETYPE .'`,'
            . "`{$this->table}`.`". self::FIELD_DATETIME .'`,'
            . "`{$this->table}`.`". self::FIELD_METADATA .'`,'
            . "`{$this->table}`.`". self::FIELD_STATUS .'`';
    }

    public function getTotalRecords(): int
    {
        return $this->totalRecords;
    }

    /** @throws Exception */
    public function save(File $file): Storage
    {
        if ($file->getId() > 0) {
            return $this->update($file);
        }

        return $this->store($file);
    }

    public function setLimit(int $limit): Storage
    {
        $this->sqlHelper->setLimit($limit);
        return $this;
    }

    public function setOffset(int $offset): Storage
    {
        $this->sqlHelper->setOffset($offset);
        return $this;
    }

    public function setTable(string $name): self
    {
        $this->table = $name;
        return $this;
    }

    public function setTableAssociationLabel(string $name): self
    {
        $this->$tableAssociationLabel = $name;
        return $this;
    }

    /** @throws Exception */
    public function store(File $file): Storage
    {
        try {
            $this->pdo->beginTransaction();
            $this->storeFile($file);
            $this->storeAssociationLabels($file);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $file->setId('');
            throw $e;
        }

        return $this;
    }

    /** @throws Exception */
    public function storeFile(File $file): self
    {
        $fieldName = self::FIELD_NAME;
        $fieldDescription = self::FIELD_DESCRIPTION;
        $fieldUrl = self::FIELD_URL;
        $fieldSize = self::FIELD_SIZE;
        $fieldViews = self::FIELD_VIEWS;
        $fieldMimetype = self::FIELD_MIMETYPE;
        $fieldDateTime = self::FIELD_DATETIME;
        $fieldMetadata = self::FIELD_METADATA;
        $fieldStatus = self::FIELD_STATUS;

        $statement = $this->pdo->prepare(
            "INSERT INTO {$this->table} (
                `{$fieldName}`, `{$fieldDescription}`, `{$fieldUrl}`, `{$fieldSize}`,
                `{$fieldViews}`, `{$fieldMimetype}`, `{$fieldDateTime}`,
                `{$fieldMetadata}`, `{$fieldStatus}`
            ) VALUES (
                :name, :description, :url, :size,
                :views, :mimetype, :datetime,
                :metadata, :status
            )"
        );

        $statement->bindValue(':name', $file->getName(), PDO::PARAM_STR);
        $statement->bindValue(':description', $file->getDescription(), PDO::PARAM_STR);
        $statement->bindValue(':url', $file->getUrl(), PDO::PARAM_STR);
        $statement->bindValue(':size', $file->getSize(), PDO::PARAM_INT);
        $statement->bindValue(':views', $file->getViews(), PDO::PARAM_INT);
        $statement->bindValue(':mimetype', $file->getMimetype(), PDO::PARAM_STR);
        $statement->bindValue(':datetime', $file->getDateTime()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':metadata', $file->getMetadata(), PDO::PARAM_STR);
        $statement->bindValue(':status', $file->getStatus()->getValue(), PDO::PARAM_INT);

        if (! $statement->execute()) {
            throw new Exception('ciebit.files.storages.store', 3);
        }

        $file->setId($this->pdo->lastInsertId());

        return $this;
    }

    private function storeAssociationLabels(File $file): self
    {
        $totalLabels = count($file->getLabels());
        if ($totalLabels <= 0) {
            return $this;
        }

        $values = [];
        for ($i=0; $i < $totalLabels; $i++) {
            $values[] = "(:file_id, :label_id_{$i})";
        }

        $fieldFileId = self::FIELD_LABEL_FILE_ID;
        $fieldLabelId = self::FIELD_LABEL_LABEL_ID;

        $statement = $this->pdo->prepare(
            "INSERT INTO {$this->tableAssociationLabel} (
                `{$fieldFileId}`, `{$fieldLabelId}`
            ) VALUES ". implode(',', $values)
        );

        $statement->bindValue(':id', $file->getId(), PDO::PARAM_INT);
        $statement->bindValue(':file_id', $file->getId(), PDO::PARAM_INT);

        $labelsList = $file->getLabels()->getArrayObject();
        for ($i=0; $labelsList->offsetExists($i); $i++) {
            $statement->bindValue(
                ":label_id_{$i}",
                $labelsList->offsetGet($i)->getId(),
                PDO::PARAM_INT
            );
        }

        if (! $statement->execute()) {
            throw new Exception('ciebit.files.storages.store', 3);
        }

        return $this;
    }

    /** @throws Exception */
    public function update(File $file): Storage
    {
        try {
            $this->pdo->beginTransaction();
            $this->updateFile($file);
            $this->updateAssociationLabels($file);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return $this;
    }

    /** @throws Exception */
    private function updateFile(File $file): self
    {
        $fieldId = self::FIELD_ID;
        $fieldName = self::FIELD_NAME;
        $fieldDescription = self::FIELD_DESCRIPTION;
        $fieldUrl = self::FIELD_URL;
        $fieldSize = self::FIELD_SIZE;
        $fieldViews = self::FIELD_VIEWS;
        $fieldMimetype = self::FIELD_MIMETYPE;
        $fieldDateTime = self::FIELD_DATETIME;
        $fieldMetadata = self::FIELD_METADATA;
        $fieldStatus = self::FIELD_STATUS;

        $statement = $this->pdo->prepare(
            "UPDATE {$this->table}
            SET
                `{$fieldName}` = :name,
                `{$fieldDescription}` = :description,
                `{$fieldUrl}` = :url,
                `{$fieldSize}` = :size,
                `{$fieldViews}` = :views,
                `{$fieldMimetype}` = :mimetype,
                `{$fieldDateTime}` = :datetime,
                `{$fieldMetadata}` = :metadata,
                `{$fieldStatus}` = :status
            WHERE
                `{$fieldId}` = :id
            LIMIT 1"
        );

        $statement->bindValue(':id', $file->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name', $file->getName(), PDO::PARAM_STR);
        $statement->bindValue(':description', $file->getDescription(), PDO::PARAM_STR);
        $statement->bindValue(':url', $file->getUrl(), PDO::PARAM_STR);
        $statement->bindValue(':size', $file->getSize(), PDO::PARAM_INT);
        $statement->bindValue(':views', $file->getViews(), PDO::PARAM_INT);
        $statement->bindValue(':mimetype', $file->getMimetype(), PDO::PARAM_STR);
        $statement->bindValue(':datetime', $file->getDateTime()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':metadata', $file->getMetadata(), PDO::PARAM_STR);
        $statement->bindValue(':status', $file->getStatus()->getValue(), PDO::PARAM_INT);

        if (! $statement->execute()) {
            throw new Exception('ciebit.files.storages.update', 4);
        }

        return $this;
    }

    /** @throws Exception */
    private function updateAssociationLabels(File $file): self
    {
        $this->destroyAssociationLabels($file);
        $this->storeAssociationLabels($file);
        return $this;
    }
}
