<?php

class TmpFileFactory {
    private $files = array();

    public function createFile() {
        $result = FsUtils::createTempFile();
        $this->files[] = $result;
        return $result;
    }

    public function createDir() {
        $result = FsUtils::createTempDir();
        $this->files[] = $result;
        return $result;
    }

    public function __destruct() {
        foreach ($this->files as $file) {
            FsUtils::drop($file);
        }
    }
}