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

    public function save(string $path): bool
    {
        foreach ($this->packages as $package) {
            if (strpos($path, $package['file']) !== false) {
                $this->download($path, $package);
                $this->unzip($path, $package);
                $this->fileCache->cloneFile($path . $package);

                return true;
            }
        }

        return false;
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

    public function downloadFromCache(string $filePath): bool
    {
        if (empty($this->fileCache)) {
            return false;
        }

        echo "\n";
        echo ' Exchange->getFromCache: Filepath: ';
        var_dump($filePath);
        echo "\n";

        //var_dump($this->fileCache->cloneFile($filePath));


        if ($this->fileCache->has($filePath)) {
            $fileFromCache = $this->fileCache->read($filePath);
            var_dump($fileFromCache);
            //@todo save file on host server
            return true;
        }

        return false;
    }
}
