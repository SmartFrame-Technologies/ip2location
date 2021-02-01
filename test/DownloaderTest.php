<?php

declare(strict_types=1);

namespace SmartFrameTest\IP2Location;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SmartFrame\IP2Location\Downloader;

class DownloaderTest extends TestCase
{

    public function testDownload(): void
    {
        $mockZipFilename = 'tmp.zip';
        $mockFilename = 'db2.bin';
        $testZipFilePath = __DIR__ . '/data/test.zip';
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $mockZipFilename . '"'
            ], file_get_contents($testZipFilePath)),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $package = [['code' => 'code', 'name' => $mockZipFilename, 'file' => $mockFilename]];
        $downloader = new Downloader($client, '', $package);
        $downloader->fromIp2Location(__DIR__ . '/data/' . $mockFilename);

        self::assertFileExists(__DIR__ . '/data/' . $mockFilename);
    }
}
