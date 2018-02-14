<?php
namespace RUCD\WebshellDetector;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeDirectoryCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('analyze:directory')
            ->setDescription('Analyze a directory.')
            ->addArgument('directory', InputArgument::REQUIRED, 'The directory to analyze');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');
        $detector = new Detector();
        $detector->analyzeDirectory($directory);
    }
}