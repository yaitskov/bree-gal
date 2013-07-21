<?php

class PhotoController extends Controller {
	public $layout = false;

	private $_model;

	public function filters() {
		return array_merge(parent::filters(), array('accessControl'));
	}

	public function accessRules() {
		return array(
			array('allow',  // allow all users to access 'index' and 'view' actions.
				'actions'=>array('view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated users to access all actions
                'actions' => array('delete', 'comment', 'swap'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionView() {
		$this->renderJson(array('body' => $this->render('view', array('photo' => $this->loadModel()), true)));
	}

    public function actionSwap($ida, $idb) {
        try {
            $photoA = Photo::model()->findByPk($ida);
            $photoB = Photo::model()->findByPk($idb);
            $tmp = $photoB->order;
            $photoB->order = $photoA->order;
            $photoA->order = $tmp;
            $photoA->saveOrEx();
            $photoB->saveOrEx();
            $this->renderJson(array());
        } catch (Exception $e) {
            Yii::log("error $e", 'error');
            $this->renderJson(array('error' => $e->getMessage()));
        }
    }

	public function actionIndex() {
		$gals = Gallery::model()->findAll();
		$this->render('index', array('galleries' => $gals));
	}

	public function actionDelete() {
        $model = $this->loadModel();
        $model->delete();
        $this->renderJson(array());
	}

	public function loadModel()	{
		if($this->_model===null) {
			if(isset($_GET['id'])) {
				$this->_model=Photo::model()->findByPk($_GET['id']);
			} else if(isset($_POST['Photo']['id'])) {
				$this->_model=Photo::model()->findByPk($_POST['Photo']['id']);
			}
			if($this->_model===null) {
				throw new CHttpException(404,'The requested page does not exist.');
            }
		}
		return $this->_model;
	}
}
