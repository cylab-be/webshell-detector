<?php
/**
 * File WpExeAnalyzerTest.php
 *
 * @file     WpExeAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;

/**
 * Class WpExeAnalyzerTest extending TestCase. Counts exec routines in WP files
 *
 * @file     WpExeAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class WpExeAnalyzerTest extends TestCase
{
    
    /**
     * Computes min, max and average for each type of dangerous functions
     *
     * @return void
     */
    public function testExeAnalyzer()
    {
        if (!file_exists(__DIR__.'/res/wordpress/'))
            return;
        $res = $this->_scanDir();
        $arrayEX = [];
        $arrayAN = [];
        $arrayVF = [];
        foreach ($res as $result) {
            array_push($arrayEX, $result[0]);
            array_push($arrayAN, $result[1]);
            array_push($arrayVF, $result[2]);
        }
        
        /*$arrayEX = Util::getMeaningfulArray($arrayEX);
        $arrayAN = Util::getMeaningfulArray($arrayAN);
        $arrayVF = Util::getMeaningfulArray($arrayVF);*/
        
        echo PHP_EOL."Exec.: Min: ".min($arrayEX)." max: ".max($arrayEX)." std dev.: ".Util::standardDeviation($arrayEX)." avg: ".Util::average($arrayEX);
        echo PHP_EOL."Anon.: Min: ".min($arrayAN)." max: ".max($arrayAN)." std dev.: ".Util::standardDeviation($arrayAN)." avg: ".Util::average($arrayAN);
        echo PHP_EOL."VarF.: Min: ".min($arrayVF)." max: ".max($arrayVF)." std dev.: ".Util::standardDeviation($arrayVF)." avg: ".Util::average($arrayVF);
    }
    
    /**
     * Scans the Wordpress directory, and searches dangerous functions
     * 
     * @param string $directory The directory to scan, wordpress by default
     * 
     * @return array An array with all values (each item is an 3-array: exec, anonymous, and variable func)
     */
    private function _scanDir($directory = __DIR__.'/res/wordpress/')
    {
        $analyzer = new ExeAnalyzer();
        $scores = [];
        $files = scandir($directory);
        $dirs = [];
        foreach ($files as $file) {
            if ($file === "." || $file === "..")
                continue;
            if (is_dir($directory.$file)) {
                array_push($dirs, $directory.$file.'/');
            } elseif (preg_match('/\.php$/', $file)) {
                $result = $analyzer->getScores(file_get_contents($directory.$file));
                array_push($scores, $result);
                $this->assertTrue($result >= 0, "Result should be greater than 0");
            }
        }
        foreach ($dirs as $dir) {
            $scores = array_merge($scores, $this->_scanDir($dir));
        }
        return $scores;
    }
}
