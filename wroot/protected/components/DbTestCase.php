<?php

class DbTestCase extends CDbTestCase {

    private $fileFactory;

    private function getFileFactory() {
        if ($this->fileFactory == null) {
            $this->fileFactory = new TmpFileFactory;
        }
        return $this->fileFactory;
    }

    public function createFile() {
        return $this->getFileFactory()->createFile();
    }

    public function createDir() {
        return $this->getFileFactory()->createDir();
    }

    public function assertSave($model) {
        if (!$model->save()) {
            $this->assertTrue(false, "failed to save " . get_class($model)
                . " id = " . $model->id . "\ncause: " . CJSON::encode($model->errors)
                . "\nattributes: " . CJSON::encode($model->attributes));
        }
    }
    /**
     * create mock without calling constructor.
     * PHPUnit fails by default to create class mock
     * if it hasn't constructor without arguments.
     * And overriding that makes the call bogous.
     */
    public function mkMock($className, $methods = array()) {
        return $this->getMock($className, $methods, array(), '', false);
    }
}

?>