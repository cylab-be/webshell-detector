<?php
namespace AnalyzerNS;

require_once 'const.php';

class SignaturesAnalyzer
{
    private function compareFingerprints($pFingerprints, $pFileContent)
    {
        $key = null;
        foreach ($pFingerprints as $fingerprint => $shell) {
            if (preg_match($fingerprint, $pFileContent)) {
                # [version] => 1359928984 db content FIXME?!?!?
                if ($fingerprint == "version") {
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
        foreach ($fingerprints as $fingerprint => $shell) {
            if (strpos($fingerprint, 'bb:') !== false) {
                $fingerprint = base64_decode(str_replace('bb:', '', $fingerprint));
            }
            $fp_regex['/' . preg_quote($fingerprint, '/') . '/'] = $shell;
        }
        $fp_flag0 = $this->compareFingerprints($fp_regex, $pFileContent);
        $fp_flag1 = $this->compareFingerprints($fp_regex, base64_encode($pFileContent));
        //FIXME remove this
        echo "flag 0:".$fp_flag0; //version
        echo "\nflag 1:".$fp_flag1;
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
