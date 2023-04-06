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

class CannotReadFileException extends Exception
{
    public function __construct(string $fileName)
    {
        parent::__construct('Cannot read file "' . $fileName . '".');
    }
}
