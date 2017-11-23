<?php
namespace AnalyzerNS;

require_once 'const.php';
require_once 'util.php';

class SignaturesAnalyzer
{
    private function compareFingerprints($pFingerprints, $pFileContent)
    {
        $key = null;
        foreach ($pFingerprints as $fingerprint => $shell) {
            if (preg_match($fingerprint, $pFileContent)) {
                if ($fingerprint == "/version/") {
                    break;
                }
                $key = $shell;
                break;
            }
        }
        return $key;
    }
    
    /**
     * TEST: comes from PHP-Webshell Detector, will be updated. Only for testing
     * @param string $pFileContent
     * @return NULL|NULL|array|mixed
     */
    public function scanFile($pFileContent)
    {
        if ($pFileContent == null || !strlen($pFileContent)) {
            return null;
        }
        $fingerprints = $this->getFingerprints();
        if ($fingerprints == null || !count($fingerprints)) {
            return null;
        }
        $fp_regex = array();
        $version = 0;
        foreach ($fingerprints as $fingerprint => $shell) {
            if (strpos($fingerprint, 'bb:') !== false) {
                $fingerprint = base64_decode(str_replace('bb:', '', $fingerprint));
            }
            $fp_regex['/' . preg_quote($fingerprint, '/') . '/'] = $shell;
        }
        
        //Level 1: the shell is not hidden
        $flag = $this->compareFingerprints($fp_regex, $pFileContent);
        if ($flag != null) {
            return $flag;
        }
        $flag = $this->compareFingerprints($fp_regex, base64_encode($pFileContent));
        if ($flag != null) {
            return $flag;
        }
        //Level 2+: the shell was encoded at least once
        $tokens = token_get_all($pFileContent);
        //printTokens($tokens);
        $strings = [];
        $level = 1;
        foreach ($tokens as $element) {
            if (is_array($element) && $element[0] == T_CONSTANT_ENCAPSED_STRING) {
                array_push($strings, $element[1]);
            }
        }
        for ($level = 1; $level < 10; $level++) {
            for ($i = 0; $i < count($strings); $i++) {
                $flag = $this->compareFingerprints($fp_regex, $strings[$i]);
                if ($flag != null) {
                    return $flag;
                }
                $strings[$i] = base64_decode($strings[$i] + '===');
            }
        }
    }
    
    /**
     * Reads the file containing signatures
     * @return array|mixed
     */
    private function getFingerprints()
    {
        $res = [];
        $fileName = __DIR__.'/../res/'.FINGERPRINTS_FILE;
        if (file_exists($fileName)) {
            $res = unserialize(base64_decode(file_get_contents($fileName)));
        }
        return $res;
    }
    
    /**
     * Anonymous call
     * @param string $func name
     * @return mixed return value of the routine
     */
    public function testMe($func)
    {
        return $this->$func();
    }
}
