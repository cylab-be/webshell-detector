<?php
namespace RUCD\WebshellDetector;

require_once 'const.php';

interface Analyzer
{
    public function analyze($fileName);
    public function testMe($func);
}
