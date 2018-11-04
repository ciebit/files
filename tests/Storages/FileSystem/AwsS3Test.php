<?php
namespace Ciebit\Files\Test\Storages\FileSystem;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Ciebit\Files\Storages\FileSystem\AwsS3;
use PHPUnit\Framework\TestCase;

use function parse_ini_file;

class AwsS3Test extends TestCase
{
    private $settings; # array

    protected function setUp(): void
    {
        $data = parse_ini_file(__DIR__.'/../../settings.ini', true);

        if (! isset($data['awsS3'])) {
            $this->markTestSkipped('Not done AWS S3 settings');
        }

        $this->settings = $data['awsS3'];
    }

    private function getCredentials(): Credentials
    {
        return new Credentials(
            $this->settings['keyId'],
            $this->settings['secretKey']
        );
    }

    private function getS3Client(): S3Client
    {
        return new S3Client([
            'region' => $this->settings['region'],
            'endpoint' => $this->settings['endpoint'],
            'version' => 'latest',
            'credentials' => $this->getCredentials()
        ]);
    }

    public function testSave(): void
    {
        $storage = new AwsS3($this->getS3Client(), $this->settings['bucket']);
        $storage->copy(__DIR__.'/../../data/image-01.jpg', 'tests/image-01.jpg');
        $this->assertTrue(true);
    }
}
