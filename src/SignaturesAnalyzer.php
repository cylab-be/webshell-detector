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
                if ($fingerprint != "/version/") {
                    $key = $shell;
                    break;
                }
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
        $decode = ["base64_decode", "gzuncompress", "gzinflate"];
        $code = [];
        $times = 0;
        $parenthesis = 0;
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] === T_STRING && in_array($token[1], $decode)) {
                    array_push($code, $token[1]);
                    $times += 1;
                } elseif ($token[0] === T_CONSTANT_ENCAPSED_STRING && count($code) != 0) {
                    if (end($code) === "(") {
                        array_push($code, $token[1]);
                    }
                }
                //TODO check if a variable is passed as param
            } elseif ($token === "(") {
                array_push($code, "(");
                $parenthesis += 1;
            } elseif ($token === ")") {
                if ($parenthesis != 0) {
                    array_push($code, ")");
                    $parenthesis -= 1;
                }
                if ($parenthesis == 0) {
                    $encoded = "";
                    foreach ($code as $instr) {
                        $encoded.=$instr;
                    }
                    eval("\$decoded=".$encoded.";");
                    if (isset($decoded)) {
                        $flag = $this->compareFingerprints($fp_regex, $decoded);
                        if ($flag != null) {
                            return $flag;
                        }
                        $flag = $this->compareFingerprints($fp_regex, base64_encode($decoded));
                        if ($flag != null) {
                            return $flag;
                        }
                    }
                }
            }
        }
        if ($times > ENCODE_MAX) {
            //was encode too many times -> probably dangerous
            return true;
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
