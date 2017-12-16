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
        $result = $analyzer->analyze(
            file_get_contents(__DIR__."/res/test.php")
        );
        echo "Entropy: ".$result.PHP_EOL;
        $this->assertTrue(
            $result >= 0,
            "result should be >= 0"
        );
    }
}
