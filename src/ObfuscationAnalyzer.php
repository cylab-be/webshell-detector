<?php

namespace RUCD\WebshellDetector;

/**
 * Class ObfuscationAnalyzer implementing Analyzer
 * Looks for clues about obfuscation
 *
 * @file     ObfuscationAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://github.com/RUCD/webshell-detector/blob/master/LICENSE MIT
 * @link     https://github.com/RUCD/webshell-detector
 */
class ObfuscationAnalyzer implements Analyzer
{

    const LOW = 1;
    const MEDIUM = 2;
    const SEVERE = 3;
    const ASCII_MIN = 3;
    const ASCII_MAX = 12;
    const LONGEST_MIN = 33;
    const LONGEST_MAX = 85;
    const DECODE_MIN = 1;
    const DECODE_MAX = 4;

    /**
     * Analyzes the content and tries to determine if the code was obfuscated
     * {@inheritDoc}
     *
     * @param string $fileContent The code to analyze
     *
     * @see \RUCD\WebshellDetector\Analyzer::analyze()
     *
     * @return void
     */
    public function analyze($fileContent)
    {
        if ($fileContent === null || ! is_string($fileContent)) {
            return self::EXIT_ERROR;
        }
        $score = $this->getScores($fileContent);
        $nonascii = $score[0];
        $longest = $score[1];
        $decode = $score[2];
        if ($nonascii < self::ASCII_MIN) {
            $nonascii = 0.0;
        } elseif ($nonascii > self::ASCII_MAX) {
            $nonascii = 1.0;
        } else {
            $nonascii = ($nonascii - self::ASCII_MIN) /
                    (self::ASCII_MAX-self::ASCII_MIN);
        }

        if ($longest < self::LONGEST_MIN) {
            $longest = 0.0;
        } elseif ($longest > self::LONGEST_MAX) {
            $longest = 1.0;
        } else {
            $longest = ($longest - self::LONGEST_MIN) /
                    (self::LONGEST_MAX-self::LONGEST_MIN);
        }

        if ($decode < self::DECODE_MIN) {
            $decode = 0.0;
        } elseif ($decode > self::DECODE_MAX) {
            $decode = 1.0;
        } else {
            $decode = ($decode- self::DECODE_MIN) /
                    (self::DECODE_MAX-self::DECODE_MIN);
        }

        return ($nonascii * self::LOW
                + self::MEDIUM * $longest
                + self::SEVERE * $decode) / 6.0;
    }

    /**
     * Computes score and return a 3-array
     *
     * @param string $filecontent The code to analyze
     *
     * @return number[] 3-array: non-ascii;longest string size;decoding routines
     */
    public function getScores($filecontent)
    {
        $filecontent = Util::extendOpenTag($filecontent);
        $tokens = token_get_all($filecontent);
        $scores = [];
        $scores[0] = floatval(Util::searchNonASCIIChars($filecontent));
        $scores[1] = floatval($this->_getLongestString($tokens));
        $scores[2] = floatval($this->_searchDecodingRoutines($tokens));
        return $scores;
    }

    /**
     * Searches decoding routines (base64, gzip etc)
     *
     * @param array $tokens Tokens contained in the text
     *
     * @return number Number of encoding routines
     */
    private function _searchDecodingRoutines($tokens)
    {
        $decode = [
            "base64_decode", "gzuncompress", "gzinflate", "gzdecode", "hex2bin",
            "convert_uudecode", "str_rot13", "strrev"];
        $decodeFunc = 0;
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_STRING) {
                if (in_array($token[1], $decode)) {
                    $decodeFunc++;
                }
            }
        }
        return $decodeFunc;
    }

    /**
     * Looks for the longest encapsulated string in the code
     *
     * @param array $tokens Tokens of the code to analyze
     *
     * @return number Size of the longest string
     */
    private function _getLongestString($tokens)
    {
        $maxsize = 0.0;
        foreach ($tokens as $token) {
            if (is_array($token)
                && ($token[0] === T_CONSTANT_ENCAPSED_STRING
                || $token[0] === T_ENCAPSED_AND_WHITESPACE)
            ) {
                $strlength = strlen($token[1]);
                if ($strlength > $maxsize) {
                    $maxsize = $strlength;
                }
            }
        }
        return $maxsize;
    }
}