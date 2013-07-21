<?php

class ImagineExt implements IApplicationComponent {
    private $_init;
    private $_imagine;
    // gd eats memory to MUCH.
    public $driver = 'imagick';

    public function getIsInitialized() {
        return $this->_init;
    }

    public function init() {
        Yii::registerAutoloader(array($this, 'autoload'), true);
        switch ($this->driver) {
        case 'gd':
            $this->_imagine = new Imagine\Imagick\Imagine();
            break;
        case 'imagick':
            $this->_imagine = new Imagine\Gd\Imagine();
            break;
        default:
            throw new ValidationException("driver '" . $this->driver . "' is not supported");
        }
        $this->_init = true;
    }

    public function autoload($className) {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $file = __DIR__.'/'.$path.'.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }

   //***************************************************************************
   // Magic
   //***************************************************************************

   /**
    * Call a PHPMailer function
    *
    * @param string $method the method to call
    * @param array $params the parameters
    * @return mixed
    */
	public function __call($method, $params) {
        return call_user_func_array(array($this->_imagine, $method), $params);
	}

   /**
    * Setter
    *
    * @param string $name the property name
    * @param string $value the property value
    */
	public function __set($name, $value) {
	   $this->_imagine->$name = $value;
	}

   /**
    * Getter
    *
    * @param string $name
    * @return mixed
    */
	public function __get($name) {
        return $this->_imagine->$name;
	}

    /**
     * helper utils because the lib uses bogous namespaces and std loader doesn't now
     * how to load Imagine classes and Imagine's class loader will be available after first
     * access to Yii::app()->imagine.
     */
    public function box($w, $h) {
        return new Imagine\Image\Box($w, $h);
    }

    public function color($c) {
        return new Imagine\Image\Color($c);
    }

    public function point($x, $y) {
        return new Imagine\Image\Point($x, $y);
    }
}

?>