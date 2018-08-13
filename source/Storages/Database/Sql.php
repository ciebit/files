<?php
declare(strict_types=1);
namespace Ciebit\Files\Storages\Database;

use Ciebit\Files\Collection;
use Ciebit\Files\Builders\Context as Builder;
use Ciebit\Files\File;
use Ciebit\Files\Status;
use Ciebit\Files\Storages\Storage;
use Ciebit\Files\Storages\Database\SqlFilters;
use Exception;
use PDO;

class Sql extends SqlFilters implements Database
{
    static private $counterKey = 0;
    private $pdo; #PDO
    private $table; #string

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->table = 'cb_files';
    }

    public function addFilterById(int $id, string $operator = '='): Storage
    {
        $key = 'id';
        $sql = "`file`.`id` $operator :{$key}";
        $this->addfilter($key, $sql, PDO::PARAM_INT, $id);
        return $this;
    }

    public function addFilterByIds(string $operator, int ...$ids): Storage
    {
        $keyPrefix = 'id';
        $keys = [];
        $operator = $operator == '!=' ? 'NOT IN' : 'IN';

        foreach ($ids as $id) {
            $key = $keyPrefix . self::$counterKey++;
            $this->addBind($key, PDO::PARAM_INT, $id);
            $keys[] = $key;
        }

        $keysStr = implode(', :', $keys);
        $this->addSqlFilter("`id` {$operator} (:{$keysStr})");

        return $this;
    }

    public function addFilterByStatus(Status $status, string $operator = '='): Storage
    {
        $key = 'status';
        $sql = "`file`.`status` {$operator} :{$key}";
        $this->addFilter($key, $sql, PDO::PARAM_INT, $status->getValue());
        return $this;
    }

    public function get(): ?File
    {
        $statement = $this->pdo->prepare("
            SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()}
            FROM {$this->table} as `file`
            WHERE {$this->generateSqlFilters()}
            LIMIT 1
        ");
        $this->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.stories.storages.database.get_error', 2);
        }
        $fileData = $statement->fetch(PDO::FETCH_ASSOC);
        if ($fileData == false) {
            return null;
        }
        return (new Builder)->setData($fileData)->build();
    }

    public function getAll(): Collection
    {
        $statement = $this->pdo->prepare("
            SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()}
            FROM {$this->table} as `file`
            WHERE {$this->generateSqlFilters()}
            {$this->generateSqlLimit()}
        ");
        $this->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.stories.storages.database.get_error', 2);
        }
        $collection = new Collection;
        $builder = new Builder;
        while ($file = $statement->fetch(PDO::FETCH_ASSOC)) {
            $builder->setData($file);
            $collection->add(
                $builder->build()
            );
        }
        return $collection;
    }

    private function getFields(): string
    {
        return '
            `file`.`id`,
            `file`.`name`,
            `file`.`description`,
            `file`.`uri`,
            `file`.`extension`,
            `file`.`size`,
            `file`.`views`,
            `file`.`mimetype`,
            `file`.`date_hour`,
            `file`.`metadata`,
            `file`.`status`
        ';
    }

    public function getTotalRows(): int
    {
        return $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
    }

    public function setStartingLine(int $lineInit): Storage
    {
        parent::setOffset($lineInit);
        return $this;
    }

    public function setTable(string $name): self
    {
        $this->table = $name;
        return $this;
    }

    public function setTotalLines(int $total): Storage
    {
        parent::setLimit($total);
        return $this;
    }
}
