<?php

declare(strict_types=1);

namespace SmartFrame\IP2Location\Contracts;

use GuzzleHttp\Psr7\Stream;

interface FileCacheInterface
{
    public function read(string $path); //@todo add result type
    public function has(string $path): bool;
    public function write(
        string $path,
        Stream $stream,
        string $mimeType = 'application/octet-stream',
        $class = 'STANDARD'
    ): bool;
    public function cloneFile(string $string): bool;
}
