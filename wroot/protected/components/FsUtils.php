<?php
class FsUtils {

    public static function drop($dirOfFile) {
        self::dropFolderWithFiles($dirOfFile);
    }

    public static function dropFolderWithFiles($dir) {
        if (!file_exists($dir)) {
            return;
        }
        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);
        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (!self::dropFolderWithFiles($dir . DIRECTORY_SEPARATOR . $file)) {
                chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
                if (!self::dropFolderWithFiles($dir . DIRECTORY_SEPARATOR . $file)) {
                    return false;
                }
            };
        }
        return rmdir($dir);
    }

    public static function getFileSize($file_path, $clear_stat_cache = false) {
        if ($clear_stat_cache) {
            clearstatcache(true, $file_path);
        }
        return NumUtils::fixIntegerOverflow(filesize($file_path));
    }

    /**
     * @param resource value what tmpfile() returns.
     */
    public static function getPathOfTmpFile($tmpHandle) {
        $metaDatas = stream_get_meta_data($tmpHandle);
        return $metaDatas['uri'];
    }

    public static function createTempFile($suffix="") {
        return tempnam("", $suffix);
    }

    public static function joinPathName($path, $name) {
        if ($path) {
            return $path . '/' . $name;
        } else {
            return $name;
        }
    }

    public static function goRoundFiles($path, $callback) {
        return self::goRoundDirs($path,
            function ($dir) use ($callback) {
                $h = opendir($dir);
                while (($file = readdir($h)) !== false) {
                    if (call_user_func_array($callback, array($file, $dir))) {
                        return true;
                    }
                }
                closedir($h);
            });
    }

    public static function goRoundDirs($path, $callback) {
        if (call_user_func_array($callback, array($path))) {
            return true;
        }
        foreach (glob($path . '/*', GLOB_ONLYDIR) as $dir) {
            if (self::goRoundDirs($dir, $callback)) {
                return true;
            }
        }
        return false;
    }

    public static function createAutoTermFile($suffix="") {
        return new AutoTermFile(self::createTempFile($suffix));
    }

    public static function createAutoTermDir() {
        return new AutoTermFile(self::createTempDir());
    }


    public static function createTempDir() {
        while (1) {
            $tmpFile = tempnam("", "");
            unlink($tmpFile);
            if (@mkdir($tmpFile)) {
                return $tmpFile;
            }
        }
    }

    public static function string2Stream($body) {
        $s = fopen("php://memory", "wr");
        fwrite($s, $body);
        fseek($s, 0);
        return $s;
    }
}
?>