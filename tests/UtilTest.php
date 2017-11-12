<?php
namespace Tests;

require_once __DIR__."/../src/util.php";

use PHPUnit\Framework\TestCase;
use \AnalyzerNS as util;

class UtilTest extends TestCase
{
    private $strings = ["<?php\n
    \$a =               10;
    \$b = 'phpi     nfo';
    
    echo \$a;
    \$b();?>",
    "<?php function inline(){return             true;}
    phpinfo();?>"
    ];
    
    public function testRemoveCRLF()
    {
        foreach ($this->strings as $string) {
            $this->assertTrue(util\removeCRLF($string) !== $string);
        }
    }
    
    public function testRemoveMultiWhiteSpaces()
    {
        foreach ($this->strings as $string) {
            $this->assertTrue(util\removeMultiWhiteSpaces($string) !== $string);
        }
    }
    
    public function testRemoveAllWhiteSpaces()
    {
        foreach ($this->strings as $string) {
            $this->assertTrue(util\removeAllWhiteSpaces($string) !== $string);
        }
    }
    
    public function testRemoveWhiteSpacesOutsideString()
    {
        foreach ($this->strings as $string) {
            $this->assertTrue(util\removeWhiteSpacesOutsideString(token_get_all($string)) !== $string);
        }
    }
}
