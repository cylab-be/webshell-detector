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
     * Performs a test on the routine Detector::analyzeDirectory
     *
     * @return void
     */
    public function testAnalyzeDirectory()
    {
        $detector = new Detector();
        $results_count = 0;
        foreach ($detector->analyzeDirectory(__DIR__."/res/") as $file => $score) {
            $results_count++;
        }
        $this->assertTrue($results_count > 0);
    }

    /**
     * Performs test on a single file
     *
     * @return void

    public function testAnalyzeFile()
    {
        $detector = new Detector();
        $score = $detector->analyzeString(file_get_contents(__DIR__."/res/php-webshells-master/OTHER/php-findsock-shell.php"));
        $this->assertTrue($score > 0);
    }
     */
}