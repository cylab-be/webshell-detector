<?php
namespace RUCD\WebshellDetector;

class Detector
{
    private $anayzers = [];

    public function __construct() {
        $this->anayzers[] = new ExeAnalyzer();
        $this->anayzers[] = new SignaturesAnalyzer();
    }

    public function analyzeString($string) {

        $scores = [];
        foreach ($this->anayzers as $analyzer) {
            $scores[] = $analyzer->analyze($string);
        }
        return $this->aggregate($scores);
    }

    private function aggregate($scores) {
        return $scores[0];
    }
}
