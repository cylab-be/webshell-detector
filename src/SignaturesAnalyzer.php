<?php
namespace RUCD\WebshellDetector;

class SignaturesAnalyzer implements Analyzer
{

    const ENCODE_MAX = 10;
    const FINGERPRINTS_FILE = "shelldetect.db";

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
     *
     * @param  string $pFileContent
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
        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];
            if (is_array($token)) {
                if ($token[0] === T_STRING && in_array($token[1], $decode)) {
                    array_push($code, $token[1]);
                    $times += 1;
                } elseif ($token[0] === T_CONSTANT_ENCAPSED_STRING) {
                    if (count($code) != 0 && end($code) === "(") {
                        array_push($code, $token[1]);
                    }
                } else if ($token[0] === T_VARIABLE && count($code) != 0 && end($code) === "(") {
                    array_push($code, $this->getStringVar($token[1], $tokens, $i));
                }
            } elseif ($token === "(" && count($code)) {
                array_push($code, "(");
                $parenthesis += 1;
            } elseif ($token === ")") {
                if ($parenthesis != 0) {
                    array_push($code, ")");
                    $parenthesis -= 1;
                    if ($parenthesis == 0) {
                        $encoded = "";
                        foreach ($code as $instr) {
                            $encoded.=$instr;
                        }
                        echo $encoded;
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
            } else if ($token === ";" && count($code)) {
                array_push($code, ";");
            }
        }
        if ($times > self::ENCODE_MAX) {
            //was encoded too many times -> probably dangerous
            return true;
        }
    }

    /**
     * Reads the file containing signatures
     *
     * @return array|mixed
     */
    private function getFingerprints()
    {
        $res = [];
        $fileName = __DIR__.'/../res/'. self::FINGERPRINTS_FILE;
        if (file_exists($fileName)) {
            $res = unserialize(base64_decode(file_get_contents($fileName)));
        }
        return $res;
    }

    private function getStringVar($varName, $tokens, $position)
    {
        $varState = '';
        for ($i = 0; $i < $position; $i++) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_VARIABLE && $token[1] === $varName && $i < count($tokens)-2
                && is_array($tokens[$i+2]) && $tokens[$i+2][0] === T_CONSTANT_ENCAPSED_STRING
            ) {
                if ($tokens[$i+1] === "=") {
                    $varState=substr($tokens[$i+2][1], 0, strlen($tokens[$i+2][1])-1);
                } elseif (is_array($tokens[$i+1]) && $tokens[$i+1][0] === T_CONCAT_EQUAL) {
                    $varState.=substr($tokens[$i+2][1], 1, strlen($tokens[$i+2][1])-1);
                }
            }
        }
        return $varState;
    }

    /**
     * Anonymous call
     *
     * @param  string $func name
     * @return mixed return value of the routine
     */
    public function testMe($func)
    {
        return $this->$func();
    }

    public function analyze($fileName)
    {
        return $this->scanFile(file_get_contents($fileName));
    }
}
