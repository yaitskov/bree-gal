<?php

class PipeStream extends BaseStream {

    public static function open($path, $mode) {
        $s = new PipeStream();
        $s->resource = popen($path, $mode);
        return $s;
    }

    public function __desctruct() {
        if ($this->resource) {
            pclose($this->resource);
        }
    }

    public function getSize() {
        return null;
    }
}