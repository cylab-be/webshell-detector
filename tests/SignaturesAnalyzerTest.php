<?php
namespace Tests;

require __DIR__ . "/../src/SignaturesAnalyzer.php";

use PHPUnit\Framework\TestCase;
use AnalyzerNS\SignaturesAnalyzer;

class AnalyzerTest extends TestCase
{
    
    public function testTestMe()
    {
        $analyzer = new SignaturesAnalyzer();
        $this->assertTrue($analyzer->scanFile(file_get_contents(__DIR__."/res/c.php")) == null);
    }
}
