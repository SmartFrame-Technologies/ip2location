<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location;


interface DownloaderInterface
{
    public function save(string $filePath);
}