<?php

/**
 * There is a user.
 *
 * The followings are the available columns in table 'tbl_user':
 * @var integer $id
 * @var string $pro_type        freezed, zombie
 * @var string $username
 * @var string $description
 * @var string $status
 * @var integer $created        epoch time when account was registered
 * @var integer $logined_at     last epoch time when user logined
 * @var integer $st_updated     last epoch time when status of account was changed
 * @var integer $updated        last epoch time when any data field was changed like $description
 * @var byte    $flags
 */
class User extends ActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName()	{
		return '{{user}}';
	}

	public function relations()	{
		return array(
			'galleries' => array(self::HAS_MANY, 'Gallery', 'owner_id'),
		);
	}

	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)	{
		return crypt($password,$this->password) === $this->password;
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @return string hash
	 */
	public function hashPassword($password)	{
		return crypt($password, $this->generateSalt());
	}

    public function changePassword($newPassword) {
        $this->password = $this->hashPassword($newPassword);
    }

	/**
	 * Generates a salt that can be used to generate a password hash.
	 *
	 * The {@link http://php.net/manual/en/function.crypt.php PHP `crypt()` built-in function}
	 * requires, for the Blowfish hash algorithm, a salt string in a specific format:
	 *  - "$2a$"
	 *  - a two digit cost parameter
	 *  - "$"
	 *  - 22 characters from the alphabet "./0-9A-Za-z".
	 *
	 * @param int cost parameter for Blowfish hash algorithm
	 * @return string the salt
	 */
	protected function generateSalt($cost=10) {
		if (!is_numeric($cost) || $cost < 4 || $cost > 31) {
			throw new CException(Yii::t('Cost parameter must be between 4 and 31.'));
		}
		// Get some pseudo-random data from mt_rand().
		$rand='';
		for ($i=0; $i<8; ++$i) {
			$rand .= pack('S', mt_rand(0,0xffff));
        }
		// Add the microtime for a little more entropy.
		$rand.=microtime();
		// Mix the bits cryptographically.
		$rand=sha1($rand,true);
		// Form the prefix that specifies hash algorithm type and cost parameter.
		$salt='$2a$'.str_pad((int)$cost,2,'0',STR_PAD_RIGHT).'$';
		// Append the random salt string in the required base64 format.
		$salt.=strtr(substr(base64_encode($rand),0,22),array('+'=>'.'));
		return $salt;
	}

}