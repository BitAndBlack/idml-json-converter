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

use BitAndBlack\IdmlJsonConverter\Converter\DomNodeToArrayConverter;
use DOMDocument;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DomNodeToArrayConverterTest extends TestCase
{
    /**
     * @var DOMDocument
     */
    private DOMDocument $domDocument;

    protected function setUp(): void
    {
        $this->domDocument = new DOMDocument();
    }

    /**
     * @param array<mixed> $outputExpected
     */
    #[DataProvider('provideTestCases')]
    public function testConversion(string $xml, array $outputExpected): void
    {
        $this->domDocument->loadXML($xml);
        $domNode = $this->domDocument->firstChild;

        $domNodeToArrayConverter = new DomNodeToArrayConverter($domNode);
        $actualArray = $domNodeToArrayConverter->getDomNodeArray();

        self::assertEquals(
            $outputExpected,
            $actualArray
        );
    }

    public static function provideTestCases(): Generator
    {
        yield [
            '<root>
                <child attribute="value 1">value 2</child>
            </root>',
            [
                '@name' => 'root',
                '@attributes' => [],
                '@value' => null,
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
        ];

        yield [
            '<root>
                <child1 attribute="value 1">
                    <child2 attribute="value 2">value 3</child2>
                    <child2 attribute="value 4">value 5</child2>
                    <child2 />
                </child1>
            </root>',
            [
                '@name' => 'root',
                '@attributes' => [],
                '@value' => null,
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
                            [
                                '@name' => 'child2',
                                '@value' => null,
                                '@attributes' => [],
                                '@children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield [
            '<root>
                <test value="&#x9;" />
            </root>',
            [
                '@name' => 'root',
                '@value' => null,
                '@attributes' => [],
                '@children' => [
                    [
                        '@name' => 'test',
                        '@value' => null,
                        '@attributes' => [
                            'value' => "\t",
                        ],
                        '@children' => [],
                    ],
                ],
            ],
        ];

        yield [
            '<root>
                <child1 attribute="value 1a">value 1b</child1>
                <child2 attribute="value 2a">value 2b</child2>
                <child1 attribute="value 3a">value 3b</child1>
                <child2 attribute="value 4a">value 4b</child2>
            </root>',
            [
                '@name' => 'root',
                '@attributes' => [],
                '@value' => null,
                '@children' => [
                    [
                        '@name' => 'child1',
                        '@attributes' => [
                            'attribute' => 'value 1a',
                        ],
                        '@value' => 'value 1b',
                        '@children' => [],
                    ],
                    [
                        '@name' => 'child2',
                        '@attributes' => [
                            'attribute' => 'value 2a',
                        ],
                        '@value' => 'value 2b',
                        '@children' => [],
                    ],
                    [
                        '@name' => 'child1',
                        '@attributes' => [
                            'attribute' => 'value 3a',
                        ],
                        '@value' => 'value 3b',
                        '@children' => [],
                    ],
                    [
                        '@name' => 'child2',
                        '@attributes' => [
                            'attribute' => 'value 4a',
                        ],
                        '@value' => 'value 4b',
                        '@children' => [],
                    ],
                ],
            ],
        ];
    }
}
