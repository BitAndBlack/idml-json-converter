<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\IdmlJsonConverter\Command;

use BitAndBlack\IdmlJsonConverter\Exception\FailedExtractingContentExpection;
use BitAndBlack\IdmlJsonConverter\Exception\UnknownFileException;
use BitAndBlack\IdmlJsonConverter\File\JSON;
use DOMException;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class JSONConvertIDMLCommand extends Command
{
    public function configure(): void
    {
        $this
            ->setName('json:convert:idml')
            ->setDescription('Converts JSON into IDML.')
            ->addArgument(
                'json-file',
                InputArgument::REQUIRED,
                'Path to the JSON file.'
            )
            ->addArgument(
                'idml-file',
                InputArgument::REQUIRED,
                'Path to the IDML file.'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $jsonFile = $input->getArgument('json-file');
        $idmlFile = $input->getArgument('idml-file');

        if (!is_string($idmlFile) || !is_string($jsonFile)) {
            $io->error('Cannot use argument.');
            return Command::FAILURE;
        }

        $io->writeln('Starting conversion.');

        $jsonContent = file_get_contents($jsonFile);

        if (!$jsonContent) {
            $io->error('Failed.');
            $io->writeln('Cannot read file.');
            return Command::FAILURE;
        }

        try {
            $jsonContent = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $jsonContent = null;
        }

        if (!is_array($jsonContent)) {
            $io->error('Failed.');
            $io->writeln('Cannot read JSON content.');
            return Command::FAILURE;
        }

        try {
            $json = new JSON($jsonContent);
        } catch (FailedExtractingContentExpection|UnknownFileException|DOMException $exception) {
            $io->error('Failed.');
            $io->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $idmlContent = $json->getIDML();

        $success = false !== file_put_contents($idmlFile, $idmlContent);

        if (!$success) {
            $io->error('Failed.');
            $io->writeln('Cannot write file.');
            return Command::FAILURE;
        }

        $io->success('Finished conversion.');

        return Command::SUCCESS;
    }
}
