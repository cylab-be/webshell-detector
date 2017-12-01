<?php
namespace RUCD\WebshellDetector;

use PHPUnit\Framework\TestCase;
use RUCD\WebshellDetector\Util;

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
            $this->assertTrue(Util::removeCRLF($string) !== $string);
        }
    }

    public function testRemoveMultiWhiteSpaces()
    {
        foreach ($this->strings as $string) {
            $this->assertTrue(Util::removeMultiWhiteSpaces($string) !== $string);
        }
    }

    public function testRemoveAllWhiteSpaces()
    {
        foreach ($this->strings as $string) {
            $this->assertTrue(Util::removeAllWhiteSpaces($string) !== $string);
        }
    }

    public function testRemoveWhiteSpacesOutsideString()
    {
        foreach ($this->strings as $string) {
            $this->assertTrue(Util::removeWhiteSpacesOutsideString(token_get_all($string)) !== $string);
        }
    }
}
