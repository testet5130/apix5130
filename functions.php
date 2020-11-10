<?php

    require_once("config.php");

    function esc_char($txt) {
        return htmlspecialchars($txt, ENT_QUOTES);        
    }

    function debug() {
        return DEBUGGING_MESSAGES;
    }

    function anyErr($err) {
        return count($err) > 0 ? true : false;
    }

    function esc_echo($txt, $max=0, $min=0) {

        $err = [];

        $txt = esc_char(strval($txt));
        $ret = $txt;

        if( !anyErr($err) && $min > 0 ) {
            if( strlen($txt) < $min ) {
                $err["min"] = "Minimum string length is ".$min;
            } else {
                $ret = $txt;
            }
        }

        if( !anyErr($err) && $max > 0 ) {
            if( strlen($txt) > $max ) {
                $err["max"] = "Maximum string length is ".$max;
            } else {
                $ret = $txt;
            }
        }

        if( anyErr($err) ) {
            return json_encode($err);
        } else {
            echo $ret;
        }

    }

    function print_j($str) {
        print_r( json_encode($str) );
    }

    function isLogin() {
        
    }

    function exists($var) {
        if($_SERVER["REQUEST_METHOD"] == "POST" ) {
            if( isset($_POST[$var]) && !empty($_POST[$var]) ) {
                return true;
            } else {
                return false;
            }
        } else {
            if( isset($_GET[$var]) && !empty($_GET[$var]) ) {
                return true;
            } else {
                return false;
            }
        }
    }

    function validate() {
        
    }

?>