<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends FormModel {
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;

    public function getIdentity() {
        return $this->_identity;
    }

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()	{
		return array(
			array('username, password', 'required'),
			array('password', 'authenticate'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params) {
		$this->_identity = new UserIdentity($this->username, $this->password);
        $this->_identity->authenticate();
		if ($this->_identity->errorCode !== UserIdentity::ERROR_NONE) {
			$this->addError('password', 'Incorrect username or password.');
        }
	}
}
