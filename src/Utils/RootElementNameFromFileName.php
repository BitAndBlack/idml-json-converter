<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlJsonConverter\Utils;

use BitAndBlack\IdmlJsonConverter\Exception\UnknownFileException;
use Stringable;

class RootElementNameFromFileName implements Stringable
{
    /**
     * @var array<string, string>
     */
    private array $rootElementNames = [
        'designmap.xml' => 'Document',
        'META-INF/container.xml' => 'container',
        'META-INF/metadata.xml' => 'x:xmpmeta',
        'Resources/Fonts.xml' => 'idPkg:Fonts',
        'Resources/Graphic.xml' => 'idPkg:Graphic',
        'Resources/Preferences.xml' => 'idPkg:Preferences',
        'Resources/Styles.xml' => 'idPkg:Styles',
        'XML/BackingStory.xml' => 'idPkg:BackingStory',
        'XML/Tags.xml' => 'idPkg:Tags',
    ];

    private readonly string $rootElementName;

    /**
     * @throws UnknownFileException
     */
    public function __construct(string $fileName)
    {
        $rootElementName = $this->rootElementNames[$fileName] ?? null;

        if (null === $rootElementName) {
            if (str_starts_with($fileName, 'MasterSpreads/')) {
                $rootElementName = 'idPkg:MasterSpread';
            } elseif (str_starts_with($fileName, 'Spreads/')) {
                $rootElementName = 'idPkg:Spread';
            } elseif (str_starts_with($fileName, 'Stories/')) {
                $rootElementName = 'idPkg:Story';
            }
        }

        if (null === $rootElementName) {
            throw new UnknownFileException($fileName);
        }

        $this->rootElementName = $rootElementName;
    }

    public function __toString(): string
    {
        return $this->getRootElementName();
    }

    public function getRootElementName(): string
    {
        return $this->rootElementName;
    }
}
