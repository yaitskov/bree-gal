<?php

class FileStream extends BaseStream {

    protected $path;

    public function getPath() { return $this->path; }

    public static function open($path, $mode) {
        $s = new FileStream();
        $s->path = $path;
        $s->resource = fopen($path, $mode);
        return $s;
    }

    public function __desctruct() {
        if ($this->resource) {
            fclose($this->resource);
        }
    }

    public function getSize() {
        return filesize($this->path);
    }

    public function removeFile() {
        FsUtils::drop($this->path);
    }

}
?>