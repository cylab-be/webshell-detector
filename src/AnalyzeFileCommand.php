<?php

namespace RUCD\WebshellDetector;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Class AnalyzeFileCommand. Defines the command analyze:file
 *
 * @file     AnalyzeFileCommand
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://github.com/RUCD/webshell-detector/blob/master/LICENSE MIT
 * @link     https://github.com/RUCD/webshell-detector
 */
class AnalyzeFileCommand extends Command
{
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
            ->setName('analyze:file')
            ->setDescription('Analyze a file.')
            ->addArgument('file', InputArgument::REQUIRED, 'The file to analyze');
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
        $file = $input->getArgument('file');
        $detector = new Detector();
        if (!file_exists($file)) {
            $file = getcwd().'/'.$file;
        }
        if (substr($file, strlen($file)-4) === ".php" && file_exists($file)) {
            $result = $detector->analyzeString(file_get_contents($file));
            if (is_string($result)) {
                $output.write($result);
            } else {
                $output->write("File $file - Score: $result");
            }
        } else {
            $output->write("File $file doesn't exist");
        }
    }
}