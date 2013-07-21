<?php

return array(
	'trialsrf' => array(
		'username' => 'trialsrf', // submit reg form
		'password' => User::model()->hashPassword('1'),
		'email'    => 'trialsrf@go.com',
        'status'   => ModLog::ST_NEW,
        'mail_code'=> '123',
        'created'  => 21234567,
        'pro_type' => User::TRIAL,
        'conf_mail_sent' => false,
	),
	'trialccs1' => array(
		'username' => 'trialccs1', // Confirmation Code Sent
		'password' => User::model()->hashPassword('1'),
		'email'    => 'trialccs1@go.com',
        'status'   => ModLog::ST_NEW,
        'mail_code'=> '123',
        'created'  => 1234567,
        'conf_mail_sent' => true,
        'pro_type' => User::TRIAL
	),
	'trialccs2' => array(
		'username' => 'trialccs2', // Confirmation Code Sent
		'password' => User::model()->hashPassword('1'),
		'email'    => 'trialccs2@go.com',
        'status'   => ModLog::ST_NEW,
        'mail_code'=> '123',
        'created'  => 1234590,
        'conf_mail_sent' => true,
        'pro_type' => User::TRIAL
	),

);
