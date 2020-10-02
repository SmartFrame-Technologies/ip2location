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
        self::$IPv4Database = new Database(__DIR__ . '/../data/IP2LOCATION-LITE-DB1.BIN');
        self::$IPv6Database = new Database(__DIR__ . '/../data/IP2LOCATION-LITE-DB1.IPV6.BIN');
    }

    public function testLookupPL(): void
    {
        $countryCodeProvider = new CountryCodeProvider(self::$IPv4Database, self::$IPv6Database);

        self::assertEquals('PL', $countryCodeProvider->lookup('89.64.24.81'));
        self::assertEquals('PL', $countryCodeProvider->lookup('2a02:a317:4d3d:2b00:6419:2042:ffbb:5902'));
    }

    public function testLookupDE(): void
    {
        $countryCodeProvider = new CountryCodeProvider(self::$IPv4Database, self::$IPv6Database);

        self::assertEquals('DE', $countryCodeProvider->lookup('95.91.210.2'));
    }

    public function testLookupUK(): void
    {
        $countryCodeProvider = new CountryCodeProvider(self::$IPv4Database, self::$IPv6Database);

        self::assertEquals('GB', $countryCodeProvider->lookup('77.100.213.217'));
    }

    public function testLookupWrongIP(): void
    {
        $countryCodeProvider = new CountryCodeProvider(self::$IPv4Database, self::$IPv6Database);

        self::assertNull($countryCodeProvider->lookup('not-ip-address'));
        self::assertNull($countryCodeProvider->lookup('000.0.0.0'));
    }
}