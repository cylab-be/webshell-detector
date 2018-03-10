<?php
/**
 * Class DetectorTest
 *
 * @file     DetectorTest
 * @category None
 * @package  Tests
 * @author   Thibault Debatty <thibault.debatty@gmail.com>
 * @license  https://github.com/RUCD/webshell-detector/blob/master/LICENSE MIT
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
 * @license  https://github.com/RUCD/webshell-detector/blob/master/LICENSE MIT
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
}