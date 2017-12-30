<?php
/**
 * Class DetectorTest
 *
 * @file     DetectorTest
 * @category None
 * @package  Tests
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;

/**
 * Class DetectorTest extending TestCase. Performs tests on the class Detector
 *
 * @file     DetectorTest
 * @category None
 * @package  Tests
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class DetectorTest extends TestCase
{

    /**
     * Performs a test on the routine Detector::analyzeString
     * 
     * @return void
     */
    public function testAnalyzeString()
    {
        $detector = new Detector();
        $this->assertTrue(
            $detector->analyzeString(file_get_contents(__DIR__.'/res/test.php').PHP_EOL.'exe("something")') > 0,
            "The detector should return a score > 0 as the test contains"
            . "the exe function"
        );
    }
    
    /**
     * Performs a test on the routine Detector::analyzeDirectory
     *
     * @return void
     */
    public function testAnalyzeDirectory()
    {
        $detector = new Detector();
        $scores = $detector->analyzeDirectory(__DIR__."/res/");
        var_dump($scores);
        $this->assertTrue(
            count($scores) > 0
        );
    }
}