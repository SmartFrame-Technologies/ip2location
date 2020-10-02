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
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="file.zip"'
            ], 'zip content'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $root = vfsStream::setup('data');
        $url = 'http://localhost';
        $package = ['code' => 'code', 'name' => 'file.zip'];

        $downloader = new Downloader($client, $url, []);
        $downloader->download($root->url(), $package);

        self::assertTrue($root->hasChild('file.zip'));
    }
}
