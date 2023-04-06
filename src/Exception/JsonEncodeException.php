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
use Throwable;

class JsonEncodeException extends Exception
{
    public function __construct(Throwable $jsonException)
    {
        parent::__construct('Failed encoding JSON content.', 0, $jsonException);
    }
}
