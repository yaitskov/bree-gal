<?php

/**
 *  It's introduced to simplify unit testing classes intensively working with files
 *  files (e.g. UploadHandler).
 *  High logic classes delegate him not only work with files but also with streams.
 */
class FileApiWrapper extends CApplicationComponent {

    /**
     *  Moves file content. If $srcPath is local file path then
     *  the rename function is used. Otherwise file_put_content.
     *  @param string $dstPath path to file in local file system.
     *  @param mixed $srcPath can be string or resource.
     *  @throw ValidationException  source file is not uploaded nor a stream
     */
    public function moveFile($dstPath, $srcPath) {
        if (is_string($srcPath)) {
            rename($srcPath, $dstPath);
        } else {
            file_put_contents($dstPath, $srcPath, 0);
            fclose($srcPath);
        }
    }

    public function moveUploadedFile($dstPath, $srcPath) {
        if (is_string($srcPath)) {
            if (is_uploaded_file($srcPath)) {
                rename($srcPath, $dstPath);
            } else {
                throw new ValidationException("file '$srcPath' is not uploaded");
            }
        } else {
            file_put_contents($dstPath, $srcPath, 0);
            fclose($srcPath);
        }
    }

    public function isUploaded($path) {
        return is_uploaded_file($path);
    }

    public function isFile($path) {
        return is_file($path);
    }

    public function getFileSize($path) {
        return FsUtils::getFileSize($path);
    }

    public function dropFile($path) {
        if ($this->isExists($path)) {
            return unlink($path);
        }
    }

    public function isExists($path) {
        return file_exists($path);
    }

    /**
     * Ensures that folder path exists i.e.
     * create folder if they are missing.
     *
     * @param string dst path to file
     */
    public function ensurePath($filePath) {
        $this->createDir(dirname($filePath));
    }

    public function createDir($path) {
        if (is_dir($path)) {
            return 1;
        }
        return mkdir($path, 0777 & ~umask(), true);
    }

    public function setFileSize($name,  $size) {
        $handle = fopen($name, "wr");
        $r = ftruncate($handle, $size);
        fclose($handle);
        return $r;
    }
}