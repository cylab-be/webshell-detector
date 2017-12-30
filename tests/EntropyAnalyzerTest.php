<?php
/**
 * File EntropyAnalyzerTest
 *
 * @file     EntropyAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;
/**
 * Class EntropyAnalyzerTest extending TestCase. Performs tests on the class EntropyAnalyzerTest
 *
 * @file     EntropyAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class EntropyAnalyzerTest extends TestCase
{
    
    /**
     * Performs test on a the object EntropyAnalyzerTest
     *
     * @return void
     */
    public function testTestMe()
    {
        $analyzer = new EntropyAnalyzer();
        $dir = __DIR__."/res/";
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === "." || $file === "..")
                continue;
            $result = $analyzer->analyze(
                file_get_contents($dir.$file)
            );
            echo PHP_EOL."Entropy: $result File: $file";
            $this->assertTrue(
                $result >= 0,
                "result should be >= 0"
            );
        }
    }
}
