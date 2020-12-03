<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;

use GuzzleHttp\ClientInterface;
use RuntimeException;
use SmartFrame\IP2Location\Contracts\DownloaderInterface;
use SmartFrame\IP2Location\Contracts\FileCacheInterface;
use ZipArchive;

class Downloader implements DownloaderInterface
{
    private ClientInterface $client;
    private string $downloadUrl;
    private array $packages;
    private ?FileCacheInterface $fileCache;

    public function __construct(
        ClientInterface $client,
        string $downloadUrl,
        array $packages,
        ?FileCacheInterface $fileCache = null
    ) {
        $this->client = $client;
        $this->downloadUrl = $downloadUrl;
        $this->packages = $packages;
        $this->fileCache = $fileCache;
    }

    public function save(string $path): void
    {
        foreach ($this->packages as $package) {
            $this->download($path, $package);
            $this->unzip($path, $package);
            $this->fileCache->cloneFile($path . $package);
        }
    }

    public function download(string $path, array $package): void
    {
        $this->client->request(
            'GET',
            sprintf('%s&file=%s', $this->downloadUrl, $package['code']),
            ['sink' => sprintf('%s/%s', $path, $package['name'])]
        );
        unset($client);
    }

    public function unzip(string $path, array $package): void
    {
        $filePath = sprintf('%s/%s', $path, $package['name']);

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException('Cannot open ZIP file');
        }

        if ($zip->extractTo(dirname($filePath), $package['file']) !== true) {
            throw new RuntimeException(sprintf('Cannot extract file "%s" from ZIPArchive located in %s', $package['file'], $filePath));
        }
        $zip->close();
        unlink($filePath);
    }

    public function downloadFromCache(array $package): bool
    {
        //@todo get files from s3 and ut in on host server
        //$path->save();
        //$this->cache->read($path);
        if (empty($this->fileCache)) {
            return false;
        }

        return true;
    }
}
