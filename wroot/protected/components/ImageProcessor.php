<?php

/**
 *  Factory and base class for image nail generators.
 */
abstract class ImageProcessor {
    // avatar
    const RECT = 'RectPreview';
    // big and small carousel and image preview and best
    const SQUARE = 'SquarePreview';
    // just in case
    const ORIG   = 'CopyImage';
    // for banners
    const BY_EDGE = 'ScaleByEdge';

    static public function create(array $params) {
        $class = @$params['class'];
        switch ($class) {
        case self::RECT:
            return new RectPreview(@$params['width'], @$params['height']);
        case self::SQUARE:
            return new SquarePreview(@$params['side']);
        case self::ORIG:
            return new CopyImage();
        case self::BY_EDGE:
            return new ScaleByEdge(@$params['maxwidth'], @$params['maxheight']);
        default:
            throw new ValidationException("ImageProcessor '$class' is invalid");
        }
    }
    /**
     *   Does actual transformation
     *   @param string source path
     *   @param string dst    path
     *   @throw Exception
     */
    abstract public function transform($srcPath, $dstPath);

    public function scaleByLength($image, $side) {
        $size = $image->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();

        if ($width >= $height) {
            $new_x = $side;
            $new_y = round(($new_x / $width) * $height, 0);
        } else {
            $new_y = $side;
            $new_x = round(($new_y / $height) * $width, 0);
        }
        return $image->resize(Yii::app()->imagine->box($new_x, $new_y));
    }
}

?>