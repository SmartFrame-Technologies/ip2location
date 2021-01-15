<?php

declare(strict_types=1);

namespace SmartFrameTest\IP2Location;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use SmartFrame\IP2Location\Downloader;

class DownloaderTest extends TestCase
{

    public function testDownload(): void
    {
        if (extension_loaded('zip') === false) {
            self::markTestSkipped('No ext/zip installed, skipping test.');
        }
        $mockFilename = 'db2.bin';
        $mockedZipFilePath = __DIR__ . '/data/test.zip';
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $mockFilename . '"'
            ], file_get_contents($mockedZipFilePath)),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $package = [['code' => 'code', 'name' => $mockFilename, 'file' => $mockFilename]];
        $downloader = new Downloader($client, '', $package);
        $downloader->fromIp2Location(__DIR__ . '/data/downloaded_' . $mockFilename);

        self::assertFileExists(__DIR__ . '/data/' . $mockFilename);
    }
}
