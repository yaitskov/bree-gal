<?php
class ActiveRecord extends CActiveRecord {
    public function getAttributeLabel($attrName) {
        return Yii::t($this->i18Category, parent::getAttributeLabel($attrName));
    }

    public function getI18Category() {
        return 'model';
    }

    public function getAttributePlaceholder($attrName) {
        return $this->getAttributeLabel($attrName);
    }

    /**
     * Tries to call save method. If it's falied then Exception is thrown
     * with the message as plain text of all validation errors.
     */
    public function saveOrEx() {
        if (!$this->save()) {
            throw new ModelValidationException($this);
        }
    }

    public function errorsToText() {
        $message = "failed to save entity " . $this->id . " of table '"
            . $this->tableName() . "'\n";
        foreach ($this->errors as $attr => $errors) {
            if (is_array($errors)) {
                $message .= $this->getAttributeLabel($attr) . ": \n";
                foreach ($errors as $errMsg) {
                    $message .= $errMsg . "\n";
                }
            } else {
                $message .= $this->getAttributeLabel($attr) . ": " . $errors . "\n";
            }
        }
        return $message;
    }

    /**
     * Copy errors to another model. E.g from active record to form.
     * To deliver errors to a web page where user can see them.
     */
    public function copyErrors($dstModel) {
        foreach ($this->errors as $attr => $errors) {
            if (is_array($errors)) {
                foreach ($errors as $errMsg) {
                    $dstModel->addError($attr, $errMsg);
                }
            } else {
                $dstModel->addError($attr, $errors);
            }
        }
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

    public function loadById($id) {
        $obj = $this->findByPk($id);
        if ($obj === null) {
            throw new EntityNotFoundException(get_class($this) . ' ' . $id . ' doesn\'t  exist');
        }
        return $obj;
    }

    /**
     *  the error is not related with any particualr field
     */
    public function addEntityError($message) {
        $this->addError('system', $message);
    }
}
?>