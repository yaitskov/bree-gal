<?php

/**
 * @var int    $id
 * @var string $name
 * @var int    $created
 * @var int    $owner_id
 */
class Gallery extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	public function tableName()	{
		return '{{gallery}}';
	}
	public function rules()	{
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>128),
		);
	}

    protected function beforeDelete() {
        foreach ($this->photoes as $photo) {
            $photo->delete();
        }
        return true;
    }

	public function relations()	{
		return array(
			'photoes' => array(self::HAS_MANY, 'Photo', 'gallery_id',
                'order'    =>'`order` ASC'),
			'galleryIcons' => array(self::HAS_MANY, 'Photo', 'gallery_id',
                'limit' => 3,
                'order'    =>'`order` ASC'),
		);
	}

	public function getUrl() {
        return Yii::app()->createUrl('gallery/view', array('id' => $this->id));
	}
}