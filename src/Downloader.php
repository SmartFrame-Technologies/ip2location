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

    public function fromIp2Location(string $path): bool
    {
        $dir = dirname($path);
        foreach ($this->packages as $package) {
            if (strpos($path, $package['file']) !== false) {
                //@todo temp do not download and unzip
                //$this->download($dir, $package);
                //$this->unzip($dir, $package);
                $this->fileCache->cloneFile($path . $package);

                return true;
            }
        }

        return false;
    }

    public function fromS3Cache(string $filePath): bool
    {
        if (empty($this->fileCache)) {
            return false;
        }
        if ($this->fileCache->has($filePath)) {
            $fileFromCache = $this->fileCache->read($filePath);
            var_dump($fileFromCache);
            exit;
            return true;
        }

        return false;
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
        //@todo leave file for test reason
        // unlink($filePath);
    }
}
