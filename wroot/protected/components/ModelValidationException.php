<?php

class ModelValidationException extends ValidationException {
    /**
     *  @var ActiveRecord
     */
    public $model;

    public function __construct(CActiveRecord $model) {
        parent::__construct($model->errorsToText());
        $this->model = $model;
    }
}
?>