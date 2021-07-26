<?php

declare(strict_types=1);

namespace SmartFrameTest\IP2Location\Provider;

use IP2Location\Database;
use PHPUnit\Framework\TestCase;
use SmartFrame\IP2Location\Provider\CountryCodeProvider;

class CountryCodeProviderTest extends TestCase
{
    private static Database $IPv4Database;
    private static Database $IPv6Database;

    public static function setUpBeforeClass(): void
    {
        self::$IPv4Database = new Database(__DIR__ . '/../data/IP-COUNTRY-REGION-CITY-SAMPLE.BIN');
        self::$IPv6Database = new Database(__DIR__ . '/../data/IPV6-COUNTRY-REGION-CITY.SAMPLE.BIN');
    }

    public function testLookupSuccess(): void
    {
        $countryCodeProvider = new CountryCodeProvider(self::$IPv4Database, self::$IPv6Database);

        self::assertEquals('PL', $countryCodeProvider->lookup('89.64.24.81'));
        self::assertEquals('UA', $countryCodeProvider->lookup('2A04:0100:0a00:0200:0000:0000:0000:4000'));
    }

    public function testLookupWrongIP(): void
    {
        $countryCodeProvider = new CountryCodeProvider(self::$IPv4Database, self::$IPv6Database);

        self::assertNull($countryCodeProvider->lookup('not-ip-address'));
        self::assertNull($countryCodeProvider->lookup('000.0.0.0'));
    }
}