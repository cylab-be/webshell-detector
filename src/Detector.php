<?php
/**
 * File Detector
 *
 * @file     Dectector
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;
/**
 * Class Detector. Entry point containing all analyzers
 *
 * @file     Dectector
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class Detector
{

    private $_analyzers = [];

    /**
     * Contructor of Detector. Initializes analyzers
     */
    public function __construct()
    {
        $this->_analyzers[] = new ExeAnalyzer();
        $this->_analyzers[] = new SignaturesAnalyzer();
        $this->_analyzers[] = new EntropyAnalyzer();
    }

    /**
     * Recursively scan a directory, and scan all files.
     *
     * @param string $directory Name of the directory to scan
     * 
     * @return array The array containing all scores
     */
    public function analyzeDirectory($directory)
    {
        $scores = [];
        if (is_dir($directory)) {
            $files = scandir($directory);
            echo "Scanning directory: ".$directory.PHP_EOL;
            foreach ($files as $file) {
                if ($file !== ".." && $file !== ".") {
                    echo $directory.$file.PHP_EOL;
                    array_push($scores, $this->analyzeFile($directory.$file));
                }
            }
        }
        return $scores;
    }

    /**
     * Analyzes a file.
     * 
     * @param string $filename The name of the file to read
     * 
     * @return float The final score of the file
     */
    public function analyzeFile($filename)
    {
        return $this->analyzeString(file_get_contents($filename));
    }

    /**
     * Analyze a string and return a score between 0 (harmless) and 1 (highly
     * suspicious).
     *
     * @param string $string The string to analyzer
     * 
     * @return float The score
     */
    public function analyzeString($string)
    {

        $scores = [];
        foreach ($this->_analyzers as $analyzer) {
            $scores[] = $analyzer->analyze($string);
        }
        return $this->_aggregate($scores);
    }

    /**
     * Returns a mark regarding the harmfulness of submitted scores
     * 
     * @param array $scores The computed scores
     * 
     * @return float The final score
     */
    private function _aggregate($scores)
    {
        // For now, perform a simple average
        return array_sum($scores) / count($scores);
    }
}
