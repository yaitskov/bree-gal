<?php

/**
 * Structurized HTTP_CONTENT_RANGE header.
 * Content-Range header, which has the following form:
 * **** Content-Range: bytes 0-524287/2000000
      . The first 500 bytes:
       bytes 0-499/1234

      . The second 500 bytes:
       bytes 500-999/1234

      . All except for the first 500 bytes:
       bytes 500-1233/1234

      . The last 500 bytes:
       bytes 734-1233/1234

 */
class ContentRange {
    /**
     * @var int numbers of bytes
     */
    public $start, $end, $size;
    public function __construct($start, $end, $size) {
        $this->start = $start;
        $this->end = $end;
        $this->size = $size;
    }

    public function isValid() {
        return $this->end >= $this->start && $this->end < $this->size;
    }

    public function getPartSize() {
        return $this->end - $this->start + 1;
    }
}

?>