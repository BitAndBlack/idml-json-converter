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

use BitAndBlack\Helpers\StringHelper;
use DOMDocument;
use DOMException;
use DOMNode;

/**
 * @see \BitAndBlack\IdmlJsonConverter\Tests\Converter\ArrayToDomNodeConverterTest
 */
readonly class ArrayToDomNodeConverter
{
    private DOMDocument $domDocument;

    /**
     * @param array{
     *     "@name": string,
     *     "@attributes": array<string, string|int|float|bool|null|array<int, int|float>>,
     *     "@value": string|null,
     *     "@children": array<int, array<mixed>>,
     * } $content
     * @throws DOMException
     */
    public function __construct(array $content, string $rootElementName = 'root')
    {
        $dom = new DomDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = true;
        $dom->xmlStandalone = true;

        if ('Document' === $rootElementName) {
            /**
             * Append processing instruction as second line here
             * InDesign needs that line and without that the IDML document will be invalid
             */
            $aidInstruction = $dom->createProcessingInstruction(
                'aid',
                'style="50" type="document" readerVersion="6.0" featureSet="257" product="18.1(51)"'
            );
            $dom->appendChild($aidInstruction);
        } elseif ('x:xmpmeta' === $rootElementName) {
            $xpacket = $dom->createProcessingInstruction('xpacket', 'begin="" id=" "');
            $dom->appendChild($xpacket);
        }

        $root = $dom->createElement($rootElementName);

        if ('container' === $rootElementName) {
            $root->setAttribute('xmlns', 'urn:oasis:names:tc:opendocument:xmlns:container');
        } else {
            $root->setAttribute('xmlns:idPkg', 'http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging');
        }

        $this->getXMLFromArray($content, $dom, $root);
        $dom->appendChild($root);

        if ('x:xmpmeta' === $rootElementName) {
            $root->setAttribute('xmlns:x', 'adobe:ns:meta/');
            $xpacket = $dom->createProcessingInstruction('xpacket', 'end="r"');
            $dom->appendChild($xpacket);
        }

        $this->domDocument = $dom;
    }

    /**
     * @param array{
     *     "@name": string,
     *     "@attributes": array<string, string|int|float|bool|null|array<int, int|float>>,
     *     "@value": string|null,
     *     "@children": array<int, array<mixed>>,
     * } $data
     * @throws DOMException
     */
    private function getXMLFromArray(array $data, DOMDocument $domDocument, DOMNode $node): void
    {
        foreach ($data['@attributes'] as $attributeKey => $attributeValue) {
            if (is_array($attributeValue)) {
                $attributeValue = implode(' ', $attributeValue);
            }

            if ('DOMVersion' === $attributeKey || 'version' === $attributeKey) {
                $attributeValue = number_format((float) $attributeValue, 1);
            }

            if ('CustomCharacters' === $attributeKey) {
                $attributeValue = '';
            }

            $node->setAttribute($attributeKey, $attributeValue);
        }

        if (null !== $value = $data['@value']) {
            if ('Contents' === $data['@name']) {
                $cdataSection = $domDocument->createCDATASection($value);
                $node->appendChild($cdataSection);
            } else {
                $value = StringHelper::booleanToString($value);
                $node->nodeValue = htmlspecialchars((string) $value);
            }
        }

        foreach ($data['@children'] as $child) {
            $subnode = $domDocument->createElement($child['@name']);
            $node->appendChild($subnode);
            $this->getXMLFromArray($child, $domDocument, $subnode);
        }
    }

    public function getDomDocument(): DOMDocument
    {
        return $this->domDocument;
    }

    public function getString(): string
    {
        $xml = (string) $this->getDomDocument()->saveXML();

        return (string) preg_replace_callback(
            '/&#(\d+);/m',
            static fn ($matches): string => sprintf('&#x%X;', $matches[1]),
            $xml
        );
    }
}
