<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location\Provider;

use IP2Location\Database;

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
        $result = $this->IPv4Database->lookup($ip);

        if (is_array($result) && isset($result['countryCode'])) {
            return $result['countryCode'];
        }

        $result = $this->IPv6Database->lookup($ip);

        if (is_array($result) && isset($result['countryCode'])) {
            return $result['countryCode'];
        }

        return null;
    }
}