<?php

class GalleryController extends Controller {
	public $layout = false;

	private $_model;

	public function filters() {
		return array_merge(parent::filters(), array('accessControl'));
	}

	public function accessRules() {
		return array(
			array('allow',  // allow all users to access 'index' and 'view' actions.
				'actions'=>array('view', 'index'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated users to access all actions
                'actions' => array('create', 'delete', 'upload', 'uploadFile'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 */
	public function actionView() {
		$this->render('view',array('model'=>$this->loadModel()));
	}

	public function actionIndex() {
		$gals = Gallery::model()->findAll();
		$this->render('index', array('galleries' => $gals));
	}

	public function actionCreate() {
		$model = new Gallery;
		if(isset($_POST['Gallery'])) {
			$model->attributes=$_POST['Gallery'];
            $model->owner_id = Yii::app()->user->id;
            $model->created  = time();
			if($model->save()) {
                $this->redirect($model->url);
            }
		}
		$this->render('create',array('model'=>$model,));
	}

    public function actionUpload() {
        $model = $this->loadModel();
        $this->render('upload', array('model' => $model));
    }

    public function actionUploadFile() {
        try {
            $model = Gallery::model()->loadById(@$_POST['gallery_id']);
            $result = $this->uploadFile($model, $_FILES, new HttpWrapper);
            $this->renderJson(array(UploadHandler::FILES => $result));
        } catch (Exception $e) {
            Yii::log("failed to upload $e", 'error');
            $this->renderJson(array('error' => $e->getMessage()));
        }
    }

    public function uploadFile($model, array $files, HttpWrapper $http) {
        $this->validateUpload($model, $http);
        $uploadHandler = new UploadHandler($model, Yii::app()->user->model);
        return $uploadHandler->getFilesFromClientAndSaveIt($files);
    }

    protected function validateUpload($project, $http) {
        $contentLength = $http->contentLength();
        $this->validatePostSize($contentLength);
    }

    /**
     * If user uploads file with size more than max post size then no obvious error
     * but other post parameters like project_id is lost.
     */
    protected function validatePostSize($contentLength) {
        $postMaxSize = NumUtils::suffixNumToNormal(ini_get('post_max_size'));
        if ($postMaxSize && ($contentLength > $postMaxSize)) {
            throw new ValidationException("Uploading data are too big");
        }
    }

	public function actionDelete() {
        $model = $this->loadModel();
        $model->delete();
        $this->redirect(array('index'));
	}

	public function loadModel()	{
		if($this->_model===null) {
			if(isset($_GET['id'])) {
				$this->_model=Gallery::model()->findByPk($_GET['id']);
			} else if(isset($_POST['Project']['id'])) {
				$this->_model=Gallery::model()->findByPk($_POST['Gallery']['id']);
			}
			if($this->_model===null) {
				throw new CHttpException(404,'The requested page does not exist.');
            }
		}
		return $this->_model;
	}
}
