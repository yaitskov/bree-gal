<?php

/**
 *  More structure way of element from $_FILES.
 *  Differs from CUploadedFile it can represent php://input files
 *  and partial files (http_content_range)
 */
class UploadedFile extends CComponent {
    public $tmpPath;
    private $origName;
    private $size;
    private $type;
    private $contentRange;
    private $error;

    // translation for system error codes in $_FILE
    // PHP File Upload error message codes:
    // http://php.net/manual/en/features.file-upload.errors.php
    protected $errorMessages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload'
    );

    public function __construct(array $data) {
        $http = Yii::app()->http;
        $this->initTmpName($data);
        $this->initOrigName($data, $http);
        $this->initSize($data, $http);
        $this->initType($data, $http);
        $this->initError($data);
    }

    private function initTmpName($data) {
        if (isset($data['tmp_name'])) {
            $this->tmpPath = $data['tmp_name'];
        }
    }

    private function initOrigName($data, $http) {
        $httpHeaderName = $http->extractFileName();
        if ($httpHeaderName) {
            $this->origName = $httpHeaderName;
        } else if (isset($data['name'])) {
            $this->origName = $data['name'];
        } else {
            $this->origName = Yii::t('upload', "no-name");
        }
    }

    private function initSize($data, $http) {
        $this->contentRange = $http->contentRange();
        if ($this->contentRange) {
            $this->size = $this->contentRange->size;
        } else if (isset($data['size'])) {
            $this->size = intval($data['size']);
        } else {
            $http->contentLength();
        }
    }

    private function initType($data, $http) {
        if (isset($data['type'])) {
            $this->type = $data['type'];
        } else {
            $this->type = $http->contentType();
        }
    }

    private function initError($data) {
        if (isset($data['error'])) {
            $this->error = $this->decodeError($data['error']);
        }
    }

    public function getOrigName() {
        return $this->origName;
    }

    public function getSize() {
        return $this->size;
    }

    public function getType() {
        return $this->type;
    }

    public function getContentRange() {
        return $this->contentRange;
    }

    public function getError() {
        return $this->error;
    }

    public function setError($error) {
        $this->error = $error;
    }

    protected function decodeError($error) {
        return array_key_exists($error, $this->errorMessages)
            ? $this->errorMessages[$error]
            : $error;
    }

    public function isUploaded() {
        return Yii::app()->file->isUploaded($this->tmpPath);
    }

    public function getRealSize() {
        if ($this->isUploaded()) {
            return Yii::app()->file->getFileSize($this->tmpPath);
        } else {
            return Yii::app()->http->contentLength();
        }
    }

    public function __toString() {
        $result = 'uploaded file with name = \'' . $this->origName
            . ', type = \'' . $this->type
            . '\', size = \'' . $this->size . '\'';
        if ($this->contentRange) {
            $result .= ', content range = ' . CVarDumper::dumpAsString($this->contentRange);
        }
        return $result;
    }

    public function setSize($size) {
        $this->size = $size;
    }

    public function isPartial() {
        return $this->contentRange;
    }
}