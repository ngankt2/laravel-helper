<?php

use Ngankt2\LaravelHelper\ZiSecurity;

if (!function_exists('_hide_string')) {
    function _hide_string($string, $start = 0, $length = 0, $re = '*'): mixed
    {
        if (empty($string)) return false;

        if (!is_string($string)) {
            return $string;
        }

        $strArr    = [];
        $mb_strlen = mb_strlen($string);
        while ($mb_strlen) {
            $strArr[]  = mb_substr($string, 0, 1, 'utf8');
            $string    = mb_substr($string, 1, $mb_strlen, 'utf8');
            $mb_strlen = mb_strlen($string);
        }
        $strlen = count($strArr);
        $begin  = $start >= 0 ? $start : ($strlen - abs($start));
        $end    = $last = $strlen - 1;
        if ($length > 0) {
            $end = $begin + $length - 1;
        } elseif ($length < 0) {
            $end -= abs($length);
        }
        for ($i = $begin; $i <= $end; $i++) {
            $strArr[$i] = $re;
        }
        if ($begin >= $end || $begin >= $last || $end > $last) return str_repeat($re, 5);
        return implode('', $strArr);
    }
}


if (!function_exists('_build_token_with_session')) {
    function _build_token_with_session($string): string
    {
        return ZiSecurity::buildTokenWithSession($string);
    }
}
if (!function_exists('_encrypt_live_time_in_session')) {
    function _encrypt_live_time_in_session($string): string
    {
        return ZiSecurity::buildSID($string, session()->getId());
    }
}
if (!function_exists('_decrypt_live_time_in_session')) {
    function _decrypt_live_time_in_session($string): string
    {
        return ZiSecurity::getIDFromSID($string, session()->getId());
    }
}
if (!function_exists('_encrypt_static')) {
    function _encrypt_static($string): string
    {
        return ZiSecurity::encryptOther($string);
    }
}
if (!function_exists('_decrypt_static')) {
    function _decrypt_static($string): string
    {
        return ZiSecurity::decryptOther($string);
    }
}

if (!function_exists('_validate_token_with_session')) {
    function _validate_token_with_session($token, $string): string
    {
        return ZiSecurity::validateTokenWithSession($token, $string);
    }
}



