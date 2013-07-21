<?php

class UploadHandler {

    const FILES = 'files';

    /**
     * @var FileProjectStore provides new entries for files and saves request data to files.
     */
    protected $gallery;
    protected $author;

    /**
     * @param Gallery $gallery
     * @param string  $branch   GIT branch name where to commit changes
     */
    function __construct($gallery, User $author) {
        $this->gallery = $gallery;
        $this->author = $author;
    }

    /**
     * Takes files from HTTP request and some how puts to the specified path.
     */
    public function getFilesFromClientAndSaveIt(array $filesArray) {
        $upload = isset($filesArray[self::FILES])
            ? $filesArray[self::FILES]
            : array('tmp_name'=>null); // single file from php://input
        if (!is_array($upload['tmp_name'])) {
            ArrayUtils::rollCells($upload);
        }
        $result = array();
        foreach ($upload['tmp_name'] as $i => $_) {
            $upFile = new UploadedFile(ArrayUtils::unroll($upload, $i));
            $result[] = $this->handleFileUpload($upFile);
        }
        return $result;
    }

    /**
     * Persist file from HTTP request to the disk.
     * @return array representing the file array('name'=>, 'size'=>, 'type'=>,
     *                                           'delete_url'=> ,
     *                                           'error'=> , 'url'=> )
     */
    protected function handleFileUpload(UploadedFile $upFile) {
        if (!$this->validateBeforeWrite($upFile)) {
            Yii::log('failed to upload ' . $upFile->origName
                . ' cause validation: ' . $upFile->error, 'info');
            return UploadedFileResponse::error($upFile);
        }
        try {
            $this->saveDataFile($upFile);
            return UploadedFileResponse::ok($upFile);
        } catch (Exception $e) {
            Yii::log('failed to upload file ' . $upFile . ' cause ' . $e,
                ($e instanceof ValidationException) ? 'info' : 'error');
            $upFile->error = $e->getMessage();
            return UploadedFileResponse::error($upFile);
        }
    }

    protected function validateBeforeWrite($upFile) {
        if ($upFile->error) {
            $upFile->error = $this->translateError($error);
            return false;
        }
        $realSize = $upFile->realSize;
        if ($realSize > $upFile->size) {
            $upFile->error = $this->translateError('Real uploaded size is more than declared');
            return false;
        }
        if ($crange = $upFile->contentRange) {
            if ($realSize != $crane->getPartSize()) {
                $upFile->error = $this->translateError('Uploaded part size is not equal to content range');
                return false;
            }
            if (!$crange->isValid()) {
                $upFile->error = $this->translateError('Content range is invalid');
                return false;
            }
        }
        return true;
    }

    protected function translateError($msg) {
        return Yii::t('upload', $msg);
    }


    protected function saveDataFile($upFile) {
        if ($upFile->isUploaded()) {
            $ext = strrpos($upFile->origName, '.');
            if (!$ext) {
                throw new Exception("file doesn't have any extension");
            }
            $ext = substr($upFile->origName, $ext);

            $photo = new Photo;
            $photo->created = time();
            $photo->gallery_id = $this->gallery->id;
            $photo->ext = $ext;
            $photo->saveOrEx();
            $photo->order = $photo->id;
            $photo->saveOrEx();
            try {
                $toPath = $photo->localPath;
                Yii::app()->file->ensurePath($toPath);
                $previewer = new ScaleByEdge(800, 500);
                $previewer->transform($upFile->tmpPath, $toPath);

                $toPath = $photo->iconLocalPath;
                Yii::app()->file->ensurePath($toPath);
                $previewer = new SquarePreview(100);
                $previewer->transform($upFile->tmpPath, $toPath);
                return $photo;
            } catch (Exception $e) {
                $photo->delete();
                throw $e;
            }
        } else {
            throw new Exception("Partial uploading is not available");
        }
    }

    /**
     *  check that size of already uploaded part is not less then start of the conten range
     *  and truncate is more
     */
    protected function checkContentRange($pathToFile, $upFile) {
        $start = $upFile->contentRange->start;
        $fSize = Yii::app()->file->getFileSize($pathToFile);
        if ($fSize < $start) {
            throw new ValidationException("lost part of file");
        }
        Yii::app()->file->setFileSize($pathToFile, $start);
    }
}
