<?php

class AutoTermFile  {

    private $path;

    public function getPath() { return $this->path; }

    public function __construct($path) {
        $this->path = $path;
    }

    public function getReadStream() {
        return FileStream::open($this->path, "r");
    }

    public function getWriteStream() {
        return FileStream::open($this->path, "wr");
    }

    public function __destruct() {
        FsUtils::drop($this->path);
    }

    public function __toString() {
        return $this->path;
    }
}