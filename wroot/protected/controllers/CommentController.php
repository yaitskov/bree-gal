<?php

class CommentController extends Controller {
	public $layout=false;

	private $_model;

	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules() {
		return array(
			array('allow', // allow authenticated users to access all actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionCreate() {
        $comment = new Comment;
        if (isset($_POST['Comment'])) {
            $comment->attributes = $_POST['Comment'];
            $comment->created = time();
            if ($comment->save()) {
                return $this->renderJson(
                    array(
                        'body' => $this->render('view', array('comment' => $comment), true),
                    ));
            }
            $this->renderJson(array('error' => "validation errors", 'causes' => $comment->errors));
        } else {
            $this->renderJson(array('error' => "no data"));
        }
    }

	public function actionDelete() {
        $comment = $this->loadModel();
        if (!$comment) {
            $this->renderJson(array('error' => 'комментарий не сущесвует'));
        }
        $comment->delete();
        $this->renderJson(array());
	}

	public function loadModel()	{
		if($this->_model===null) {
			if(isset($_GET['id']))
				$this->_model=Comment::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}
}
