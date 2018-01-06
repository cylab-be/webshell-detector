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
        
        $dir = __DIR__."/res/";
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === "." || $file === "..")
                continue;
            echo PHP_EOL . "File $file";
            $result = $analyzer->analyze(file_get_contents($dir . $file));
            echo " Result: $result";
            $this->assertTrue($result >= 0 && $result <= 1, "result should be >= 0 and <= 1");
        }
    }
}
