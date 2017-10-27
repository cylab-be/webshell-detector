<?php
    namespace Tests;
    use PHPUnit\Framework\TestCase;
    require_once '../src/Analyzer.php';
    
    class AnalyzerTest extends TestCase{
        
        public function testTestMe(){
            $analyzer = new \Analyzer();
            $analyzer->analyze("../src/test.php");
            $this->assertTrue($analyzer->testMe("searchNonASCIIChars") >0);
        }
    }
?>