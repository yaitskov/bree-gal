<?php

class ScaleByEdge extends ImageProcessor {
    protected $maxWidth, $maxHeight;
    public function __construct($maxWidth, $maxHeight) {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
    }

    public function transform($srcPath, $dstPath) {
        $img = Yii::app()->imagine;
        $image = $img->open($srcPath);
        $size = $image->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();


        if ($width > $this->maxWidth and $height > $this->maxHeight) {
            if ($width - $this->maxWidth > $height - $this->maxHeight) {
                $this->scale($image, $this->maxWidth, $dstPath);
            } else {
                if ($width / $height < 1) {
                    $this->scale($image, $this->maxHeight, $dstPath);
                } else {
                    $this->scale($image, $this->maxWidth, $dstPath);
                }
            }
        } else {
            $this->scale($image, $this->maxHeight, $dstPath);
        }
    }

    protected function scale($image, $length, $dst) {
        if (!$this->scaleByLength($image, $length)) {
            throw new ValidationException("cannot scale");
        }
        $image->save($dst);
    }
}

?>