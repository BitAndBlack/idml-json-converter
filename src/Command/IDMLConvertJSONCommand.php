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

use BitAndBlack\IdmlJsonConverter\Exception\CannotReadFileException;
use BitAndBlack\IdmlJsonConverter\Exception\JsonEncodeException;
use BitAndBlack\IdmlJsonConverter\File\IDML;
use BitAndBlack\IdmlJsonConverter\MemoryLimit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IDMLConvertJSONCommand extends Command
{
    public function configure(): void
    {
        $this
            ->setName('idml:convert:json')
            ->setDescription('Converts an IDML file into JSON.')
            ->addArgument(
                'idml-file',
                InputArgument::REQUIRED,
                'Path to the IDML file.'
            )
            ->addArgument(
                'json-file',
                InputArgument::REQUIRED,
                'Path to the JSON file.'
            )
            ->addOption(
                'compress-output',
                'c',
                InputOption::VALUE_NONE,
                'Whether to reduce the size of the output by removing unnecessary whitespaces. This option is deactivated by default, resulting in easily readable but larger JSON files.',
            )
            ->addOption(
                'memory-limit-disabled',
                'm',
                InputOption::VALUE_NONE,
                'This option deactivates the memory limit.',
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $isMemoryLimitDisabled = $input->getOption('memory-limit-disabled');

        if ($isMemoryLimitDisabled) {
            $success = ini_set('memory_limit', '-1');

            if (!$success) {
                $io->error('Failed changing memory limit.');
            }
        }

        $memoryLimitCurrent = new MemoryLimit();
        $memoryLimitCurrentBytes = $memoryLimitCurrent->getMemoryLimitInBytes();

        if (-1.0 === $memoryLimitCurrentBytes) {
            $isMemoryLimitDisabled = true;
        }

        $memoryLimitPreferredMegaBytes = 512;
        $memoryLimitPreferredBytes = $memoryLimitPreferredMegaBytes * 1024 * 1024;

        if (!$isMemoryLimitDisabled && $memoryLimitCurrentBytes < $memoryLimitPreferredBytes) {
            $io->warning(
                'The memory limit is currently set to ' . $memoryLimitCurrent . ', which may lead to problems. '
                . 'Our recommendation for this process would be at least ' . $memoryLimitPreferredMegaBytes . 'M. '
                . 'Please consider changing this value. '
                . 'If you want to disable the memory limit completely, run this command with the option "--memory-limit-disabled".'
            );
        }

        $idmlFile = $input->getArgument('idml-file');
        $jsonFile = $input->getArgument('json-file');

        if (!is_string($idmlFile) || !is_string($jsonFile)) {
            $io->error('Cannot use argument.');
            return Command::FAILURE;
        }

        $io->writeln('Starting conversion.');

        try {
            $idml = new IDML($idmlFile);
        } catch (CannotReadFileException $exception) {
            $io->error('Failed.');
            $io->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        /** @var bool $compressOutput */
        $compressOutput = $input->getOption('compress-output');

        if ($compressOutput) {
            $idml->setPrettifyOutput(false);
        }

        try {
            $json = $idml->getJSON();
        } catch (JsonEncodeException $exception) {
            $io->error('Failed.');
            $io->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $success = false !== file_put_contents($jsonFile, $json);

        if (!$success) {
            $io->error('Failed.');
            $io->writeln('Cannot write file.');
            return Command::FAILURE;
        }

        $io->success('Finished conversion.');

        return Command::SUCCESS;
    }
}
