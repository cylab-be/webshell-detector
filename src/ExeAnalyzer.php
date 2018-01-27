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
        $string = preg_replace('/<\?\n/', '<?php'.PHP_EOL, $string);
        $tokens = token_get_all($string);
        if (count($tokens) === 0)
            return 0;
        $nbFunc = 0;
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_STRING)
                $nbFunc++;
        }
        //Util::printTokens($tokens);
        $retExec = $this->_searchExecCmdFunctions($tokens);
        $retAnonymous = $this->_searchAnonymousFunctions($tokens);
        $retVarFunc = $this->_searchVariableFunctions($tokens);
        echo PHP_EOL."Exec: $retExec \nAnonymous: $retAnonymous \nVariable Func: $retVarFunc";
        return $nbFunc ? (($retExec * 2 + $retAnonymous + $retVarFunc) / 4) / $nbFunc : 0;
    }

    /**
     * Basic. Searches dangerous function names allowing to execute commands
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
            //echo (is_array($token) ? $token[1] : $token).PHP_EOL;
            if (is_array($token) && $token[0] === T_STRING) {
                if (in_array($token[1], $funcs))
                    $count++;
            }
        }
        return $count;
    }
    
    /**
     * Looks for variable functions, such as:
     * $foo = function() {...}
     * $foo()
     * OR
     * $foo = 'some_function_name';
     * $foo()
     * 
     * @param array $tokens Tokens of the code
     * 
     * @return number Number of variable functions
     */
    private function _searchVariableFunctions($tokens)
    {
        $state = 0;
        $matches = 0;
        $nestedParenthesis = 0;
        //$stateStr = '';
        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];
            if ($state == 0 && is_array($token) && $token[0] === T_VARIABLE) {
                //look for variable
                $state = 1;
                //$stateStr.= $token[1];
            } elseif ($state == 1) {
                //look for left parenthesis
                if (is_array($token) && $token[0] === T_WHITESPACE)
                    continue;
                elseif ($token === "(") {
                    $state = 2;
                    //$stateStr.='(';
                } else {
                    $state = 0;
                    //$stateStr = '';
                }
            } elseif ($state == 2) {
                //look for param
                if (is_array($token) && $token[0] === T_WHITESPACE)
                    continue;
                elseif ($token === "(")
                    $nestedParenthesis++;
                elseif ($token === ")") {
                    if ($nestedParenthesis != 0)
                        $nestedParenthesis--;
                    else {
                        $state = 0;
                        //echo PHP_EOL.$stateStr.")";
                        //$stateStr = '';
                        $matches++;
                    }
                }
            }
        }
        //TODO can't handle nested func
        return $matches;
    }
}
