<?php

/**
 *    задача. переделка i_crop_copy чтобы делать превью фото без искажений.
 *    сжимает/растягиваем по минимальной стороне
 *    затем обрезаем
 *
 */
class PhotoPreview
{
    public function Make($required, $srcPath, $dstPath)
    {
        $rp = new SquarePreview($required);
        $rp->transform($srcPath, $dstPath);
    }
}

?>