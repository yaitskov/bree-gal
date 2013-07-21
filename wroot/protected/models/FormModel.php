<?php

class FormModel extends CFormModel {
    public function getAttributeLabel($attrName) {
        return Yii::t($this->i18Category, parent::getAttributeLabel($attrName));
    }

    public function getI18Category() {
        return 'model';
    }

    public function getAttributePlaceholder($attrName) {
        return $this->getAttributeLabel($attrName);
    }

    public function addException($e) {
        $this->addError('system', $e->getMessage());
    }

    public function isRequired($attrName) {
        $vals = $this->getValidators($attrName);
        foreach ($vals as $val) {
            if ($val instanceof CRequiredValidator) {
                return true;
            }
        }
        return false;
    }
}