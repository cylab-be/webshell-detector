<?php
/**
 * Class ExeAnalyzer
 *
 * @file     ExeAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;
/**
 * Class ExeAnalyzer implementing Analyzer
 * Performs an analysis of the file, looking dangerous routines
 *
 * @file     ExeAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class ExeAnalyzer implements Analyzer
{

    /**
     * Performs an analysis on the given string, regarding dangerous routine
     * {@inheritDoc}
     * 
     * @param string $string The string to analyze
     * 
     * @see \RUCD\WebshellDetector\Analyzer::analyze()
     * 
     * @return int The score of the given string
     */
    public function analyze($string)
    {
        $ret = $this->_searchExecCmdFunctions($string);
        return $ret ? $ret : $this->_searchExecCmdFunctions(preg_replace('/<\?\n/', '<?php'.PHP_EOL, $string));
    }

    /**
     * Basic. Searches dangerous function names allowing to execute commands
     * 
     * @param string $string The string to analyze
     *
     * @return int. Number of dangerous functions
     */
    private function _searchExecCmdFunctions($string)
    {
        $count = 0;
        $nbroutines = 0;
        $tokens = token_get_all($string);
        if (count($tokens) === 0) {
            return 0;
        }
        $funcs = array("exec", "passthru", "popen", "proc_open", "pcntl_exec", "shell_exec", "system");
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_STRING) {
                $nbroutines++;
                foreach ($funcs as $func) {
                    if ($token[1] === $func) {
                        $count++;
                    }
                }
            }
            elseif ($token[0] === T_EVAL)
                $count++;
            elseif ($token === "`")
                $count+=0.5;
        }
        return $nbroutines ? $count/$nbroutines : 0.0;
    }
}
