<?php
namespace Ciebit\Files\Builders;

use Ciebit\Files\File;
use DateTime;

trait setBasicAttributes
{
    public function setBasicAttributes(File $file, array $data): void
    {
        isset($this->data['datetime'])
        && $file->setDateTime(new DateTime($this->data['datetime']));

        isset($this->data['description'])
        && $file->setDescription((string) $this->data['description']);

        isset($this->data['id'])
        && $file->setId((string) $this->data['id']);

        isset($this->data['labelsId'])
        && is_array($this->data['labelsId'])
        && $file->setLabelsId($this->data['labelsId']);

        isset($this->data['size'])
        && $file->setSize((float) $this->data['size']);

        isset($this->data['views'])
        && $file->setViews((int) $this->data['views']);
    }
}
