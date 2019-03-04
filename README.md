# Files

Módulo para representação de arquivos


## Representações disponíveis

- Imagem
- PDF
- Unknown


## Exemplo de uso

```
<?php

use Ciebit\Files\Pdfs\Pdf;
use Ciebit\Files\Status;
use Ciebit\Files\Storages\Database\Sql;

$pdf = new Pdf('File Name', 'http://siteexample.com/file.pdf', Status::ACTIVE());

$storage = new Sql($pdo);
$storage->save($pdf);
```
