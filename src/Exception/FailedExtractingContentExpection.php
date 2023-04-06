<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlJsonConverter\Exception;

use BitAndBlack\IdmlJsonConverter\Exception;
use Throwable;

class FailedExtractingContentExpection extends Exception
{
    public function __construct(string $reason, int $code = 0, ?Throwable $previousException = null)
    {
        parent::__construct($reason, $code, $previousException);
    }

    public static function phpMemory(): FailedExtractingContentExpection
    {
        return new self('Not possible to use "php://memory".', 1);
    }

    public static function fileTooLarge(Throwable $previousException): FailedExtractingContentExpection
    {
        return new self('IDML file is too large. Use `setArchiveOptions` to configure the file handling by yourself.', 2, $previousException);
    }

    public static function cannotReadFromStream(): FailedExtractingContentExpection
    {
        return new self('Cannot read from stream.', 3);
    }
}
