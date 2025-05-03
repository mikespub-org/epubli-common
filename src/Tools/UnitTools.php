<?php

namespace Epubli\Common\Tools;

/**
 * Tools for converting and formatting measurement units.
 * @author Timor Kodal <t.kodal@epubli.com>
 * @author Simon Schrape <simon@epubli.com>
 */
class UnitTools
{
    public const MM_PER_INCH = 25.4;
    public const PT_PER_INCH = 72;

    /**
     * Convert millimeters to DTP points.
     * @param float $mmValue value in millimeters
     * @return float value in DTP points
     */
    public static function mmToPt($mmValue)
    {
        return $mmValue / self::MM_PER_INCH * self::PT_PER_INCH;
    }

    /**
     * Convert DTP points to millimeters.
     * @param float $ptValue value in DTP points
     * @return float value in millimeters
     */
    public static function ptToMm($ptValue)
    {
        return $ptValue / self::PT_PER_INCH * self::MM_PER_INCH;
    }

    /**
     * Returns a human readable representation of elapsed time durations
     * @param $value
     * @param int $digits
     * @param bool $fullName
     * @return string
     * @author Timor Kodal <t.kodal@epubli.com>
     */
    public static function humanReadableSeconds($value, $digits = 2, $fullName = false)
    {
        $dimensions = [
            "factor" => [60, 60, 60, 24, 7, 4],
            "short" => ["s", "m", "h", "d", "w", "m"],
            "long" => ["Seconds", "Minutes", "Hours", "Days", "Weeks", "Months"],
        ];
        $dim = 0;
        $buf = $value;
        while ($buf > $factor = $dimensions["factor"][$dim]) {
            $dim++;
            $buf = $buf / $factor;
        }

        return sprintf("%02.{$digits}f", $buf) . " " . $dimensions[($fullName ? 'long' : 'short')][$dim];
    }

    /**
     * Returns a human readable representation of bytes (filesizes, memory etc)
     * @param $value
     * @param int $digits
     * @param bool $fullName
     * @return string
     * @author Timor Kodal <t.kodal@epubli.com>
     */
    public static function humanReadableBytes($value, $digits = 2, $fullName = false)
    {
        $dimensions = [
            "short" => ["B", "KB", "MB", "GB", "TB", "PB"],
            "long" => ["Bytes", "Kilobyte", "Megabyte", "Gigabyte", "Terabyte", "Pentabyte"],
        ];
        $factor = 1024;
        $dim = 0;
        $buf = $value;
        while ($buf > $factor) {
            $dim++;
            $buf = $buf / $factor;
        }

        return sprintf("%02.{$digits}f", $buf) . " " . $dimensions[($fullName ? 'long' : 'short')][$dim];
    }
}
