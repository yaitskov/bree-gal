<?php

class IoUtils {
    /**
     * Reads pipe until the end and close the pipe.
     * @param resource opened with popen function
     * @return string all content got through the pipe
     */
    public static function readStreamToString(BaseStream $s) {
        $result = '';
        while (!$s->isEof) {
            $block = $s->readBytes();
            $result .= $block;
        }
        return $result;
    }
}

?>