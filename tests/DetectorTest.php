<?php
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;
use RUCD\WebshellDetector\Detector;

class DetectorTest extends TestCase
{

    public function testAnalyzeString()
    {
        $detector = new Detector();
        $this->assertTrue(
                $detector->analyzeString('<?php exe("something") ?>') > 0,
                "The detector should return a score > 0 as the test contains"
                . "the exe function");
    }
}
