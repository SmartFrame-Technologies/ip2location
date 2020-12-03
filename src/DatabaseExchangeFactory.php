<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;

use Aws\S3\S3Client;
use GuzzleHttp\Client;

class DatabaseExchangeFactory
{
    public static function create(
        string $downloadUrl,
        array $packages
    ): DatabaseExchange {
        return new DatabaseExchange(
            new Downloader(new Client(), $downloadUrl, $packages)
        );
    }

    public static function createWithS3Cache(
        string $downloadUrl,
        array $packages,
        S3Client $s3Client,
        array $s3Config
    ): DatabaseExchange {
        $cache = new S3FileCache($s3Client, $s3Config);
        return new DatabaseExchange(
            new Downloader(new Client(), $downloadUrl, $packages, $cache),
            $cache
        );
    }

}
