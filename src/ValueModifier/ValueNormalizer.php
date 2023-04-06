<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlJsonConverter\ValueModifier;

use BitAndBlack\Helpers\ArrayHelper;
use BitAndBlack\Helpers\StringHelper;

readonly class ValueNormalizer
{
    /**
     * @param string|array<mixed> $content
     */
    public function __construct(
        private string|array $content
    ) {
    }

    /**
     * @return string|array<mixed>
     */
    public function getTyped(): string|array
    {
        return ArrayHelper::recurse(
            $this->content,
            static function (string|int|float|bool|null $value): mixed {
                $value = StringHelper::stringToBoolean($value);
                $value = StringHelper::stringToNumber($value);
                return $value;
            }
        );
    }

    /**
     * @return string|array<mixed>
     */
    public function getStringified(): string|array
    {
        return ArrayHelper::recurse(
            $this->content,
            static function (string|int|float|bool|null $value): mixed {
                $value = StringHelper::booleanToString($value);

                /**
                 * Current hack to keep null values.
                 */
                if ('null' === $value) {
                    $value = null;
                }

                return $value;
            }
        );
    }
}
