<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlJsonConverter;

use Stringable;

readonly class MemoryLimit implements Stringable
{
    private string $memoryLimit;

    public function __construct()
    {
        $memoryLimit = ini_get('memory_limit');

        if (false === $memoryLimit) {
            $memoryLimit = '0';
        }

        $this->memoryLimit = $memoryLimit;
    }

    public function __toString(): string
    {
        return $this->getMemoryLimit();
    }

    public function getMemoryLimit(): string
    {
        return $this->memoryLimit;
    }

    public function getMemoryLimitInBytes(): float
    {
        $memoryLimit = $this->getMemoryLimit();

        if (preg_match('/^(\d+)(.)$/', $memoryLimit, $matches)) {
            if ('M' === $matches[2]) {
                $memoryLimit = (float) $matches[1] * 1024 * 1024;
            } elseif ('K' === $matches[2]) {
                $memoryLimit = (float) $matches[1] * 1024;
            }
        }

        return (float) $memoryLimit;
    }
}
