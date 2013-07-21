<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    public static function te($a, $b) {
        return "$a = $b";
    }
	/**
	 * @var string the default layout for the controller view. Defaults to 'column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    protected function header($str) {
        header($str);
    }

    protected function renderJson($data) {
        $this->header('Pragma: no-cache');
        $this->header('Cache-Control: no-store, no-cache, must-revalidate');
        //$this->header('Content-Disposition: inline; filename="files.json"');
        // Prevent Internet Explorer from MIME-sniffing the content-type:
        $this->header('X-Content-Type-Options: nosniff');
        $this->header('Vary: Accept');
        $this->header('Content-type: application/json');
        echo CJSON::encode($data);
    }


    public function render($view, $params, $return=false) {
        if (@$_GET['json']) {
            return $this->renderJson(array('body' => parent::render($view, $params, true)));
        } else {
            return parent::render($view, $params, $return);
        }
    }

    function init() {
        parent::init();
        $app = Yii::app();
        $app->setTimeZone('Europe/London');
        $tz = Yii::app()->user->getState('timezone');
        if ($tz) {
              Yii::app()->setTimeZone($tz);
        }
        $this->pageTitle = Yii::app()->params['sitename'];
    }
}