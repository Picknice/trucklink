<?php
class NormalizeView {
    public static function price($price) {
        $price = strrev($price);

        $result = "";

        for ($i = 0; $i < strlen($price); $i++) {
            if ($i%3 == 0) {
                $result .= " ";
            }

            $result .= $price[$i];
        }
        
        return trim(strrev($result));
    }

    public static function checkPrice($price, $method = 0, $money_icon = "$") {
        if ($method != 1) {
            return $money_icon . " " . self::price($price===null ? '0' : $price);
        }
        return self::method($method);
    }
    
    public static function method($method) {
        if (!$method) return 'No price';
        else if ($method == 1) {
            return "Find out price";
        }
    }

    public static function date($date) {
        global $MONTHS;
        $year = intval(substr($date, 0, 4));
        $date = substr($date, 5);
        $day = substr($date, -2);
        $month = (int) substr($date, 0, 2);
        $month = $MONTHS[$month - 1];
        $month = mb_substr($month, 0, 3);

        return "$day $month";
    }
}