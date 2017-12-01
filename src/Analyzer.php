<?php
namespace AnalyzerNS;

require_once 'const.php';
require_once 'util.php';

interface Analyzer
{
    public function analyze($fileName);
    public function testMe($func);
}
