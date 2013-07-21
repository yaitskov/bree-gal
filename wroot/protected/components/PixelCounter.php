<?php


class PixelCounter
{
    public $counter = 0;
    public $limits;
    public function __construct($limit)
    {
        $this->limits = $this->GetBytes($limit);
    }
    public function GetBytes($color)
    {
        $result = array();
        foreach (range(0,2)  as $byte)
            $result[] = $this->GetByte($color, $byte);
        return $result;
    }
    public function GetByte($color, $byte)
    {
        return ($color >> (8*$byte)) & 0xff;
    }
    public function Reset()
    {
        $this->counter = 0;
        return $this;
    }
        
    public function Run(Canvas $canvas, $vector, $start)
    {
        $canvas->GetCurrentSize($width, $height);
        $dx = $vector[0];
        $dy = $vector[1];
        $x = $start[0];
        $y = $start[1];
        
        while ($x > 0 and $y > 0 and $x < $width and $y < $height)
        {
            if ($this->GetBytes($canvas->GetPixel($x, $y)) < $this->limits)
                $this->counter += 1;
            $x += $dx;
            $y += $dy;
        }
        return $this->counter;
    }
}

?>