<?php

/**
 * Some class hierarchy for different types of streams.
 * Initial cause to eliminate differencies between closing pclose and fclose.
 */
abstract class BaseStream extends CComponent {
    protected $resource;

    protected function __construct() { }

    public function getResource() {
        return $this->resource;
    }

    public function getIsEof() {
        return !$this->resource || feof($this->resource);
    }

    public function readBytes($size=8192) {
        return fread($this->resource, $size);
    }

    public function writeBytes($data) {
        return fwrite($this->resource, $data);
    }

    abstract public function getSize();

    public function asString() {
        return IoUtils::readStreamToString($this);
    }

    public function removeFile() {
        // do nothing
    }

    public function copyTo(BaseStream $dst, $blockSize = 8192) {
        while (!$this->isEof) {
            $block = $this->readBytes($blockSize);
            $dst->writeBytes($block);
        }
    }
}