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
     * @param string $directory Name of the directory, by default __DIR__/res/
     * 
     * @return void
     */
    public function testScanFile($directory = __DIR__.'/res/')
    {
        $analyzer = new SignaturesAnalyzer();
        $files = scandir($directory);
        $dirs = [];
        echo PHP_EOL."Scanning $directory";
        foreach ($files as $file) {
            if ($file === "." || $file === "..")
                continue;
            if (is_dir($directory.$file)) {
                array_push($dirs, $directory.$file.'/');
            } elseif (preg_match('/\.php$/', $file)) {
                $result = $analyzer->analyze(file_get_contents($directory.$file));
                echo PHP_EOL."Score: $result File: $file";
                $this->assertTrue($result >= 0 && $result <= 1, "Result should be between 0 and 1");
            }
        }
        foreach ($dirs as $dir) {
            $this->testScanFile($dir);
        }
    }
}
