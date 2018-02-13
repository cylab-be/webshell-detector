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
     * Computes the average of an array
     * 
     * @param array $array The array of numbers to analyze
     * 
     * @return number The average
     */
    public static function average($array)
    {
        if (!is_array($array) || count($array) == 0)
            return 0;
        return floatval(array_sum($array))/count($array);
    }
    
    /**
     * Sometimes, a PHP code starts only with '<?' and token_get_all cannot perform parsing properly (the code will be 
     * considered as HTML code). So we extend the open tag
     * 
     * @param string $string The PHP code
     * 
     * @return string The code with an extended open tag
     */
    public static function extendOpenTag($string)
    {
        return preg_replace('/<\?[\s*|\n]/', '<?php'.PHP_EOL, $string);
    }
    
    /**
     * Reduce and sort an an array, keeping the most meaningful values
     * 
     * @param array $array The array to analyze
     * 
     * @return array The new "meaningful" array
     */
    public static function getMeaningfulArray($array)
    {
        $array = array_filter($array);
        sort($array);
        $a = intval(count($array)*0.1);
        $b = intval(count($array)*0.9);
        return array_slice($array, $a, $b - $a);
    }
    
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
        $res = [];
        if (!$tokens) {
            return null;
        }
        foreach ($tokens as $x) {
            if (is_array($x) && $x[0] === T_WHITESPACE)
                continue;
            $res[] = $x;
        }
        return $res;
    }
    
    /**
     * Searches for non-ASCII characters, often used in obfuscated files
     *
     * @param string $string The search to analyze
     * 
     * @return number Number of non-ASCII chars in the string
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
        return $count;
    }
    
    /**
     * Computes the standard deviation of an array of numbers
     * 
     * @param array $array Array of numbers
     * 
     * @return number The standard deviation
     */
    public static function standardDeviation($array)
    {
        if (!is_array($array) || count($array) == 0) {
            return 0.0;
        }
        $avg = floatval(array_sum($array))/count($array);
        $variance = 0.0;
        foreach ($array as $var) {
            $variance += pow($var - $avg, 2);
        }
        return sqrt($variance/count($array));
    }
}
