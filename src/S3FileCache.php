<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;

use Aws\S3\S3ClientInterface;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use SmartFrame\IP2Location\Contracts\FileCacheInterface;

class S3FileCache implements FileCacheInterface
{
    private S3ClientInterface $s3Client;
    private string $bucket;
    private string $key;

    public function __construct(S3ClientInterface $s3Client, array $config)
    {
        $this->s3Client = $s3Client;
        $this->bucket = $config['Bucket'];
    }

    public function read(string $path): StreamInterface
    {
        $result = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $path,
        ]);
        var_dump($result);
        exit;

        /*
        $request = $this->s3Client->createPresignedRequest($cmd, '+1 minute');
        $url = $request->getUri()->__toString();
        try {
            return new Stream($url);
        } catch (\InvalidArgumentException $e) {
            throw new FileNotFoundException(
                sprintf('File %s not found in storage', $path), 0, $e
            );
        }
        */
    }

    public function write(
        string $path,
        Stream $stream,
        string $mimeType = 'application/octet-stream',
        $class = 'STANDARD'
    ): bool {
        $response = $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
            'Body' => (string) $stream,
            'ContentType' => $mimeType,
            'StorageClass' => $class
        ]);

        return $response->hasKey('ObjectURL');
    }

    public function has(string $path): bool
    {
        return $this->s3Client->doesObjectExist(
            $this->bucket,
            $path
        );
    }

    public function cloneFile(string $string): bool
    {
        $handler =  new Stream(fopen($string, 'rb+'));

    }
}
