<?php
/**
 * File ObfuscationAnalyzerTest
 *
 * @file     ObfuscationAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;

/**
 * Class ObfuscationAnalyzerTest.
 * Performs tests on the class ObfuscationAnalyzerTest
 *
 * @file ObfuscationAnalyzerTest
 *
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class ObfuscationAnalyzerTest extends TestCase
{
    /**
     * Performs test on the class ObfuscationAnalyzer
     * 
     * @param string $directory The directory to scan
     * 
     * @return void
     */
    public function testObfuscationAnalyzer($directory = __DIR__.'/res/php-webshells-master/')
    {
        $analyzer = new ObfuscationAnalyzer();
        $files = scandir($directory);
        $dirs = [];
        echo PHP_EOL."Scanning $directory".PHP_EOL;
        foreach ($files as $file) {
            if ($file === "." || $file === "..") {
                continue;
            }
            if (is_dir($directory.$file)) {
                array_push($dirs, $directory.$file.'/');
            } elseif (preg_match('/\.php$/', $file)) {
                echo "File $file ";
                $result = $analyzer->analyze(file_get_contents($directory.$file));
                echo " Score: $result".PHP_EOL;
                $this->assertTrue($result >= 0 && $result <= 1, "Result should be between 0 and 1");
            }
        }
        foreach ($dirs as $dir) {
            $this->testObfuscationAnalyzer($dir);
        }
    }
    
    /**
     * Peforms test on a single file
     * 
     * @return void
     * 
    public function testObfuscationSingleFile()
    {
        $analyzer = new ObfuscationAnalyzer();
        $result = $analyzer->analyze(file_get_contents(__DIR__.'/res/php-webshells-master/Uploader.php'));
        echo PHP_EOL."Score: $result";
        $this->assertTrue($result >= 0 && $result <= 1, "Result should be between 0 and 1");
    }
     */
}