<?php
/**
 * File ObfuscationAnalyzer
 *
 * @file     ObfuscationAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

/**
 * Class ObfuscationAnalyzer implementing Analyzer
 * Looks for clues about obfuscation
 * 
 * @file     ObfuscationAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class ObfuscationAnalyzer implements Analyzer
{

    const LOW = 1;

    const MEDIUM = 2;

    const SEVERE = 3;

    /**
     * Analyzes the content and tries to determine if the code was obfuscated
     * {@inheritDoc}
     * 
     * @param string $filecontent The content of the file to analyze
     * 
     * @see \RUCD\WebshellDetector\Analyzer::analyze()
     * 
     * @return void
     */
    public function analyze($filecontent)
    {
        if ($filecontent === null || ! is_string($filecontent)) {
            return self::EXIT_ERROR;
        }
        $tokens = token_get_all($filecontent);
        $scores = [];
        $scores[0][0] = Util::searchNonASCIIChars($filecontent);
        $scores[0][1] = self::LOW;
        $scores[1][0] = $this->_getLongestString($filecontent);
        $scores[1][1] = self::LOW;
        $scores[2][0] = $this->_searchDecodingRoutines($tokens);
        $scores[2][1] = self::MEDIUM;
        return $this->_computeScore($scores);
    }
    
    /**
     * Searches decoding routines (base64, gzip etc)
     * 
     * @param array $tokens Tokens contained in the text
     * 
     * @return number Ratio of encoding routines
     */
    private function _searchDecodingRoutines($tokens)
    {
        $decode = ["base64_decode", "gzuncompress", "gzinflate", "gzdecode", "hex2bin", "convert_uudecode", "str_rot13"];
        $func = 0;
        $decodeFunc = 0;
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] == T_STRING) {
                $func++;
                if (in_array($token[1], $decode)) {
                    $decodeFunc++;
                }
            }
        }
        return $func ? $decodeFunc/$func : 0;
    }
        
    /**
     * Looks for the longest encapsulated string in the code 
     * 
     * @param string $string The code to analyze
     * 
     * @return number the ratio compared to the average
     */
    private function _getLongestString($string)
    {
        $tokens = token_get_all($string);
        $maxsize = 0.0;
        $sumsize = 0.0;
        $nbstrings = 0.0;
        foreach ($tokens as $token) {
            if (is_array($token) && ($token[0] === T_CONSTANT_ENCAPSED_STRING || $token[0] === T_ENCAPSED_AND_WHITESPACE)) {
                $nbstrings ++;
                $strlength = strlen($token[1]);
                $sumsize += $strlength;
                if ($strlength > $maxsize) {
                    $maxsize = $strlength;
                }
            }
        }
        if ($nbstrings === 0.0 || $sumsize === 0.0) {
            return 0;
        }
        if ($nbstrings == 1) return 1;
        $scale = ($maxsize / ($sumsize / $nbstrings)) - 1;
        return $scale > 1 ? 1 : $scale; // FIXME may be too restrictive
    }

    /**
     * Computes the weighted averages of sub-scores
     *
     * @param float $scores All sub-scores
     * 
     * @return number The final score
     */
    private function _computeScore($scores)
    {
        $score = 0.0;
        $sumweight = 0.0;
        for ($i = 0; $i < count($scores); $i ++) {
            if ($scores[$i][0] != self::EXIT_ERROR) {
                $score += ($scores[$i][0] * $scores[$i][1]);
                $sumweight += $scores[$i][1];
            }
        }
        return $sumweight ? $score / $sumweight : 0;
    }
}