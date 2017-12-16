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
        return $this->_searchExecCmdFunctions($string);
    }

    /**
     * Basic. Searches dangerous function names allowing to execute commands
     * 
     * @param string $string The string to analyze
     *
     * @return boolean. True if dangerous functions are found.
     */
    private function _searchExecCmdFunctions($string)
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
    
}
