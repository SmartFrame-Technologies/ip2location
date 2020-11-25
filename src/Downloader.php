<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;

use GuzzleHttp\ClientInterface;
use RuntimeException;
use ZipArchive;

class Downloader implements DownloaderInterface
{
    private ClientInterface $client;
    private string $downloadUrl;
    private array $packages;

    public function __construct(ClientInterface $client, string $downloadUrl, array $packages)
    {
        $this->client = $client;
        $this->downloadUrl = $downloadUrl;
        $this->packages = $packages;
    }

    public function save(string $path): void
    {
        foreach ($this->packages as $package) {
            $this->download($path, $package);
            $this->unzip($path, $package);
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
}
