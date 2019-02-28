<?php
namespace Ciebit\Files\Storages\Database;

use Ciebit\Files\Collection;
use Ciebit\Files\Builders\Context as Builder;
use Ciebit\Files\File;
use Ciebit\Files\Status;
use Ciebit\Files\Storages\Database\Database;
use Ciebit\Files\Storages\Storage;
use Ciebit\Labels\Storages\Storage as LabelStorage;
use Ciebit\SqlHelper\Sql as SqlHelper;
use DateTime;
use Exception;
use PDO;

use function array_map;
use function intval;

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
        $table = "`{$this->table}`";
        $tableAssociation = "`{$this->tableAssociationLabel}`";
        $fieldId = '`'. self::FIELD_ID .'`';
        $fieldFileId = '`'. self::FIELD_LABEL_FILE_ID .'`';
        $fieldLabelId = '`'. self::FIELD_LABEL_LABEL_ID .'`';

        $ids = array_map('intval', $ids);
        $this->addFilter("{$tableAssociation}.{$fieldLabelId}", PDO::PARAM_INT, $operator, ...$ids);

        $this->addSqlJoin(
            "INNER JOIN {$tableAssociation}
            ON {$tableAssociation}.{$fieldFileId} = {$table}.{$fieldId}"
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
        $statement = $this->pdo->prepare(
            "DELETE FROM {$this->table} WHERE `id` = :id"
        );

        $statement->bindValue(':id', $file->getId(), PDO::PARAM_INT);

        if (! $statement->execute()) {
            throw new Exception('ciebit.files.storages.destroy', 3);
        }

        unset($file);

        return $this;
    }

    /** @throws Exception */
    public function findAll(): Collection
    {
        $statement = $this->pdo->prepare("
            SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()}
            FROM {$this->table}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            {$this->sqlHelper->generateSqlLimit()}
        ");

        $this->sqlHelper->bind($statement);

        if ($statement->execute() === false) {
            throw new Exception('ciebit.stories.storages.get_error', 2);
        }

        $this->totalRecords = $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();

        $collection = new Collection;
        $builder = new Builder;

        while ($fileData = $statement->fetch(PDO::FETCH_ASSOC)) {
            $collection->add(
                $builder->setData($fileData)->build()
            );
        }

        return $collection;
    }

    /** @throws Exception */
    public function findOne(): ?File
    {
        $fieldId = self::FIELD_ID;
        $fieldFileId = self::FIELD_LABEL_FILE_ID;
        $fieldLabelId = self::FIELD_LABEL_LABEL_ID;

        $statement = $this->pdo->prepare(
            "SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()},
            GROUP_CONCAT(`{$this->tableAssociationLabel}`.`{$fieldLabelId}`) as `labels_id`
            FROM `{$this->table}`
            LEFT JOIN `{$this->tableAssociationLabel}`
            ON `{$this->tableAssociationLabel}`.`{$fieldFileId}` = `{$this->table}`.`{$fieldId}`
            {$this->sqlHelper->generateSqlJoin()}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            GROUP BY `{$this->table}`.`{$fieldId}`
            LIMIT 1"
        );

        $this->sqlHelper->bind($statement);

        if ($statement->execute() === false) {
            throw new Exception('ciebit.files.storages.get_error', 2);
        }

        $this->totalRecords = $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();

        $fileData = $statement->fetch(PDO::FETCH_ASSOC);
        if ($fileData == false) {
            return null;
        }

        $file = (new Builder)->setData($fileData)->build();
        $labelsId = explode(',', $fileData['labels_id']);
        if (count($labelsId) > 0) {
            $labels = (clone $this->labelStorage)->addFilterById('=', ...$labelsId)->findAll();
            $file->setLabels($labels);
        }

        return $file;
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

    /** @throws Exception */
    public function update(File $file): Storage
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
}
