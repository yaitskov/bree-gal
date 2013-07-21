<?php

/**
 * the purpose is similar to FileApiWrapper. Also the class brings more
 * semantic to http headers map.
 */
class HttpWrapper extends CApplicationComponent {
    /**
     *  @var array copy of $_SERVER
     */
    private $server;

    public function __construct($server = null) {
        if (is_null($server)) {
            $this->server = $_SERVER;
        } else {
            $this->server = $server;
        }
    }

    /**
     * Extracts file name from HTTP_CONTENT_DISPOSITION header.
     * @returns null if such header is missing
     * example: Content-Disposition: INLINE; FILENAME= "foo.html"
     */
    public function extractFileName() {
        if (isset($this->server['HTTP_CONTENT_DISPOSITION'])) {
            return rawurldecode(preg_replace(
                '/(^[^"]+")|("$)/',
                '',
                $this->server['HTTP_CONTENT_DISPOSITION']));
        }
        return null;
    }

    /** Content-Range header, which has the following form:
     * **** Content-Range: bytes 0-524287/2000000
     */
    public function contentRange() {
        if (isset($this->server['HTTP_CONTENT_RANGE'])) {
            $arr = preg_split('/[^0-9]+/', $this->server['HTTP_CONTENT_RANGE']);
            return new ContentRange(intval($arr[1]), intval($arr[2]), intval($arr[3]));
        }
        return null;
    }

    public function contentLength() {
        return intval(@$this->server['CONTENT_LENGTH']);
    }

    public function accept() {
        return $this->server['HTTP_ACCEPT'];
    }

    public function contentType() {
        return $this->server['CONTENT_TYPE'];
    }

    public function packUtfDownloadName($name) {
        return str_replace('+', '%20', urlencode($name));
    }

    public function sendImage($file, $cache_expire) {
        header("Content-Type: image/jpeg");
        header("Content-Length: " . filesize($file));

        header("Pragma: public");
        header("Cache-Control: max-age=".$cache_expire);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');

        $fp = fopen($file, "rb");
        fpassthru($fp);
        fclose($fp);
    }

    public function sendFile(BaseStream $srcStream, $utfName,
        $mime = 'application/octests', $asciiName = 'file')
    {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename=' . $asciiName
            . '; filename*=UTF-8\'\'' . self::packUtfDownloadName($utfName));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $size = $srcStream->size;
        if ($size) {
            header('Content-Length: ' . $size);
        }
        ob_clean();
        flush();
        $srcStream->copyTo(FileStream::open("php://output", 'w'));
    }
}