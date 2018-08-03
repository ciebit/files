<?php
declare(strict_types=1);

namespace Ciebit\Files\Builders;

use Ciebit\Files\Builders\Builder;
use Ciebit\Files\File;
use Ciebit\Files\Status;
use DateTime;

class FromArray implements Builder
{
    private $data; #:array

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): File
    {
        if (
            ! is_array($this->data) OR
            ! isset($this->data['uri'])
        ) {
            throw new Exception('ciebit.files.builders.invalid', 3);
        }
        $file = new File(
            $this->data['name'],
            $this->data['uri'],
            $this->data['mimetype'],
            Status::DRAFT()
        );
        isset($this->data['date_hour'])
        && $file->setDateHour(new DateTime($this->data['date_hour']));
        isset($this->data['id'])
        && $file->setId((int) $this->data['id']);
        isset($this->data['extension'])
        && $file->setExtension((string) $this->data['extension']);
        isset($this->data['mimetype'])
        && $file->setMimetype((string) $this->data['mimetype']);
        isset($this->data['size'])
        && $file->setSize((float) $this->data['size']);
        isset($this->data['views'])
        && $file->setViews((int) $this->data['views']);
        isset($this->data['status'])
        && $file->setStatus(new Status((int) $this->data['status']));
        return $file;
    }
}
