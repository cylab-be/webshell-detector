<?php
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;
use RUCD\WebshellDetector\ExeAnalyzer;

class ExeAnalyzerTest extends TestCase
{

    public function testTestMe()
    {
        $analyzer = new ExeAnalyzer();
        $result = $analyzer->analyze(
                file_get_contents(__DIR__."/res/test.php"));

        $this->assertTrue(
                $result >= 0 && $result <= 1,
                "result should be >= 0 and <= 1");
    }
}
