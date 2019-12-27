<?php


namespace App\Helpers;


use DateTime;

class DateHelper
{
    private const DEFAULT_DATE_FORMAT = 'Y-m-d';
    private const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:m:s';

    public static function dateToString(DateTime $date): string
    {
        return $date->format(self::DEFAULT_DATE_FORMAT);
    }

    public static function stringToDate(string $date): DateTime
    {
        return DateTime::createFromFormat(self::DEFAULT_DATE_FORMAT, $date);
    }

    public static function datetimeToString(DateTime $dateTime): string
    {
        return $dateTime->format(self::DEFAULT_DATETIME_FORMAT);
    }

    public static function stringToDatetime(string $datetime): DateTime
    {
        return DateTime::createFromFormat(self::DEFAULT_DATETIME_FORMAT, $datetime);
    }

}