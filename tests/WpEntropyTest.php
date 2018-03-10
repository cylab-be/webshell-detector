<?php
/**
 * File WpEntropyTest.php
 * 
 * @file     WpEntropyTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;

/**
 * Class WpEntropyTest extending TestCase. Compute entropy for Wordpress files
 *
 * @file     WpEntropyTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class WpEntropyTest extends TestCase
{
    
    /**
     * Computes min, max and average. For now, prints 'Min: 4.0269868333593, max: 5.5184645933068
     *
     * @return void
     */
    public function testEntropy()
    {
        if (!file_exists(__DIR__.'/res/wordpress/')) {
            return;
        }
        $res = $this->_scanDir();
        $min = 10.0;
        $max = 0.0;
        foreach ($res as $result) {
            if ($result < $min) {
                $min = $result;
            } elseif ($result > $max) {
                $max = $result;
            }
        }
        echo PHP_EOL."Min: $min, max: $max"; 
    }
    
    /**
     * Scans directory and computes entropy for each file
     * 
     * @param string $directory The directory to scan
     * 
     * @return array An array containing all values
     */
    private function _scanDir($directory = __DIR__.'/res/wordpress/')
    {
        $analyzer = new EntropyAnalyzer();
        $scores = [];
        $files = scandir($directory);
        $dirs = [];
        foreach ($files as $file) {
            if ($file === "." || $file === "..") {
                continue;
            }
            if (is_dir($directory.$file)) {
                array_push($dirs, $directory.$file.'/');
            } elseif (preg_match('/\.php$/', $file)) {
                $result = $analyzer->computeEntropy(file_get_contents($directory.$file));
                array_push($scores, $result);
                $this->assertTrue($result >= 0, "Result should be greater than 0");
            }
        }
        foreach ($dirs as $dir) {
            $scores = array_merge($scores, $this->_scanDir($dir));
        }
        return $scores;   
    }
}
