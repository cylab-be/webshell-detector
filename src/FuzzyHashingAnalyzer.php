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
use webd\language\StringDistance;
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
    
    private $_spamsum;
    
    /**
     * Constructor of the class FuzzyHashingAnalyzer
     */
    public function __construct()
    {
        $this->_spamsum = new SpamSum;
    }
    
    /**
     * Iterate over known hashes and check if the hash of the current string matches
     * {@inheritDoc}
     * 
     * @param string $filecontent The content of the file to compare
     * 
     * @see \RUCD\WebshellDetector\Analyzer::analyze()
     * 
     * @return number between 0 (theoretically, nothing matched) and 1 (exact match)
     */
    public function analyze($filecontent)
    {
        $filename = __DIR__."/../res/". self::FUZZY_HASH_FILE;
        if ($filecontent != null && is_string($filecontent) && file_exists($filename)) {
            $filecontent = Util::removeAllWhiteSpaces($filecontent);
            $hashes = file($filename, FILE_IGNORE_NEW_LINES);
            $currhash = $this->_spamsum->HashString($filecontent)->__toString();
            if ($currhash != null) {
                $score = 1000;
                $match = '';
                foreach ($hashes as $hash) {
                    $tmpscore = StringDistance::Levenshtein($hash, $currhash);
                    if ($tmpscore < $score) {
                        $score = $tmpscore;
                        $match = $hash;
                    }
                }
                return 1-($score/max(strlen($currhash), strlen($match)));
            }
        }
        return self::EXIT_ERROR;
    }
}