<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlJsonConverter\Tests\Converter;

use BitAndBlack\IdmlJsonConverter\Converter\ArrayToDomNodeConverter;
use DOMDocument;
use DOMException;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrayToDomNodeConverterTest extends TestCase
{
    /**
     * @var DOMDocument
     */
    private DOMDocument $domDocument;

    protected function setUp(): void
    {
        $this->domDocument = new DOMDocument('1.0', 'UTF-8');
        $this->domDocument->preserveWhiteSpace = false;
        $this->domDocument->formatOutput = true;
    }

    /**
     * @param array<mixed> $input
     * @throws DOMException
     */
    #[DataProvider('provideTestCases')]
    public function testConversion(array $input, string $xmlExpected): void
    {
        $this->domDocument->loadXML($xmlExpected);

        $arrayToDomNodeConverter = new ArrayToDomNodeConverter($input);
        $output = $arrayToDomNodeConverter->getString();

        self::assertEquals(
            $this->domDocument->saveXML(),
            $output
        );
    }

    public static function provideTestCases(): Generator
    {
        yield [
            [
                '@name' => 'root',
                '@value' => null,
                '@attributes' => [],
                '@children' => [
                    [
                        '@name' => 'child',
                        '@value' => 'value 2',
                        '@attributes' => [
                            'attribute' => 'value 1',
                        ],
                        '@children' => [],
                    ],
                ],
            ],
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<root xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging">
    <child attribute="value 1">value 2</child>
</root>',
        ];

        yield [
            [
                '@name' => 'root',
                '@value' => null,
                '@attributes' => [],
                '@children' => [
                    [
                        '@name' => 'child1',
                        '@value' => null,
                        '@attributes' => [
                            'attribute' => 'value 1',
                        ],
                        '@children' => [
                            [
                                '@name' => 'child2',
                                '@value' => 'value 3',
                                '@attributes' => [
                                    'attribute' => 'value 2',
                                ],
                                '@children' => [],
                            ],
                            [
                                '@name' => 'child2',
                                '@value' => 'value 5',
                                '@attributes' => [
                                    'attribute' => 'value 4',
                                ],
                                '@children' => [],
                            ],
                        ],
                    ],
                ],
            ],
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<root xmlns:idPkg="http://ns.adobe.com/AdobeInDesign/idml/1.0/packaging">
    <child1 attribute="value 1">
        <child2 attribute="value 2">value 3</child2>
        <child2 attribute="value 4">value 5</child2>
    </child1>
</root>',
        ];
    }
}
