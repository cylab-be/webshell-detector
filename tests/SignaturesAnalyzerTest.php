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
        file_put_contents(__DIR__."/res/enc_c.php", "<?php
        \$a=\"".base64_encode(file_get_contents(__DIR__."/res/c_str.txt"))."\";
        \$a.=\"===\";
        \$b=base64_decode(\$a);
        echo \$b;");
        //file_put_contents(__DIR__."/res/enc_c.php", "<?php\n\$a=base64_decode(gzuncompress( base64_decode(\"".base64_encode(gzcompress(base64_encode(file_get_contents(__DIR__."/res/c_str.txt"))))."\".\"===\")));");
        $flag1 = $analyzer->analyze(__DIR__."/res/c.php");
        $flag2 = $analyzer->analyze(__DIR__."/res/enc_c.php");
        echo PHP_EOL.'Flag 1: '.$flag1;
        echo PHP_EOL.'Flag 2: '.$flag2;
        echo PHP_EOL;
        $this->assertTrue($flag1 != null);
        $this->assertTrue($flag2 != null);
    }
}
