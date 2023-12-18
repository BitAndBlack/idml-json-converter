<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlJsonConverter\Converter;

use DOMNode;

/**
 * @see \BitAndBlack\IdmlJsonConverter\Tests\Converter\DomNodeToArrayConverterTest
 */
readonly class DomNodeToArrayConverter
{
    /**
     * @var array|string|null
     */
    private string|array|null $domNodeArray;

    public function __construct(DOMNode $node)
    {
        $this->domNodeArray = $this->domNodeToArray($node);
    }

    /**
     * @return array<int|string, mixed>|string|null
     */
    private function domNodeToArray(DOMNode $node): array|string|null
    {
        if (XML_CDATA_SECTION_NODE === $node->nodeType || XML_TEXT_NODE === $node->nodeType) {
            $textContent = trim($node->textContent, "\t\n\r");

            return '' !== $textContent
                ? $textContent
                : null
            ;
        }

        $output = [];

        if (XML_ELEMENT_NODE === $node->nodeType) {
            $childNodesCount = $node->childNodes->length;

            for ($counter = 0; $counter < $childNodesCount; ++$counter) {
                $childNode = $node->childNodes->item($counter);

                if (!isset($output['@children'])) {
                    $output['@children'] = [];
                }

                $childKeyNext = count($output['@children']);

                if (null === $childNode) {
                    continue;
                }

                $childNodeValues = $this->domNodeToArray($childNode);

                if (isset($childNode->tagName)) {
                    if (!is_array($output)) {
                        continue;
                    }

                    $output['@children'][$childKeyNext] = $childNodeValues;
                    continue;
                }

                if (is_string($childNodeValues)) {
                    $output['@value'] = $childNodeValues;
                }

                if ([] !== $output['@children']) {
                    $output['@value'] = null;
                }
            }

            if (is_array($output)) {
                $nodeAttributes = $node->attributes;

                if (null !== $nodeAttributes) {
                    $attributes = [];

                    foreach ($nodeAttributes as $nodeAttribute) {
                        $attributes[$nodeAttribute->nodeName] = (string) $nodeAttribute->nodeValue;

                        if ('CustomCharacters' === $nodeAttribute->nodeName) {
                            $attributes[$nodeAttribute->nodeName] = htmlentities((string) $nodeAttribute->nodeValue);
                        }
                    }

                    $output['@attributes'] = $attributes;
                }

                $base = [
                    '@name' => $node->nodeName,
                    '@attributes' => [],
                    '@value' => null,
                    '@children' => [],
                ];

                $output = array_merge($base, $output);
            }
        }

        return $output;
    }

    public function getDomNodeArray(): array|string|null
    {
        return $this->domNodeArray;
    }
}
