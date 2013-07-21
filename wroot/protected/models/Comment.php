<?php

/**
 * Entity comment either project or a separate file.

 * The followings are the available columns in table 'tbl_comment':
 * @var integer $id
 * @var string  $content        text of comment
 * @var int     $photo_id
 * @var integer $created        unix time stamp

 */
class Comment extends ActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{comment}}';
	}

	public function rules()	{
		return array(
			array('content, photo_id', 'required'),
			array('photo_id', 'exist', 'className' => 'Photo', 'attributeName' => 'id'),
		);
	}

	public function relations()	{
		return array(
			'photo' => array(self::BELONGS_TO, 'Photo', 'photo_id'),
		);
	}
}