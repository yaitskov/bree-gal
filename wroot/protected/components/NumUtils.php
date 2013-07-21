<?php

class NumUtils {
    /**
     * Fix for overflowing signed 32 bit integers,
     * works for sizes up to 2^32-1 bytes (4 GiB - 1):
     */
    public static function fixIntegerOverflow($size) {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }

    public static function suffixNumToNormal($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return self::fixIntegerOverflow($val);
    }

    public static function formatMoney($money) {
        return sprintf('$ %.2f', $money);
    }

    public static function formatBytes($bytes, $precision = 2) {
        if ($bytes === null) {
            return null;
        }

        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

?>