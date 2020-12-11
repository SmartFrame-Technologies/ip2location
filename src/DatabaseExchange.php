<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;

use SmartFrame\IP2Location\Contracts\DownloaderInterface;

class DatabaseExchange
{
    /**
     * file lifetime in seconds (33 days)
     */
    public const MAX_FILE_LIFETIME = 33 * 24 * 3600;

    private DownloaderInterface $downloader;

    public function __construct(DownloaderInterface $downloader)
    {
        $this->downloader = $downloader;
    }

    public function exchange(string $filePath): void
    {
        if ($this->isDatabaseOutdated($filePath)) {
            $this->downloader->fromS3Cache($filePath);
            if ($this->isDatabaseOutdated($filePath)) {
                $this->downloader->fromIp2Location($filePath);
            }
        }
    }

    private function isDatabaseOutdated(string $filePath): bool
    {
        return !file_exists($filePath) || (time() - filemtime($filePath)) > self::MAX_FILE_LIFETIME;
    }
}
