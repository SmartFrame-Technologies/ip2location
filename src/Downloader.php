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

    public function fromIp2Location(string $path): void
    {
        $dir = dirname($path);
        foreach ($this->packages as $package) {
            if (strpos($path, $package['file']) !== false) {
                $this->download($dir, $package);
                $this->unzip($dir, $package);
                if ($this->fileCache) {
                    $this->fileCache->cloneFile($path);
                }

                return;
            }
        }
    }

    public function fromS3Cache(string $filePath): void
    {
        if (empty($this->fileCache) || !$this->fileCache->has($filePath)) {
            return;
        }
        file_put_contents($filePath, $this->fileCache->read($filePath)->getContents());
    }

    private function download(string $path, array $package): void
    {
        $this->client->request(
            'GET',
            sprintf('%s&file=%s', $this->downloadUrl, $package['code']),
            ['sink' => sprintf('%s/%s', $path, $package['name'])]
        );
        unset($client);
    }

    private function unzip(string $path, array $package): void
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
    }
}
