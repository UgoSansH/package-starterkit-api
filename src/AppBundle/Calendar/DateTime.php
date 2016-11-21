<?php

namespace AppBundle\Calendar;

use DateTime as BaseDateTime;
use InvalidArgumentException;

/**
 * Extended DateTime
 */
class DateTime extends BaseDateTime
{
    const DATE_PATTERN     = '/^\d{4}-\d{2}-\d{2}$/';
    const TIME_PATTERN     = '/^\d{2}:\d{2}:\d{2}$/';
    const DATETIME_PATTERN = '/^[\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}\+[\d]{4}$/';

    const DEFAULT_DATE     = 'Y-m-d';
    const DEFAULT_TIME     = 'H:i:s';
    const DEFAULT_DATETIME = BaseDateTime::ISO8601;

    /**
     * Create new DateTime instance from string
     *
     * @param string $date
     * @param string $format
     *
     * @return self
     * @throws InvalidArgumentException
     */
    public static function createFromString($date, $format = null)
    {
        if (is_null($format)) {
            if (!preg_match(self::DATETIME_PATTERN, $date)) {
                if (!preg_match(self::DATE_PATTERN, $date)) {
                    if (!preg_match(self::TIME_PATTERN, $date)) {
                        throw new InvalidArgumentException(sprintf('Invalid date time format "%s"', $date));
                    } else {
                        $format = self::DEFAULT_TIME;
                    }
                } else {
                    $format = self::DEFAULT_DATE;
                }
            } else {
                $format = self::DEFAULT_DATETIME;
            }
        }

        return self::createFromFormat($format, $date);
    }

    public function isSameDay(DateTime $date)
    {
       return (($this->format('Y') == $date->format('Y')) && ($this->format('m') == $date->format('m')) && ($this->format('d') == $date->format('d')));
    }

}
