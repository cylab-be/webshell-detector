<?php
/**
 * File AnalyzeDirectoryCommand
 *
 * @file     AnalyzeDirectoryCommand
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Class AnalyzeDirectoryCommand. Defines the command analyze:directory
 *
 * @file     AnalyzeDirectoryCommand
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class AnalyzeDirectoryCommand extends Command
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
            ->setName('analyze:directory')
            ->setDescription('Analyze a directory.')
            ->addArgument('directory', InputArgument::REQUIRED, 'The directory to analyze');
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
        $directory = $input->getArgument('directory');
        $detector = new Detector();
        if ($directory[strlen($directory)-1] !== "/")
            $directory.="/";
        $tmp = getcwd().'/'.$directory;
        if (file_exists($tmp)) {
            $result = $detector->analyzeDirectory($tmp);
            if (is_string($result)) {
                $output->writeln($result);
            } else {
                foreach ($result as $key => $value)
                    $output->write("File $key - Score: $value".PHP_EOL);
            }
        } else {
            $output->writeln("File $tmp doesn't exist");
        }
    }
}