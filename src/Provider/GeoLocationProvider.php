<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location\Provider;

use IP2Location\Database;
use OutOfBoundsException;
use SmartFrame\IP2Location\Model\GeoLocation;
use Throwable;

class GeoLocationProvider
{
    private Database $IPv4Database;

    private Database $IPv6Database;

    private array $lookupFields = [
        Database::IP_ADDRESS,
        Database::IP_VERSION,
        Database::IP_NUMBER,
        Database::REGION_NAME,
        Database::CITY_NAME,
        Database::COUNTRY,
    ];

    public function __construct(Database $IPv4Database, Database $IPv6Database)
    {
        $this->IPv4Database = $IPv4Database;
        $this->IPv6Database = $IPv6Database;
    }

    public function lookup(string $ip): ?GeoLocation
    {
        try {
            if (preg_match('/^(?:[\d]{1,3}\.){3}[\d]{1,3}$/', $ip)) {
                $lookupResult = $this->IPv4Database->lookup($ip, $this->lookupFields);
            } else {
                $lookupResult = $this->IPv6Database->lookup($ip, $this->lookupFields);
            }
        } catch (Throwable $exception) {
            $lookupResult = null;
        }

        try {
            $geoLocation = $this->fetchGeoLocation($lookupResult);
        } catch (OutOfBoundsException $exception) {
            $geoLocation = null;
        }

        return $geoLocation;
    }

    private function fetchGeoLocation($response): GeoLocation
    {
        if (
            is_array($response) &&
            $this->isValidKey($response, 'ipNumber') &&
            $this->isValidKey($response, 'ipVersion') &&
            $this->isValidKey($response, 'ipAddress') &&
            $this->isValidKey($response, 'countryCode') &&
            $this->isValidKey($response, 'countryName')
        ) {
            return new GeoLocation(
                $response['ipNumber'],
                (int)$response['ipVersion'],
                $response['ipAddress'],
                $response['countryCode'],
                $response['countryName'],
                $response['regionName'],
                $response['cityName']
            );
        }
        throw new OutOfBoundsException('GeoLocation for requested IP address not found');
    }

    private function isValidKey(array $array, string $key): bool
    {
        return array_key_exists($key, $array) && (is_string($array[$key]) | is_int($array[$key]));
    }
}