#!/usr/bin/env php

<?php

// autoload for external libraries
require __DIR__ . '/../vendor/autoload.php';

// autoload for our classes
require __DIR__ . '/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new RUCD\WebshellDetector\AnalyzeDirectoryCommand());
$application->run();
