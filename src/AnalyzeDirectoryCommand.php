<?php

namespace RUCD\WebshellDetector;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Class AnalyzeDirectoryCommand. Defines the command analyze:directory
 *
 * @file     AnalyzeDirectoryCommand
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://github.com/RUCD/webshell-detector/blob/master/LICENSE MIT
 * @link     https://github.com/RUCD/webshell-detector
 */
class AnalyzeDirectoryCommand extends Command
{

    const DEFAULT_THRESHOLD = 0.4;

    /**
     * Configures the command analyze:directory
     * {@inheritDoc}
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('analyze:directory')
            ->setDescription('Analyze a directory.')
            ->addArgument(
                'directory',
                InputArgument::REQUIRED,
                'The directory to analyze'
            )
            ->addOption(
                'threshold',
                't',
                InputOption::VALUE_OPTIONAL,
                'The minimum score to display result',
                self::DEFAULT_THRESHOLD
            );
    }

    /**
     * Runs the command analyze:directory
     * {@inheritDoc}
     *
     * @param InputInterface  $input  stdin reader
     * @param OutputInterface $output stdout writer
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = realpath($input->getArgument('directory'));
        if ($directory === null) {
            throw new Exception("Invalid directory!");
        }

        $threshold = $input->getOption("threshold");

        $detector = new Detector();
        foreach ($detector->analyzeDirectory($directory) as $file => $score) {
            if ($score >= $threshold) {
                $output->write("$file : $score" . PHP_EOL);
            }
        }
    }
}