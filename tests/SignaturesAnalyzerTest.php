<?php
/**
 * File SignaturesAnalyzerTest
 *
 * @file     SignaturesAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;

/**
 * Class SignaturesAnalyzerTest extending TestCase. Performs tests on the class SignaturesAnalyzer
 * 
 * @file     SignaturesAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class SignaturesAnalyzerTest extends TestCase
{

    /**
     * Performs a basic scan of a file using registered analyzers
     * 
     * @return void
     */
    public function testScanFile()
    {
        $analyzer = new SignaturesAnalyzer();
        $flag1 = $analyzer->analyze(
            file_get_contents(__DIR__ . "/res/c.php")
        );
        $flag2 = $analyzer->analyze(
            file_get_contents(__DIR__ . "/res/enc_c.php")
        );
        $this->assertTrue($flag1 != null);
        $this->assertTrue($flag2 != null);
        $this->assertTrue(is_double($flag1), "Result should be a number");
        $this->assertTrue($flag1 >= 0, "Result should be >= 0");
        $this->assertTrue($flag <= 1, "Result should be <= 1");
    }
}
