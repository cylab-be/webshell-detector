<?php
namespace AnalyzerNS;

class Analyzer
{
    
    private $fileName;
    private $fileContent;
    
    public function analyze($pFileName)
    {
        if (!$pFileName || !is_string($pFileName)) {
            $this->kill("No file");
        } else {
            $this->fileName= $pFileName;
            $this->fileContent = file_get_contents($this->fileName);
        }
    }
    
    
    /**
     * //FIXME kill properly
     * @param string $message
     */
    private function kill($message)
    {
        die($message);
    }
    
    /**
     * Searches for non-ASCII characters, often used in obfuscated files
     * @return number
     */
    private function searchNonASCIIChars()
    {
        $count = 0;
        for ($i = 0; $i < strlen($this->fileContent); $i++) {
            if (ord($this->fileContent[$i]) > 0x7f) {
                $count++;
            }
        }
        return $count/strlen($this->fileContent);
    }
    
    /**
     * Wrapper for tests
     * @param $func
     * @return ? value of the called function
     */
    public function testMe($func)
    {
        return $this->$func();
    }
}
