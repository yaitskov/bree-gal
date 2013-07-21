<?php

class ArrayUtils {
    /**
     *  Value in each cell of the array wrap into another array.
     */
    public static function rollCells(array &$arr) {
        foreach ($arr as &$val) {
            $val = array($val);
        }
    }


    /**
     * @param array arr can be empty array or array like array('name' => array("a", "b"), 'size'=> array(30,20))
     * @return array with keys of $arr and values of subarray element at position $i
     */
    public static function unroll(array $arr, $i) {
        $result = array();
        foreach ($arr as $k => $v) {
            if (isset($v[$i])) {
                $result[$k] = $v[$i];
            } else {
                $result[$k] = null;
            }
        }
        return $result;
    }
}

?>