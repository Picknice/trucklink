<?php
$month_short = [
    'jan',
    'feb',
    'mar',
    'apr',
    'may',
    'jun',
    'jul',
    'aug',
    'sep',
    'oct',
    'nov',
    'dec'
];

class DateView
{
    public static function normalizeDate($date, $haveYear = false)
    {
        global $month_short;

        $result = mb_substr($date, 5, 6);
        $result = mb_substr($result, -2) . ' ' . $month_short[mb_substr($result, 0, 2) - 1];

        if ($haveYear) {
            $result .= ' ' . mb_substr($date, 0, 4);
        }

        return $result;
    }

    public static function normalizeDateSql($date)
    {
        global $month_short;

        $result = explode(' ', $date);

        $year = count($result) == 2 ? date('Y') : $result[2];
        $month = (int) array_search(mb_strtolower($result[1]), $month_short) + 1;
        if($month < 10){
            $month = '0' . $month;
        }
        $day = $result[0];
        if($day < 10){
            $day = '0' . $day;
        }

        $result = "{$year}-{$month}-{$day}";

        return $result;
    }
}