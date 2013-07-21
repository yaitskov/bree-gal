<?php

/**
 * @var integer $id
 * @var integer $created
 * @var int     $gallery_id
 */
class Photo extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	public function tableName()	{
		return '{{photo}}';
	}

	public function rules()	{
		return array(
		);
	}

	public function relations()	{
		return array(
			'gallery' => array(self::BELONGS_TO, 'Gallery', 'gallery_id'),
            'comments' => array(self::HAS_MANY, 'Comment', 'photo_id',
                'order'  => 'created ASC'),
		);
	}

    /**
     *  direct url to static file of normal size
     */
    public function getImageUrl() {
        return '/files/normal/' . $this->id . $this->ext;
    }
    public function getIconUrl() {
        return '/files/icons/' . $this->id . $this->ext;
    }

    public function getIconLocalPath() {
        return Yii::app()->params['icon-files'] . '/' . $this->id . $this->ext;
    }

    public function getLocalPath() {
        return Yii::app()->params['normal-files'] . '/' . $this->id . $this->ext;
    }

    protected function beforeDelete() {
        foreach ($this->comments as $comment) {
            $comment->delete();
        }
        return true;
    }

    protected function afterDelete() {
        @unlink($this->localPath);
        @unlink($this->iconLocalPath);
    }
}