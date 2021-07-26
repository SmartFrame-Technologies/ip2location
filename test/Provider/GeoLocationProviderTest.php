<?php

declare(strict_types=1);

namespace SmartFrameTest\IP2Location\Provider;

use IP2Location\Database;
use PHPUnit\Framework\TestCase;
use SmartFrame\IP2Location\Model\GeoLocation;
use SmartFrame\IP2Location\Provider\GeoLocationProvider;

class GeoLocationProviderTest extends TestCase
{
    private static Database $IPv4Database;
    private static Database $IPv6Database;

    public static function setUpBeforeClass(): void
    {
        self::$IPv4Database = new Database(__DIR__ . '/../data/IP-COUNTRY-REGION-CITY-SAMPLE.BIN');
        self::$IPv6Database = new Database(__DIR__ . '/../data/IPV6-COUNTRY-REGION-CITY.SAMPLE.BIN');
    }

    public function testV4LookupSuccess(): void
    {
        $geoLocationProvider = new GeoLocationProvider(self::$IPv4Database, self::$IPv6Database);

        $result = $geoLocationProvider->lookup('5.34.22.41');
        self::assertInstanceOf(GeoLocation::class, $result);
        self::assertEquals('5.34.22.41', $result->getIpAddress());
        self::assertEquals(4, $result->getIpVersion());
        self::assertEquals('86119977', $result->getIpNumber());
        self::assertEquals('KZ', $result->getCountryCode());
        self::assertEquals('Kazakhstan', $result->getCountryName());
        self::assertEquals('Batys Qazaqstan oblysy', $result->getRegionName());
        self::assertEquals('Oral', $result->getCityName());
    }

    public function testV6LookupSuccess(): void
    {
        $geoLocationProvider = new GeoLocationProvider(self::$IPv4Database, self::$IPv6Database);

        $result = $geoLocationProvider->lookup('2A04:0100:0a00:0200:0000:0000:0000:4000');
        self::assertInstanceOf(GeoLocation::class, $result);
        self::assertEquals('2A04:0100:0a00:0200:0000:0000:0000:4000', $result->getIpAddress());
        self::assertEquals(6, $result->getIpVersion());
        self::assertEquals('55848365295905069167090858890880892928', $result->getIpNumber());
        self::assertEquals('UA', $result->getCountryCode());
        self::assertEquals('Ukraine', $result->getCountryName());
        self::assertEquals('Vinnytska oblast', $result->getRegionName());
        self::assertEquals('Vinnytsia', $result->getCityName());
    }

    public function testLookupWrongIP(): void
    {
        $countryCodeProvider = new GeoLocationProvider(self::$IPv4Database, self::$IPv6Database);

        self::assertNull($countryCodeProvider->lookup('not-ip-address'));
        self::assertNull($countryCodeProvider->lookup('000.0.0.0'));
    }
}