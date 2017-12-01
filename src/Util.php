<?php
namespace RUCD\WebshellDetector;

class Util
{

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
     * @param  string $haystack
     * @param  array  $arrayOfWords words to search in the haystack
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
     * @param  $string
     * @return NULL|$string
     */
    public static function removeCRLF($string)
    {
        return $string ? str_replace(PHP_EOL, '', $string) : null;
    }

    /**
     * Removes whites spaces if the are repeateds
     *
     * @param  $string
     * @return NULL|string without repeated white spaces
     */
    public static function removeMultiWhiteSpaces($string)
    {
        return $string ? preg_replace('/\s{2,}/', ' ', $string) : null;
    }

    /**
     * Removes all whites spaces
     *
     * @param  $string
     * @return NULL|string whitout any white spaces
     */
    public static function removeAllWhiteSpaces($string)
    {
        return $string ? preg_replace('/\s+/', ' ', $string) : null;
    }

    /**
     * Removew white spaces outside strings
     *
     * @param  $string
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
}
