<?php
namespace Tests;

require __DIR__ . "/../src/ExeAnalyzer.php";

use PHPUnit\Framework\TestCase;
use AnalyzerNS\ExeAnalyzer;

class ExeAnalyzerTest extends TestCase
{
    
    public function testTestMe()
    {
        $analyzer = new ExeAnalyzer();
        $analyzer->analyze(__DIR__."/res/test.php");
        $this->assertTrue($analyzer->testMe("searchExecCmdFunctions") >0);
    }
}
