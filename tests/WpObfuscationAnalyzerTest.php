<?php
/**
 * File WpObfuscationAnalyzerTest.php
 *
 * @file     WpObfuscationAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;

/**
 * Class WpObfuscationAnalyzerTest extending TestCase. Computes a mark regarding obfuscation
 *
 * @file     WpObfuscationAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class WpObfuscationAnalyzerTest extends TestCase
{
    
    /**
     * Computes min, max and average for each type of dangerous functions
     * WP:
     * Non-ASCII: Min: 3 max: 16 std dev.: 3.6660605559647 avg: 7.4
     * Longest S: Min: 29 max: 122 std dev.: 25.247051922748 avg: 59.578616352201
     * Decode. R: Min: 1 max: 3 std dev.: 0.67082039324994 avg: 1.5
     * Webshells:
     * Non-ASCII: Min: 1 max: 781 std dev.: 233.79131875619 avg: 155.90243902439
     * Longest S: Min: 40 max: 1663 std dev.: 455.12495964173 avg: 495.6
     * Decode. R: Min: 1 max: 4 std dev.: 0.8887803753209 avg: 1.9583333333333
     * 
     * @return void
     */
    public function testObfuscationAnalyzer()
    {
        if (!file_exists(__DIR__.'/res/wordpress/'))
            return;
        $res = $this->_scanDir();
        $arrayNA = [];
        $arrayDE = [];
        $arrayLO = [];
        foreach ($res as $result) {
            array_push($arrayNA, $result[0]);
            array_push($arrayLO, $result[1]);
            array_push($arrayDE, $result[2]);
        }
        $arrayNA = Util::getMeaningfulArray($arrayNA);
        $arrayDE = Util::getMeaningfulArray($arrayDE);
        $arrayLO = Util::getMeaningfulArray($arrayLO);
        echo PHP_EOL."Non-ASCII: Min: ".min($arrayNA)." max: ".max($arrayNA)." std dev.: ".Util::standardDeviation($arrayNA)." avg: ".Util::average($arrayNA);
        echo PHP_EOL."Longest S: Min: ".min($arrayLO)." max: ".max($arrayLO)." std dev.: ".Util::standardDeviation($arrayLO)." avg: ".Util::average($arrayLO);
        echo PHP_EOL."Decode. R: Min: ".min($arrayDE)." max: ".max($arrayDE)." std dev.: ".Util::standardDeviation($arrayDE)." avg: ".Util::average($arrayDE);        
    }
    
    /**
     * Scans the Wordpress directory, 
     *
     * @param string $directory The directory to scan, wordpress by default
     *
     * @return array An array with all values (each item is an 3-array: exec, anonymous, and variable func)
     */
    private function _scanDir($directory = __DIR__.'/res/wordpress/')
    {
        $analyzer = new ObfuscationAnalyzer();
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
