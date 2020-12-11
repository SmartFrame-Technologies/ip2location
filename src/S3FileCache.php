<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;

use Aws\S3\S3ClientInterface;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use SmartFrame\IP2Location\Contracts\FileCacheInterface;
use SmartFrame\IP2Location\Exception\EmptyFileException;

class S3FileCache implements FileCacheInterface
{
    private S3ClientInterface $s3Client;
    private string $bucket;
    private string $prefix;

    public function __construct(S3ClientInterface $s3Client, string $bucket, string $prefix)
    {
        $this->s3Client = $s3Client;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
    }

    public function read(string $path): StreamInterface
    {
        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $this->prefix . $path,
        ]);
        $request = $this->s3Client->createPresignedRequest($cmd, '+1 minute');

        return new Stream(fopen((string) $request->getUri(), 'rb'));
    }

    public function write(
        string $path,
        Stream $stream,
        string $mimeType = 'application/octet-stream',
        $class = 'STANDARD'
    ): bool {
        $response = $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $this->prefix . $path,
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
            $this->prefix . $path
        );
    }

    public function cloneFile(string $path): bool
    {
        $file = new Stream(fopen($path, 'rb+'));
        if (!$file) {
            throw new EmptyFileException('Source file not found!');
        }

        return $this->write($path, $file);
    }
}
