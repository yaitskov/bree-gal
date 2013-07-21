<?php

class SiteController extends Controller {
	public $layout = false;

	public function actions() {
		return array(
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	public function filters() {
		return array_merge(parent::filters(), array('accessControl'));
	}

	public function accessRules() {
		return array(
			array('allow', // allow authenticated users to access all actions
                'actions' => array('changePassword'),
				'users'=>array('@'),
			),
			array('allow',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function login(User $user) {
        $identity = new UserIdentity();
        $identity->authByUser($user);
        $duration = 3600*24*30;
        Yii::app()->user->login($identity, $duration);
        $this->redirect(Yii::app()->user->returnUrl);
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
	    if($error=Yii::app()->errorHandler->error) {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

    public function actionChangePassword() {
        $form = new ChangePasswordForm;
        if (isset($_POST['ChangePasswordForm'])) {
            $form->attributes = $_POST['ChangePasswordForm'];
            if ($form->validate()) {
                $m = Yii::app()->user->model;
                $m->changePassword($form->repeat);
                if ($m->save()) {
                    $this->redirect(array('gallery/index'));
                }
            }
        }
        $this->render('changePassword', array('form' => $form));
    }

	public function actionLogin() {
		if (!defined('CRYPT_BLOWFISH') || !CRYPT_BLOWFISH) {
			throw new CHttpException(500, "PHP need Blowfish support for crypt().");
        }
		$model = new LoginForm;
		if(isset($_POST['LoginForm'])) {
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate()) {
                $this->login($model->identity->user);
            }
		}
		// display the login form
		$this->render('login', array('model' => $model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}
