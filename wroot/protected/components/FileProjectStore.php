<?php

/**
 * Generate unique id for new file and ensure
 * that its name also unique in the scope of the project.
 */
class FileProjectStore {

    /**
     * @var Project model
     */
    private $project;
    /**
     * @var int user pk
     */
    private $authorId;

    public function __construct($project,$authorId) {
        $this->project = $project;
        $this->authorId = $authorId;
    }

    /**
     * Finds a file with the same name in the project. If it exists and complete
     * allocate new file with origin name plus number prefix. If it exists and incomplete
     * and reference to the existing file is returned.  If such file doesn't exist then
     * it's created with exactly the same name.
     * @param  string   file name on user PC
     * @param  string   MIME type
     * @param  int      total file size in bytes
     * @param  ContentRange not null in the case of uploading file by parts
     * @return FileModel persisted entity related with the uploaded file
     * @throw  ValidationException file is too big or crackers are detected
     */
    public function getOrNewInode($name, $mime, $size, $contentRange) {
        if (is_null($contentRange)) {
            return $this->getNewInode($name, $mime, $size, ModLog::ST_NEW);
        } else {
            $fileEntry = FileModel::model()->findFirstIncomplete($this->project, $name, $this->authorId);
            if (!$fileEntry) {
                return $this->getNewInode($name, $mimi, $size, ModLog::ST_INCOMPLETE);
            }
            return $fileEntry;
        }
    }

    protected function getNewInode($name, $mime, $size, $status) {
        $uniqName = FileModel::model()->findFreeFileNameInPro($this->project->id, $name);
        $fileEntry = $this->fillFileModel($uniqName, $mime, $size, $status);
        $fileEntry->saveOrEx();
        return $fileEntry;
    }

    protected function fillFileModel($name, $mime, $size, $status) {
        $fileModel = new FileModel;
        $fileModel->created     = time();
        $fileModel->updated     = time();
        $fileModel->author_id   = $this->authorId;
        $fileModel->cursize     = $size;
        $fileModel->project_id  = $this->project->id;
        $fileModel->mime        = $mime;
        $fileModel->oriname     = $name;
        $fileModel->description = '';
        $fileModel->status      = $status;
        return $fileModel;
    }
}

?>