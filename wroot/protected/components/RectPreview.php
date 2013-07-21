<?php


/**
 *   Размеры получаемой фотографии всегда равны заданным
 */
class RectPreview extends ImageProcessor
{
    protected $width, $height, $ratio;
    public function __construct($width, $height)
    {
        $this->width = (int)$width;
        if ($this->width < 1)
            throw new SomeEx('', 'Invalid width \'' . $width . '\'');        
        $this->height = (int)$height;
        if ($this->height < 1)
            throw new SomeEx('', 'Invalid height \'' . $height . '\'');
        $this->ratio = $this->height / $this->width;
    }
    public function Transform($srcPath, $dstPath)
    {
        $img = new Canvas();            
        $img->load($srcPath);
        $img->GetOrigSize($width, $height);
        
        Logger::get()->log(__FUNCTION__ . " $srcPath  ow " . $this->width . "  oh " . $this->height . " or " . $this->ratio  );        
        $ratio = $height / $width;
        /**
           (preg_match('/n1/', $srcPath) and $this->width == 200 and $this->height == 100) or
        */
        if ($this->ratio < $ratio
            and ($this->width <= $width
                or $this->height <= $height
                or ($this->width >= $width and $this->height >= $height)))
            $this->FrameRatioLess($img, $height, $width, $ratio);
        else
            $this->FrameRatioMore($img, $height, $width, $ratio);
        
        $img->save($dstPath);                
    }

    public function FrameRatioLess($img, $height, $width, $ratio)
    {
        Logger::get()->log(__FUNCTION__ . " h = $height; w = $width r = $ratio  ");        
        // first step - scale
        $nwidth = $this->width;

        // now width of image = width of frame
        // but height of image can be more that frame one
        // new height
        //  w_old / h_old = w_new / h_new =>   h_new = (w_new * h_old) / w_old
        $nheight = round(($nwidth * $height) / $width);
        if ($width > $height)
            $img->scaleByLength($nwidth);
        else
            $img->scaleByLength($nheight);        
        
        Logger::get()->log(__FUNCTION__ . " new width $nwidth;  new height $nheight; ");                  
        /* if ($nheight == $this->height) */
        /*     return; */
        
        $img->crop(
            0, round(($nheight - $this->height) / 2),
            $this->width, $this->height);
   
    }
    public function FrameRatioMore($img, $height, $width, $ratio)
    {
        Logger::get()->log(__FUNCTION__ . " h = $height; w = $width r = $ratio  ");                
        // first step - scale
        $nheight = $this->height;
        $nwidth = round(($nheight * $width) / $height);
        if ($width > $height)            
            $img->scaleByLength($nwidth);
        else
            $img->scaleByLength($nheight);            
        Logger::get()->log(__FUNCTION__ . " new width $nwidth;  old $width  ");          
        // second crop

        $img->crop(
            round(($nwidth - $this->width) / 2), 0,
            $this->width, $this->height);
    }
}

?>