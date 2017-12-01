<?php
namespace RUCD\WebshellDetector;

class ExeAnalyzer implements Analyzer
{

    public function analyze($string)
    {
        return $this->searchExecCmdFunctions($string);
    }

    /**
     * Basic. Searches dangerous function names allowing to execute commands
     *
     * @return boolean. True if dangerous functions are found.
     */
    private function searchExecCmdFunctions($string)
    {
        $tokens = token_get_all($string);
        $funcs = array("exec", "passthru", "popen", "proc_open", "pcntl_exec", "shell_exec", "system");
        if (Util::strposOnArray($string, $funcs) === false) {
            foreach ($tokens as $token) {
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
     *
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
}
