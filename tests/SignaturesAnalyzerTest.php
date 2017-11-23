<?php
namespace Tests;

require __DIR__ . "/../src/SignaturesAnalyzer.php";

use PHPUnit\Framework\TestCase;
use AnalyzerNS\SignaturesAnalyzer;

class SignaturesAnalyzerTest extends TestCase
{
    
    
    public function testScanFile()
    {
        $analyzer = new SignaturesAnalyzer();
        //$this->assertTrue($analyzer->scanFile(file_get_contents(__DIR__."/res/c.php")) != null);
        $this->assertTrue($analyzer->scanFile(file_get_contents(__DIR__."/res/enc_c.php")) != null);
    }
}
