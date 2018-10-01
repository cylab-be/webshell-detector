<?php
namespace RUCD\WebshellDetector;


use Aggregation\WOWA;
/**
 * Class Detector. Entry point containing all analyzers
 *
 * @file     Dectector
 * @category None
 * @package  Source
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://github.com/RUCD/webshell-detector/blob/master/LICENSE MIT
 * @link     https://github.com/RUCD/webshell-detector
 */
class TrainDetector
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
     * @param string $directory    Name of the directory to scan
     * @param string $dataFile     File name where scores will be saved
     * @param string $expectedFile File name where expected results 
     *                             for wowa-training are saved
     *
     * @return array a generator of entries $file => $score
     */
    public function analyzeDirectory($directory, $dataFile, $expectedFile)
    {

        if (!is_dir($directory)) {
            throw new Exception("$directory is not a directory!");
        }
        
        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file === ".." || $file === ".") {
                continue;
            }

            $file = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($file)) {
                yield from $this->analyzeDirectory($file, $dataFile, $expectedFile);
                continue;

            }

            if (preg_match('/\.php$/', $file)) {
                if (preg_match('~/res/php-webshells-master~', $file)  
                    || preg_match('~/res/webshells_modified~', $file) 
                ) {
                    $this->_addDataToFile($expectedFile, 1);
                } else {
                    $this->_addDataToFile($expectedFile, 0);
                }
                $score = $this->analyzeString(
                    file_get_contents($file), $dataFile
                );
                yield $file => $score;
            }
        }
    }

    /**
     * Analyze a string and return a score between 0 (harmless) and 1 (highly
     * suspicious).
     *
     * @param string $string      The string to analyzer
     * @param string $fileToStore File name where scores are saved
     *
     * @return float The score
     */
    public function analyzeString($string, $fileToStore)
    {
        $scores = [];
        foreach ($this->_analyzers as $analyzer) {
            $res = $analyzer->analyze($string);
            $scores[] = $res;
        }
        $this->_addDataToFile($fileToStore, $scores);
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
        // Wowa aggregation
        return array_sum($scores) / count($scores);
        //return WOWA::wowa($this->w, $this->p, $scores);
    }
    /**
     * Add data in file to be used in wowa-training
     * 
     * @param string $fileName File name to save data
     * @param type   $data     data to save (scores or expected)
     * 
     * @return none 
     */
    private function _addDataToFile($fileName, $data) 
    {
        if (!file_exists($fileName)) {
            $dataArray = [];
            $dataArray[] = $data;
            file_put_contents($fileName, serialize($dataArray));
        } else {
            $oldData = unserialize(file_get_contents($fileName));
            $oldData[] = $data;
            file_put_contents($fileName, serialize($oldData));
            
        }
    }
}
