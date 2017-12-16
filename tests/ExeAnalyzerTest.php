<?php
/**
 * File ExeAnalyzerTest
 *
 * @file     ExeAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;
/**
 * Class ExeAnalyzerTest extending TestCase. Performs tests on the class ExeAnalyzer
 *
 * @file     ExeAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class ExeAnalyzerTest extends TestCase
{

    /**
     * Performs test on a the object ExeAnalyzer
     * 
     * @return void
     */
    public function testTestMe()
    {
        $analyzer = new ExeAnalyzer();
        $result = $analyzer->analyze(
            file_get_contents(__DIR__."/res/test.php")
        );

        $this->assertTrue(
            $result >= 0 && $result <= 1,
            "result should be >= 0 and <= 1"
        );
    }
}
