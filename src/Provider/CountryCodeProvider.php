<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location\Provider;

use IP2Location\Database;
use OutOfBoundsException;
use Throwable;

class CountryCodeProvider
{
    private Database $IPv4Database;

    private Database $IPv6Database;

    public function __construct(Database $IPv4Database, Database $IPv6Database)
    {
        $this->IPv4Database = $IPv4Database;
        $this->IPv6Database = $IPv6Database;
    }

    public function lookup(string $ip): ?string
    {
        try {
            if (preg_match('/^(?:[\d]{1,3}\.){3}[\d]{1,3}$/', $ip)) {
                $lookupResult = $this->IPv4Database->lookup($ip);
            } else {
                $lookupResult = $this->IPv6Database->lookup($ip);
            }
        } catch (Throwable $exception) {
            $lookupResult = null;
        }

        try {
            $countryCode = $this->fetchCountryCode($lookupResult);
        } catch (OutOfBoundsException $exception) {
            $countryCode = null;
        }

        return $countryCode;
    }

    private function fetchCountryCode($countryCode): string
    {
        if (is_array($countryCode) && isset($countryCode['countryCode']) && is_string($countryCode['countryCode'])) {
            return $countryCode['countryCode'];
        }
        throw new OutOfBoundsException('Country code for requested IP address not found');
    }
}
