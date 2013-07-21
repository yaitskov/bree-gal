<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

	private $_id;
    public $user;

    public function __construct($username='', $password='') {
        parent::__construct($username, $password);
    }

	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		$user = User::model()->find('username = ?', array($this->username));
		if($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
        } else {
            if (!$user->validatePassword($this->password)) {
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            } else {
                $this->authByUser($user);
            }
        }
		return $this->errorCode == self::ERROR_NONE;
	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId() {
		return $this->_id;
	}

    /**
     * For auto auth with face book.
     */
    public function authByUser(User $user) {
        $this->user = $user;
        $this->_id = $user->id;
        $this->username = $user->username;
        $session = Yii::app()->session;
        $session['username'] = $user->username;
        $this->errorCode=self::ERROR_NONE;
        $user->save();
    }

    /**
     *  @param int $user_Id User.id value
     */
    public function setId($user_id) {
        $this->_id = $user_id;
    }
}