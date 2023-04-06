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

class AttributeSplitter
{
    /**
     * @var array<int, string>
     */
    private array $attributesSplittable = [
        'Anchor',
        'ColumnsPositions',
        'GeometricBounds',
        'ItemTransform',
        'LeftDirection',
        'MasterPageTransform',
        'RightDirection',
        'StoryList',
    ];

    /**
     * @var array<mixed>
     */
    private readonly array $content;

    /**
     * @param array<mixed> $content
     */
    public function __construct(array $content)
    {
        $this->content = ArrayHelper::recurse(
            $content,
            function (string|int|float|bool|null $value, string|int $key = null): mixed {
                if (in_array($key, $this->attributesSplittable)) {
                    return explode(' ', (string) $value);
                }

                return $value;
            }
        );
    }

    /**
     * @return array<mixed>
     */
    public function getValue(): array
    {
        return $this->content;
    }
}
