<?php

class UploadedFileResponse {
    public static function error(UploadedFile $upFile) {
        $result = new stdclass();
        $result->name = $upFile->origName;
        $result->size = $upFile->size;
        $result->error = $upFile->error;
        $result->type = $upFile->type;
        return $result;
    }

    public static function ok(UploadedFile $upFile) {
        $result = new stdclass();
        $result->name = $upFile->origName;
        $result->size = $upFile->size;
        $result->type = $upFile->type;
        return $result;
    }
}

?>