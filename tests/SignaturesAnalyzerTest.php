<?php
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;
use RUCD\WebshellDetector\SignaturesAnalyzer;

class SignaturesAnalyzerTest extends TestCase
{

    public function testScanFile()
    {
        $analyzer = new SignaturesAnalyzer();
        $flag1 = $analyzer->analyze(
                file_get_contents(__DIR__ . "/res/c.php"));
        $flag2 = $analyzer->analyze(
                file_get_contents(__DIR__ . "/res/enc_c.php"));
        $this->assertTrue($flag1 != null);
        $this->assertTrue($flag2 != null);
        $this->assertTrue(is_double($flag1), "Result should be a number");
        $this->assertTrue($flag1 >= 0, "Result should be >= 0");
        $this->assertTrue($flag <= 1, "Result should be <= 1");
    }
}
