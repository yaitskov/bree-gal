<?php

/**
 * Nail with equal width and height always.
 */
class SquarePreview extends ImageProcessor {
    protected $side;
    public function __construct($side) {
        $this->side = (int)$side;
        if ($this->side < 1) {
            throw new ValidationException('Invalid side \'' . $side . '\'');
        }
    }

    public function transform($srcPath, $dstPath) {
        $required = $this->side;
        $img = Yii::app()->imagine;
        $image = $img->open($srcPath);
        $size = $image->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();

        if ($width > $height)
            $longest = round($width * $required / $height);
        else if ($width < $height)
            $longest = round($height * $required / $width);
        else
            $longest = $required;
        $this->scaleByLength($image, $longest);

        if ($width > $height) {
            $image->crop($img->point(round(($longest - $required) / 2), 0),
                $img->box($required, $required));
        } else if ($width < $height) {
            $image->crop($img->point(0, round(($longest - $required) / 2)),
                $img->box($required, $required));
        }
        $image->save($dstPath);
    }
}

?>