<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location\Contracts;

interface DownloaderInterface
{
    public function fromIp2Location(string $filePath): void;
    public function fromS3Cache(string $filePath): void;
}
