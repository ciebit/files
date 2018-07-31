<?php
declare(strict_types=1);

namespace Ciebit\Files\Builders;

trait setBasicAttributes
{
    public function setBasicAttributes(File $file, array $data): void
    {
        isset($this->data['date_hour'])
        && $file->setDateHour(new DateTime($this->data['date_hour']));

        isset($this->data['description'])
        && $file->setDescription((string) $this->data['description']);

        isset($this->data['extension'])
        && $file->setExtension($this->data['extension']);

        isset($this->data['id'])
        && $file->setId($this->data['id']);

        isset($this->data['size'])
        && $file->setSize($this->data['size']);

        isset($this->data['views'])
        && $file->setViews($this->data['views']);
    }
}
