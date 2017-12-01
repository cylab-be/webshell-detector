<?php

namespace RUCD\WebshellDetector;

class Detector {

    private $anayzers = [];

    public function __construct() {
        $this->anayzers[] = new ExeAnalyzer();
        $this->anayzers[] = new SignaturesAnalyzer();
    }

    /**
     * Recursively scan a directory, and scan all files.
     *
     * @param type $directory
     */
    public function analyzeDirectory($directory) {

    }

    /**
     * Analyze a file.
     * @param type $filename
     * @return type
     */
    public function analyzeFile($filename) {
        return $this->analyzeString(file_get_contents($filename));
    }

    /**
     * Analyze a string and return a score between 0 (harmless) and 1 (highly
     * suspicious).
     *
     * @param type $string
     * @return type
     */
    public function analyzeString($string) {

        $scores = [];
        foreach ($this->anayzers as $analyzer) {
            $scores[] = $analyzer->analyze($string);
        }
        return $this->aggregate($scores);
    }

    private function aggregate($scores) {
        // For now, perform a simple average
        return array_sum($scores) / count($scores);
    }
}
