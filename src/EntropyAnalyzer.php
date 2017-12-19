<?php
/**
 * File EntropyAnalyzer
 *
 * @file     EntropyAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
namespace RUCD\WebshellDetector;
/**
 * Class EntropyAnalyzer implementing Analyzer
 * Please see: http://www.onlamp.com/pub/a/php/2005/01/06/entropy.html
 * Entropy means "amount of useful informations delivered by a source". If we see a set of random discrete variables,
 * we can define entropy as a noisy signal, where some values are expected, and some of them are not. Each one has a
 * probability, and the ones with a low probability are considered meaningful indicators.
 * Mathematically, the amount of information contained in a signal it then equal to the negative logarithm of the
 * probability associated to this signal:
 * Info(P(sig)) = -log(P(sig))
 * 
 * Kolmogorov axioms state that a probability is between 0 and 1, and taking the log in this range gives a negative value,
 * that's the reason why the result is multiplied by -1.
 * 
 * And then, the entropy of a text can be computed by summing all log weigthed by probabilities and multiplying the result by -1:
 * 
 * @file     EntropyAnalyzer
 * @category None
 * @package  Source
 * @author   Enzo Borel <borelenzo@gmail.com>
 * @license  https://raw.githubusercontent.com/RUCD/webshell-detector/master/LICENSE Webshell-detector
 * @link     https://github.com/RUCD/webshell-detector
 */
class EntropyAnalyzer implements Analyzer
{
    /**
     * Performs an analysis on a file regarding entropy
     * {@inheritDoc}
     * 
     * @param string $filecontent The content of the file to analyze
     * 
     * @see \RUCD\WebshellDetector\Analyzer::analyze()
     * 
     * @return int The score of the file
     */
    public function analyze($filecontent)
    {
        return $this->_computeEntropy($filecontent);
    }
    
    /**
     * Computes entropy of a given string
     * 
     * @param string $fileContent The content of the file to analyze
     * 
     * @return float The computed entropy, -1 if the parameter isn't a string or is null
     */
    private function _computeEntropy($fileContent)
    {
        if ($fileContent == null || !is_string($fileContent)) {
            return self::EXIT_ERROR;
        }
        $letters = str_split($fileContent);
        $freqs = $this->_getFrequencies($letters);
        $entropy = 0.0;
        foreach ($freqs as $token => $freq) {
            $rel_freq = $freq/count($letters);
            $entropy += $rel_freq * log($rel_freq, 2);
        }
        $entropy *= -1;
        return $entropy;
    }
    
    /**
     * Iterates over the array of tokens, and count their frequency
     * 
     * @param array $tokens Tokens if the string
     * 
     * @return array 2D array mapping tokens and the frequency
     */
    private function _getFrequencies($tokens)
    {
        $freqs = array();
        for ($i = 0; $i < count($tokens); $i++) {
            if (!isset($freqs[$tokens[$i]])) {
                $freqs[$tokens[$i]] = 1;
            } else {
                $freqs[$tokens[$i]]++;
            }
        }
        return $freqs;
    }

    
}