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

use BitAndBlack\IdmlJsonConverter\Converter\ArrayToDomNodeConverter;
use BitAndBlack\IdmlJsonConverter\Exception\FailedExtractingContentExpection;
use BitAndBlack\IdmlJsonConverter\Exception\UnknownFileException;
use BitAndBlack\IdmlJsonConverter\Utils\RootElementNameFromFileName;
use BitAndBlack\IdmlJsonConverter\ValueModifier\ValueNormalizer;
use DOMException;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

readonly class JSON
{
    private ?string $response;

    /**
     * @param array<string, string> $content
     * @throws DOMException
     * @throws FailedExtractingContentExpection
     * @throws UnknownFileException
     */
    public function __construct(array $content)
    {
        $outputStream = fopen('php://memory', 'wb+');

        if (false === $outputStream) {
            throw FailedExtractingContentExpection::phpMemory();
        }

        $archive = new Archive();
        $archive->setOutputStream($outputStream);
        $archive->setSendHttpHeaders(false);

        $zipStream = new ZipStream(null, $archive);

        foreach ($content as $fileName => $fileContent) {
            $valueNormalizer = new ValueNormalizer($fileContent);
            $fileContent = $valueNormalizer->getStringified();

            if (str_ends_with($fileName, '.xml')) {
                $rootElementName = new RootElementNameFromFileName($fileName);
                $arrayToDomNodeConverter = new ArrayToDomNodeConverter($fileContent, $rootElementName);
                $fileContent = $arrayToDomNodeConverter->getString();

                $fileContent = preg_replace_callback(
                    '/&lt;\?ACE\s(.*?)\?&gt;/s',
                    static fn ($matches) => '<?ACE ' . htmlentities((string) $matches[1], ENT_QUOTES | ENT_XML1, 'UTF-8') . '?>',
                    $fileContent
                );
            }

            $zipStream->addFile($fileName, $fileContent);
        }

        try {
            $zipStream->finish();
        } catch (OverflowException $exception) {
            throw FailedExtractingContentExpection::fileTooLarge($exception);
        }

        rewind($outputStream);

        $response = stream_get_contents($outputStream);

        fclose($outputStream);

        if (!$response) {
            throw FailedExtractingContentExpection::cannotReadFromStream();
        }

        $this->response = $response;
    }

    public function getIDML(): ?string
    {
        return $this->response;
    }
}
