<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;

use SmartFrame\IP2Location\Contracts\FileCacheInterface;
use SmartFrame\IP2Location\Contracts\DownloaderInterface;

class DatabaseExchange
{
    /**
     * file lifetime in seconds (33 days)
     */
    public const MAX_FILE_LIFETIME = 33 * 24 * 3600;

    private DownloaderInterface $downloader;
    private FileCacheInterface $fileCache;

    public function __construct(DownloaderInterface $downloader, FileCacheInterface $fileCache)
    {
        $this->downloader = $downloader;
        $this->fileCache = $fileCache;
    }

    public function exchange(string $filePath, $useCache = false): void
    {
        if (!file_exists($filePath) || (time() - filemtime($filePath)) > self::MAX_FILE_LIFETIME) {
            if ($useCache && $this->fileCache->has($filePath)) {
                $fileFromCache = $this->fileCache->read($filePath);
                var_dump($fileFromCache);
                //@todo save file on server
            }
            $this->downloader->save(dirname($filePath));
        }
    }
}