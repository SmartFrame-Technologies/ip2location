<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location\Model;

class GeoLocation
{
    private string $ipNumber;
    private int $ipVersion;
    private string $ipAddress;

    private string $countryCode;
    private string $countryName;
    private ?string $regionName;
    private ?string $cityName;

    public function __construct(
        string $ipNumber,
        int $ipVersion,
        string $ipAddress,
        string $countryCode,
        string $countryName,
        ?string $regionName,
        ?string $cityName
    )
    {
        $this->ipNumber = $ipNumber;
        $this->ipVersion = $ipVersion;
        $this->ipAddress = $ipAddress;
        $this->countryCode = $countryCode;
        $this->countryName = $countryName;
        $this->regionName = $regionName;
        $this->cityName = $cityName;
    }

    public function getIpNumber(): string
    {
        return $this->ipNumber;
    }

    public function getIpVersion(): int
    {
        return $this->ipVersion;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCountryName(): string
    {
        return $this->countryName;
    }

    public function getRegionName(): ?string
    {
        return $this->regionName;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }
}
