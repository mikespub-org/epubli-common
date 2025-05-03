<?php

namespace Epubli\Common\Tools;

/**
 * Tools for date(time) manipulation and visualization.
 * @author Timor Kodal <t.kodal@epubli.com>
 * @author Simon Schrape <simon@epubli.com>
 */
class DateTools
{
    private static $months = [
        'January' => 'Januar',
        'February' => 'Februar',
        'March' => 'März',
        'April' => 'April',
        'May' => 'Mai',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'August',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Dezember',
    ];

    private static $weekdays = [
        'Monday' => 'Montag',
        'Tuesday' => 'Dienstag',
        'Wednesday' => 'Mittwoch',
        'Thursday' => 'Donnerstag',
        'Friday' => 'Freitag',
        'Saturday' => 'Samstag',
        'Sunday' => 'Sonntag',
    ];

    /**
     * @param $date
     * @return bool
     */
    public static function validateDate($date)
    {
        if ($date instanceof \DateTime) {
            return true;
        } elseif (is_string($date)) {
            preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/is', (string) $date, $m);
            return !empty($m);
        }
        return false;
    }

    /**
     * @param string $interval
     *
     * @return \DateInterval|null
     * @throws \Exception
     */
    public static function createDateInterval($interval)
    {
        /**
         * @var \DateInterval $dateInterval
         */
        $dateInterval = null;
        if ($interval) {
            $invert = $interval[0] == '-';
            $interval = str_replace(['+', '-'], '', $interval);
            $dateInterval = new \DateInterval('P' . strtoupper((string) $interval));
            if ($invert) {
                $dateInterval->invert = 1;
            }
        }
        return $dateInterval;
    }

    /**
     * adds or subtracts a time span to or from a given date
     * syntax example (subtract 1 year, 2 months, 10 days, 5 hours, 50 minutes and 30 seconds from the current datetime):
     * DateTools::dateAdd('-1y2m10dT5h50m30s');
     *
     * @param $period
     * @param \DateTime|int|null $date
     *
     * @return \DateTime|int
     * @throws \Exception
     */
    public static function dateAdd($period, $date = null)
    {
        if (!isset($date)) {
            /**
             * @var \DateTime
             */
            $date = new \DateTime();
        }

        if (is_numeric($period)) {
            $date = new \DateTime();
            $date->setTimestamp($date->getTimestamp() + $period);
        } elseif ($period instanceof \DateTime) {
            return $period;
        } elseif (isset($period) && $period) {
            $codeInterval = 'P' . str_replace(['+', '-'], '', strtoupper((string) $period));
            $interval = new \DateInterval($codeInterval);
            if ($period[0] == "-") {
                $date->sub($interval);
            } else {
                $date->add($interval);
            }
        }

        return $date;
    }

    /**
     * Accepts a string, returns a string with translated months and weekdays to German.
     *
     * @param $string
     *
     * @return string
     */
    public static function translateMonthsWeekdaysToGerman($string)
    {
        return str_replace(
            array_map(self::to3Chars(...), array_keys(self::$weekdays)),
            array_map(self::to3Chars(...), self::$weekdays),
            str_replace(
                array_map(self::to3Chars(...), array_keys(self::$months)),
                array_map(self::to3Chars(...), self::$months),
                str_replace(
                    array_keys(self::$months),
                    self::$months,
                    str_replace(
                        array_keys(self::$weekdays),
                        self::$weekdays,
                        $string
                    )
                )
            )
        );
    }

    /**
     * Returns the 3 first chars of a string
     *
     * @param $str
     *
     * @return bool|string
     */
    private static function to3Chars($str)
    {
        return substr((string) $str, 0, 3);
    }
}
