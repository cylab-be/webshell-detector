<?php
namespace RUCD\WebshellDetector;

class ExeAnalyzer implements Analyzer
{
    private $fileName;
    private $fileContent;
    private $tokens;

    public function analyze($pFileName)
    {
        if (!$pFileName || !is_string($pFileName)) {
            $this->kill("No file");
        } else {
            $this->fileName= $pFileName;
            $this->fileContent = file_get_contents($this->fileName);
            $this->tokens = token_get_all($this->fileContent);
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
     * Basic. Searches dangerous function names allowing to execute commands
     * @return boolean. True if dangerous functions are found.
     */
    private function searchExecCmdFunctions()
    {
        $funcs = array("exec", "passthru", "popen", "proc_open", "pcntl_exec", "shell_exec", "system");
        if (Util::strposOnArray($this->fileContent, $funcs) === false) {
            foreach ($this->tokens as $token) {
                if (!is_array($token) && $token === "`") {
                    return true;
                }
            }
            return false;
        }
        return true;
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
