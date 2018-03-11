#!/usr/bin/env php

<?php
/**
 * File Main.php. Entry point
 *
 * @file     Main
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE
 * @link     https://github.com/RUCD/webshell-detector
 */
require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application("PHP WebShell detector", "@package_version@");
$application->add(new RUCD\WebshellDetector\AnalyzeDirectoryCommand());
$application->add(new RUCD\WebshellDetector\AnalyzeFileCommand());
$application->run();
