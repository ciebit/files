<?php
namespace Ciebit\Files\Storages\FileSystem;

use Aws\S3\S3Client;
use Exception;
use Ciebit\Files\Storages\FileSystem\FileSystem;

class AwsS3 implements FileSystem
{
    private $bucket; # string
    private $s3Client; # S3Client

    public function __construct(S3CLient $client, string $bucket)
    {
        $this->bucket = $bucket;
        $this->s3Client = $client;
    }

    public function has(string $fileName): bool
    {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $fileName
            ]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function copy(string $origin, string $destiny): FileSystem
    {
        $result = $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $destiny,
            'SourceFile' => $origin,
            'ACL' => 'public-read'
        ]);

        return $this;
    }
}
