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
    
    const MIN_EXEC = 0;
    
    const MAX_EXEC = 3;
    
    const MIN_ANONYMOUS = 1;
    
    const MAX_ANONYMOUS = 6;
   
    const MIN_VARFUNC = 1;
    
    const MAX_VARFUNC = 3;

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
        $scores = $this->getScores($string);
        $exec = $scores[0];
        $anonymous = $scores[1];
        $varfunc = $scores[2];
        
        if ($exec < self::MIN_EXEC)
            $exec = 0;
        elseif ($exec > self::MAX_EXEC)
            $exec = 1;
        else 
            $exec = ($exec - self::MIN_EXEC) / (self::MAX_EXEC - self::MIN_EXEC);
        
        if ($anonymous < self::MIN_ANONYMOUS)
            $anonymous = 0;
        elseif ($anonymous > self::MAX_ANONYMOUS)
            $anonymous = 1;
        else
            $anonymous = ($anonymous - self::MIN_ANONYMOUS) / (self::MAX_ANONYMOUS - self::MIN_ANONYMOUS);
        
        if ($varfunc < self::MIN_VARFUNC)
            $varfunc = 0;
        elseif ($varfunc > self::MAX_VARFUNC)
            $varfunc = 1;
        else
            $varfunc = ($varfunc - self::MIN_VARFUNC) / (self::MAX_VARFUNC - self::MIN_VARFUNC);
        
        return ($exec * 3 + $varfunc + $anonymous * 2)/6.0;
    }
    
    /**
     * Get scores as an 3-array
     * 
     * @param string $string The code to analyze
     * 
     * @return \RUCD\WebshellDetector\int.[]|number[]
     */
    public function getScores($string)
    {
        $tokens = token_get_all(Util::extendOpenTag($string));
        $retExec = $this->_searchExecCmdFunctions($tokens);
        $retAnonymous = $this->_searchAnonymousFunctions($tokens);
        $retVarFunc = $this->_searchVariableFunctions($tokens);
        return array($retExec, $retAnonymous, $retVarFunc);
    }

    /**
     * Searches dangerous function names allowing to execute commands
     * 
     * @param array $tokens Tokens of the code to analyze
     *
     * @return int. Number of dangerous functions
     */
    private function _searchExecCmdFunctions($tokens)
    {
        $count = 0;
        $funcs = array("exec", "assert", "passthru", "popen", "proc_open", "pcntl_exec", "shell_exec", "system");
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_STRING) {
                if (in_array($token[1], $funcs))
                    $count++;
            }
            elseif ($token[0] === T_EVAL)
                $count++;
            elseif ($token === "`")
                $count+=0.5;
        }
        return $count;
    }
    
    /**
     * Searches anonymous, user-defined and callback functions
     * 
     * @param array $tokens Tokens of the code
     * 
     * @return number Number and callback, user-defined or anonymous functions
     */
    private function _searchAnonymousFunctions($tokens)
    {
        $count = 0;
        $funcs = array(
            "array_diff_uassoc",
            "array_diff_ukey",
            "array_filter",
            "array_intersect_uassoc",
            "array_intersect_ukey",
            "array_map",
            "array_reduce",
            "array_udiff",
            "array_udiff_assoc",
            "array_uintersect",
            "array_uintersect_assoc",
            "array_uintersect_uassoc",
            "call",
            "call_user_func",
            "call_user_func_array",
            "create_function",
            "forward_static_call",
            "forward_static_call_array", 
            "preg_replace_callback",
            "preg_replace_callback_array",
            "register_shutdown_function",
            "register_tick_function",
            "set_exception_handler", 
            "usort");
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_STRING) {
                if (in_array($token[1], $funcs))
                    $count++;
            }
        }
        return $count;
    }
    
    /**
     * Looks for variable functions, such as:
     * $foo = function() {...};
     * $foo()
     * OR
     * $foo = 'some_function_name';
     * $foo();
     * 
     * @param array $tokens Tokens of the code
     * 
     * @return number Number of variable functions
     */
    private function _searchVariableFunctions($tokens)
    {
        $stack = array([0, ""]);
        $matches = 0;
        $nestedParenthesis = 0;
        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];
            if (end($stack)[0] === 0 && is_array($token) && $token[0] === T_VARIABLE) {
                $stack[key($stack)][0] = 1; //push variable
                $stack[key($stack)][1].= $token[1];
            } elseif (end($stack)[0] === 2) {
                if (is_array($token) && $token[0] === T_VARIABLE) {
                    $stack[] = [1, $token[1]]; //new nested variable
                } else {
                    if ($token === "(")
                        $nestedParenthesis++;
                    elseif ($token === ")") {
                        if ($nestedParenthesis != 0)
                            $nestedParenthesis--;
                        else {
                            array_pop($stack);
                            $matches++;
                            if (empty($stack))
                                $stack = array([0, ""]);
                        }
                    }
                }
            } elseif (end($stack)[0] === 1) {
                //look for left parenthesis
                if (is_array($token) && $token[0] === T_WHITESPACE)
                    continue;
                elseif ($token === "(") {
                    $stack[key($stack)][0] = 2;
                    $stack[key($stack)][1].='(';
                } else {
                    array_pop($stack);
                    if (empty($stack))
                        $stack = array([0, ""]);
                }
            }
        }
        return $matches;
    }
}
