<?php

class ChangePasswordForm extends FormModel {
	public $repeat;
	public $password;

	public function rules()	{
		return array(
			array('repeat, password', 'required'),
			array('password', 'compare', 'compareAttribute' => 'repeat'),
		);
	}
}
