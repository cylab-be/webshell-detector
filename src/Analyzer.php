<?php
/**
 * File Analyzer
 *
 * @file     Analyzer
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;
/**
 * Interface Analyzer. Defines the main behavior of all analyzers
 *
 * @file     Analyzer
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
interface Analyzer
{
    /**
     * Analyze a string and return a score between 0 (harmless) and 1 (highly
     * suspicious).
     *
     * @param string $filename Name of the file to read
     * 
     * @return mixed The value retuned by the implementation of the routine
     */
    public function analyze($filename);
}
