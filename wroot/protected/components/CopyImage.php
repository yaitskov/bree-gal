<?php


class CopyImage extends ImageProcessor
{
    public function Transform($srcPath, $dstPath)
    {
        if (!@copy($srcPath, $dstPath))
            throw new SomeEx('', "Cannot copy '$srcPath' to '$dstPath'");
    }
}


?>
