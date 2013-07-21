<?php

class DateTimeUtils {

    public static function shift2Tz($minutes) {
        $tz = timezone_name_from_abbr(null, $minutes * 60, true);
        if ($tz === false) {
            $tz = timezone_name_from_abbr(null, $minutes * 60, false);
        }
        return $tz;
    }

    public static function formatPeriod($period) {
        switch ($period) {
        case 'm':
            return 'month';
        case 'd':
            return 'day';
        case 'w':
            return 'week';
        case 'y':
            return 'year';
        default:
            Yii::log("unknown period = '$period'", 'error');
            return '';
        }
    }
}