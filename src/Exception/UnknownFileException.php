<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlToJsonConverter\Exception;

use BitAndBlack\IdmlToJsonConverter\Exception;

class UnknownFileException extends Exception
{
    /**
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        parent::__construct('Cannot handle file "' . $fileName . '" as it is an unknown part.');
    }
}
