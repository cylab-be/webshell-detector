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
     * @param string $directory Name of the directory, by default __DIR__/res/
     *
     * @return void
     */
    public function testFuzzyHashing($directory = __DIR__.'/res/webshells_modified/')
    {
        $detector = new FuzzyHashingAnalyzer();
        /*$this->writeInFile();
        exit();*/
        $files = scandir($directory);
        $dirs = [];
        echo PHP_EOL."Scanning $directory";
        foreach ($files as $file) {
            if ($file === "." || $file === "..")
                continue;
            if (is_dir($directory.$file)) {
                array_push($dirs, $directory.$file.'/');
            } elseif (preg_match('/\.php$/', $file)) {
                $result = $detector->analyze(file_get_contents($directory.$file));
                echo PHP_EOL."Score: $result File: $file";
                $this->assertTrue($result >= 0 && $result <= 1, "Result should be between 0 and 1");
            }
        }
        foreach ($dirs as $dir) {
            $this->testFuzzyHashing($dir);
        }
    }

    /**
     * Writes spamsum hashes in the resource file
     * 
     * @param string $dir Name of the the directory to scan
     * 
     * @return void
     */
    public function writeInFile($dir = __DIR__ . "/res/")
    {
        $files = scandir($dir);
        $spamsum = new SpamSum();
        $towrite = '';
        $dirs = [];
        foreach ($files as $file) {
            if ($file === "." || $file === ".." || $file === "harmless.php" || $file === "test.php")
                continue;
            elseif (is_dir($dir.$file) && $dir.$file !== 'wordpress')
                array_push($dirs, $dir.$file.'/');
            elseif (preg_match('/\.php$/', $file)) {
                $text = file_get_contents($dir . $file);
                $text = Util::removeAllWhiteSpaces($text);
                $res = $spamsum->Hash($text)->__toString();
                $towrite .= $res . PHP_EOL;
            }
        }
        $filepath = __DIR__ . '/../res/shells_fuzzyhash.txt';
        if (file_exists($filepath)) {
            file_put_contents($filepath, $towrite, FILE_APPEND);
        } else {
            file_put_contents($filepath, $towrite);
        }
        foreach ($dirs as $dir) {
            $this->writeInFile($dir);
        }
    }
}