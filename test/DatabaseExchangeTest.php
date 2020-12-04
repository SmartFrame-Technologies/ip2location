<?php

declare(strict_types=1);

namespace SmartFrameTest\IP2Location;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use SmartFrame\IP2Location\Contracts\DownloaderInterface;
use SmartFrame\IP2Location\DatabaseExchange;

class DatabaseExchangeTest extends TestCase
{

    public function testExchangeMissingFile(): void
    {
        $downloaderMock = $this->createMock(DownloaderInterface::class);
        $downloaderMock
            ->expects(self::once())
            ->method('fromIp2Location');

        $databaseExchange = new DatabaseExchange($downloaderMock);

        $root = vfsStream::setup('data');

        $databaseExchange->exchange($root->url() . 'IP-COUNTRY.BIN');
    }

    public function testExchangeOldFile(): void
    {
        $downloaderMock = $this->createMock(DownloaderInterface::class);
        $downloaderMock
            ->expects(self::once())
            ->method('fromIp2Location');

        $databaseExchange = new DatabaseExchange($downloaderMock);

        $root = vfsStream::setup('data');
        $file = new vfsStreamFile('IP-COUNTRY.BIN');
        $file->lastModified(time() - (DatabaseExchange::MAX_FILE_LIFETIME + 1));
        $root->addChild($file);

        $databaseExchange->exchange($root->getChild('IP-COUNTRY.BIN')->url());
    }

    public function testExchangeCorrectFile(): void
    {
        $downloaderMock = $this->createMock(DownloaderInterface::class);
        $downloaderMock
            ->expects(self::never())
            ->method('fromIp2Location');

        $databaseExchange = new DatabaseExchange($downloaderMock);

        $root = vfsStream::setup('data');
        $file = new vfsStreamFile('IP-COUNTRY.BIN');
        $root->addChild($file);

        $databaseExchange->exchange($root->getChild('IP-COUNTRY.BIN')->url());
    }
}
