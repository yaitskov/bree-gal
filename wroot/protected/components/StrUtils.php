<?php

class StrUtils {

    /**
     * Inserts slash after 2 letters.
     *
     * @param string $str input
     * @return string changed value
     */
    public static function insert2Slash($str) {
        return substr($str, 0, 2) . '/' . substr($str, 2);
    }

    public static function unhex($hex) {
        return pack('H*', $hex);
    }

    public static function hex($bin) {
        $r = unpack('H*', $bin);
        return $r[1];
    }
}
?>