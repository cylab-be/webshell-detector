<?php
namespace Tests;

require __DIR__ . "/../src/Analyzer.php";

use PHPUnit\Framework\TestCase;
use AnalyzerNS\Analyzer;

class AnalyzerTest extends TestCase
{
    
    public function testTestMe()
    {
        $analyzer = new Analyzer();
        $analyzer->analyze(__DIR__."/res/test.php");
        $this->assertTrue($analyzer->testMe("searchExecCmdFunctions") >0);
    }
}
