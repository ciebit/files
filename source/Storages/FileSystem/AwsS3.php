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
    private $endpoint; #string
    private $region; #string
    private $version; #string

    public function __construct(string $region, string $bucket, string $keyId, string $keySecret)
    {
        $this->bucket = $bucket;
        $this->credentials = new Credentials($keyId, $keySecret);
        $this->endpoint = '';
        $this->region = $region;
        $this->version = 'latest';
    }

    private function getS3Client(): S3Client
    {
        $settings = [
            'region' => $this->region,
            'version' => $this->version,
            'credentials' => $this->credentials
        ];

        if ($this->endpoint != null) {
            $settings['endpoint'] = $this->endpoint;
        }

        return new S3CLient($settings);
    }

    public function has(string $fileName): bool
    {
        $client = $this->getS3Client();

        try {
            $result = $client->getObject([
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
        $client = $this->getS3Client();

        $result = $client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $fileName,
            'SourceFile' => $filePath,
            'ACL' => 'public-read'
        ]);

        return $this;
    }

    public function setEndpoint(string $url): self
    {
        $this->endpoint = $url;
        return $this;
    }
}
