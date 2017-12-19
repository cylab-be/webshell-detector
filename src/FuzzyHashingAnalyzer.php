<?php
/**
 * File FuzzyHashingAnalyzer
 *
 * @file     FuzzyHashingAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;

use webd\language\SpamSum;
use webd\language\LCS;
/**
 * Class FuzzyHashingAnalyzer. Computes a hash using a Context-Triggered Piecewise Hashing algorithm.
 * This kind a algorithm can be used to detect similar files. Whereas hash functions like MD5 or SHA-256 will produce
 * different strings even if inputs are close, fuzzy hashing will produce similar outputs.
 * See more: http://dfrws.org/sites/default/files/session-files/paper-identifying_almost_identical_files_using_context_triggered_piecewise_hashing.pdf   
 *
 * @file     FuzzyHashingAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class FuzzyHashingAnalyzer implements Analyzer
{
    const FUZZY_HASH_FILE = "shells_fuzzyhash.txt";
    const MIN_FUZZY_SCORE = 20;
    
    private $_spasum;
    
    /**
     * Constructor of the class FuzzyHashingAnalyzer
     */
    public function __construct()
    {
        $this->_spasum = new SpamSum;
    }
    
    /**
     * Iterate over known hashes and check if the hash of the current string matches
     * {@inheritDoc}
     * 
     * @param string $filecontent The content of the file to compare
     * 
     * @see \RUCD\WebshellDetector\Analyzer::analyze()
     * 
     * @return -1 if an error occures, 0 if nothing matches, the score between 0 and 100 otherwise
     */
    public function analyze($filecontent)
    {
        $filename = __DIR__."/../res/". self::FUZZY_HASH_FILE;
        if (file_exists($filename) && $filecontent != null && is_string($filecontent)) {
            $hashes = file($filename, FILE_IGNORE_NEW_LINES);
            $currhash = $this->_spasum->HashString($filecontent)->__toString();
            if ($currhash != null) {
                $score = 0;
                $match = 0;
                foreach ($hashes as $hash) {
                    $lcs = new LCS($hash, $currhash);
                    $tmpscore = 100-$lcs->distance();
                    if ($tmpscore > self::MIN_FUZZY_SCORE) {
                        $score += $tmpscore;
                        $match++;
                    }
                }
                return $match ? $score/$match : 0;
            }
        }
        return self::EXIT_ERROR;
    }
}