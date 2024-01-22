<?php

class DateController {
    public static function availableUntil($date, $seconds) {
        return time() - strtotime($date) < $seconds;
    }
}