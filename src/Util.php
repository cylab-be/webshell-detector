<?php
/**
 * File Util
 *
 * @file     Util
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

/**
 * Class Util. Contains some utilitarian routines
 *
 * @file     Util
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class Util
{
    /**
     * Prints the array of tokens
     * 
     * @param array $pTokens The array of tokens to print
     * 
     * @return void
     */
    public static function printTokens($pTokens)
    {
        if (!isset($pTokens) || !is_array($pTokens)) {
            return;
        }
        foreach ($pTokens as $token) {
            if (is_array($token)) {
                echo token_name($token[0]).': '.$token[1].PHP_EOL;
            } else {
                echo $token[0].PHP_EOL;
            }
        }
    }
    
    /**
     * Apply the strpos function with an array of parameters
     *
     * @param string $haystack     The string where the research is performed
     * @param array  $arrayOfWords words to search in the haystack
     * 
     * @return int|boolean
     */
    public static function strposOnArray($haystack, $arrayOfWords)
    {
        if (is_array($arrayOfWords)) {
            foreach ($arrayOfWords as $word) {
                $pos = strpos($haystack, $word);
                if ($pos !== false) {
                    return $pos;
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Removes all carriage returns and/or line feeds
     *
     * @param string $string The string to analyze
     * 
     * @return NULL|$string
     */
    public static function removeCRLF($string)
    {
        return $string ? str_replace(PHP_EOL, '', $string) : null;
    }

    /**
     * Removes whites spaces if the are repeateds
     *
     * @param string $string The string to analyze
     * 
     * @return NULL|string without repeated white spaces
     */
    public static function removeMultiWhiteSpaces($string)
    {
        return $string ? preg_replace('/\s{2,}/', ' ', $string) : null;
    }

    /**
     * Removes all whites spaces
     *
     * @param string $string The string to analyze
     * 
     * @return NULL|string whitout any white spaces
     */
    public static function removeAllWhiteSpaces($string)
    {
        return $string ? preg_replace('/\s+/', ' ', $string) : null;
    }

    /**
     * Remove white spaces outside strings
     *
     * @param array $tokens List of tokens
     * 
     * @return NULL|string
     */
    public static function removeWhiteSpacesOutsideString($tokens)
    {
        if (!$tokens) {
            return null;
        }
        $retString = '';
        foreach ($tokens as $x) {
            if (!is_array($x)) {
                $retString.=$x;
            } else {
                $retString.=(is_integer($x[0]) && $x[0] == T_WHITESPACE ? ' ':$x[1]);
            }
        }
        return $retString;
    }
    
    /**
     * Searches for non-ASCII characters, often used in obfuscated files
     *
     * @param string $string The search to analyze
     * 
     * @return number The percentage of non-ASCII chars in the string
     */
    public static function searchNonASCIIChars($string)
    {
        if ($string === null || !is_string($string)) {
            return Analyzer::EXIT_ERROR;
        }
        $count = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            if (ord($string[$i]) > 0x7f) {
                $count++;
            }
        }
        return $count/strlen($string);
    }
}
