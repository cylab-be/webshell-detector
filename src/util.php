<?php
namespace Analyzer;

/**
 * Apply the strpos function with an array of parameters
 * @param string $haystack
 * @param array $arrayOfWords words to search in the haystack
 * @return int|boolean
 */
function strposOnArray($haystack, $arrayOfWords)
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
}
