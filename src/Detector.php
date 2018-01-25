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
        $this->_analyzers[] = new FuzzyHashingAnalyzer();
        $this->_analyzers[] = new ObfuscationAnalyzer();
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
            echo PHP_EOL."Scanning directory: ".$directory.PHP_EOL;
            $dirs = [];
            foreach ($files as $file) {
                if ($file !== ".." && $file !== ".") {
                    if (is_dir($directory.$file)) {
                        array_push($dirs, $directory.$file.'/');
                    } elseif (preg_match('/\.php$/', $file)) {
                        echo PHP_EOL."File $file";
                        $score = $this->analyzeString(file_get_contents($directory.$file));
                        echo PHP_EOL."Score: $score".PHP_EOL;
                        array_push($scores, $score);
                    }
                }
            }
            foreach ($dirs as $dir) {
                $this->analyzeDirectory($dir);
            }
        }
        return $scores;
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
            echo PHP_EOL."Runs ".get_class($analyzer).": ";
            $scores[] = $analyzer->analyze($string);
            echo end($scores);
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
