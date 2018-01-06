<?php
/**
 * File FuzzyHashingAnalyzerTest
 *
 * @file     FuzzyHashingAnalyzerTest
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;
use webd\language\SpamSum;

/**
 * Class FuzzyHashingAnalyzerTest.
 * Performs tests on the class FuzzyHashingAnalyzer
 *
 * @file FuzzyHashingAnalyzerTest
 *
 * @category None
 * @package  Tests
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class FuzzyHashingAnalyzerTest extends TestCase
{

    /**
     * Runs the routine FuzzyHashingAnalyzer::analyze
     *
     * @return void
     */
    public function testFuzzyHashing()
    {
        $detector = new FuzzyHashingAnalyzer();
        //$this->writeInFile();
        $dir = __DIR__ . "/res/";
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === "." || $file === "..")
                continue;
            $val = $detector->analyze(file_get_contents($dir . $file));
            echo PHP_EOL . "Score: $val File $file"; //should print 0 except for harmless.php and test.php since hashes are based on these files
            $this->assertTrue($val >= 0 && $val <= 100);
        }
    }

    /**
     * Writes spamsum hashes in the resource file
     * 
     * @return void
     */
    public function writeInFile()
    {
        $dir = __DIR__ . "/res/";
        $files = scandir($dir);
        $spamsum = new SpamSum();
        $towrite = '';
        foreach ($files as $file) {
            if ($file == "." || $file === ".." || $file === "test.php" || $file === "harmless.php")
                continue;
            $text = file_get_contents($dir . $file);
            $res = $spamsum->Hash($text)->__toString();
            $towrite .= $res . PHP_EOL;
        }
        file_put_contents(__DIR__ . '/../res/shells_fuzzyhash.txt', $towrite);
    }
}