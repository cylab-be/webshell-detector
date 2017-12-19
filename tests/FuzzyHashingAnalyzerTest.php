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
 * Class FuzzyHashingAnalyzerTest. Performs tests on the class FuzzyHashingAnalyzer
 *
 * @file     FuzzyHashingAnalyzerTest
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
        $spamsum = new SpamSum();
        /*$hash = $spamsum->Hash(file_get_contents(__DIR__.'/res/c.php'))->__toString();
        $hash1 = $spamsum->Hash(file_get_contents(__DIR__.'/res/c_str.txt'))->__toString();
        file_put_contents(__DIR__.'/../res/shells_fuzzyhash.txt', $hash.PHP_EOL.$hash1);*/
        $val = $detector->analyze(file_get_contents(__DIR__.'/res/c.php'));
        echo "Score: ".$val;
        $this->assertTrue($val > 20 && $val <= 100);
    }
    
}