<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlJsonConverter\File;

use BitAndBlack\IdmlJsonConverter\Converter\DomNodeToArrayConverter;
use BitAndBlack\IdmlJsonConverter\Exception\CannotReadFileException;
use BitAndBlack\IdmlJsonConverter\Exception\JsonEncodeException;
use BitAndBlack\IdmlJsonConverter\ValueModifier\AttributeSplitter;
use BitAndBlack\IdmlJsonConverter\ValueModifier\ValueNormalizer;
use DOMDocument;
use JsonException;
use ZipArchive;

class IDML
{
    /**
     * @var array<mixed>
     */
    private readonly array $content;

    private bool $prettifyOutput;

    /**
     * @throws CannotReadFileException
     */
    public function __construct(string $file)
    {
        $this->prettifyOutput = true;

        $zipArchive = new ZipArchive();

        $contents = [];

        if (true !== $zipArchive->open($file)) {
            throw new CannotReadFileException($file);
        }

        $filesCount = $zipArchive->numFiles;

        for ($counter = 0; $counter < $filesCount; ++$counter) {
            $filename = $zipArchive->getNameIndex($counter);
            $content = $zipArchive->getFromIndex($counter);

            if (false === $filename || false === $content) {
                continue;
            }

            if (str_ends_with($filename, '.xml')) {
                $content = (string) preg_replace_callback(
                    '/<\?ACE\s(.*?)\?>/s',
                    static fn ($matches) => '&lt;?ACE ' . htmlentities((string) $matches[1], ENT_QUOTES | ENT_XML1, 'UTF-8') . '?&gt;',
                    $content
                );

                $content = (string) preg_replace_callback(
                    '/<!\[CDATA\[(.*?)\]\]>/s',
                    static fn ($matches) => htmlentities((string) $matches[1], ENT_QUOTES | ENT_XML1, 'UTF-8'),
                    $content
                );

                $domDocument = new DOMDocument();
                $domDocument->loadXML($content);

                $node = $domDocument->documentElement;

                if (null === $node) {
                    continue;
                }

                $domNodeToArrayConverter = new DomNodeToArrayConverter($node);
                $array = $domNodeToArrayConverter->getDomNodeArray();

                $contents[$filename] = $array;
                continue;
            }

            $contents[$filename] = $content;
        }

        $zipArchive->close();

        $attributeSplitter = new AttributeSplitter($contents);
        $contents = $attributeSplitter->getValue();

        $valueNormalizer = new ValueNormalizer($contents);
        $contents = $valueNormalizer->getTyped();

        $this->content = $contents;
    }

    /**
     * @return array<mixed>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @throws JsonEncodeException
     */
    public function getJSON(): string
    {
        $jsonFlags = JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION;

        if ($this->isPrettifyOutput()) {
            $jsonFlags |= JSON_PRETTY_PRINT;
        }

        try {
            $json = json_encode(
                $this->getContent(),
                $jsonFlags
            );
        } catch (JsonException $jsonException) {
            throw new JsonEncodeException($jsonException);
        }

        return $json;
    }

    public function setPrettifyOutput(bool $prettifyOutput): self
    {
        $this->prettifyOutput = $prettifyOutput;
        return $this;
    }

    public function isPrettifyOutput(): bool
    {
        return $this->prettifyOutput;
    }
}
