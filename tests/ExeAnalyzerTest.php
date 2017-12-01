<?php
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;
use RUCD\WebshellDetector\ExeAnalyzer;

class ExeAnalyzerTest extends TestCase
{

    public function testTestMe()
    {
        $analyzer = new ExeAnalyzer();
        $analyzer->analyze(__DIR__."/res/test.php");
        $this->assertTrue($analyzer->testMe("searchExecCmdFunctions") >0);
    }
}
