<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;

use Aws\S3\S3Client;
use GuzzleHttp\Client;

class DatabaseExchangeFactory
{
    public static function create(
        string $downloadUrl,
        array $packages,
        array $s3CacheConfig = ['Client' => []]
    ): DatabaseExchange {
        $cache = new S3FileCache(new S3Client([$s3CacheConfig['Client']]), $s3CacheConfig);

        return new DatabaseExchange(
            new Downloader(new Client(), $cache, $downloadUrl, $packages),
            $cache
        );
    }
}
