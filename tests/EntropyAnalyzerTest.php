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
     * @param string $dir Name of the directory, by default __DIR__/res/
     *
     * @return void
     */
    public function testEntropyAnalyzer($dir = __DIR__."/res/php-webshells-master/")
    {
        $analyzer = new EntropyAnalyzer();
        $files = scandir($dir);
        $dirs = [];
        echo PHP_EOL."Scanning $dir";
        foreach ($files as $file) {
            if ($file === "." || $file === "..") {
                continue;
            }
            if (is_dir($dir.$file)) {
                array_push($dirs, $dir.$file.'/');
            } elseif (preg_match('/\.php$/', $file)) {
                $result = $analyzer->analyze($dir.$file);
                echo PHP_EOL."Entropy: $result File: $file";
                $this->assertTrue($result >= 0, "Result should be >= 0");
            }
        }
        foreach ($dirs as $d) {
            $this->testEntropyAnalyzer($d);
        }
    }
}
