<?php
namespace RUCD\WebshellDetector;

interface Analyzer
{
    /**
     * Analyze a string and return a score between 0 (harmless) and 1 (highly
     * suspicious).
     *
     * @param type $string
     */
    public function analyze($string);
}
