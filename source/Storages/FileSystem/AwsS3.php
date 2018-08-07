<?php
namespace Ciebit\Files\Storages\FileSystem;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Exception;
use Ciebit\Files\Storages\FileSystem\FileSystem;

class AwsS3 implements FileSystem
{
    private $bucket; #string
    private $client; #S3Client
    private $credentials; #Credentials
    private $region; #string
    private $version; #string

    public function __construct(string $region, string $bucket, string $keyId, string $keySecret)
    {
        $this->bucket = $bucket;
        $this->credentials = new Credentials($keyId, $keySecret);
        $this->region = $region;
        $this->version = 'latest';

        $this->client = new S3CLient([
            'region' => $this->region,
            'version' => $this->version,
            'credentials' => $this->credentials
        ]);
    }

    public function has(string $fileName): bool
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $fileName
            ]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function save(string $filePath, string $fileName): FileSystem
    {
        $result = $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $fileName,
            'SourceFile' => $filePath,
        ]);

        return $this;
    }
}
