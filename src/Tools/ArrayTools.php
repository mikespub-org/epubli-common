<?php

namespace Epubli\Common\Tools;

/**
 * @author Timor Kodal <t.kodal@epubli.com>
 */
class ArrayTools
{
    public const SORT_ASC = 0;
    public const SORT_DEFAULT = 0;
    public const SORT_DESC = 1;
    public const SORT_NOCASE = 2;
    public const SORT_TRIM = 4;
    public const SORT_NATURAL = 8;

    public static $sortKey;
    public static $sortOptions;

    public static function distinctMerge(array $array1, array $array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = self::distinctMerge($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }
        return $merged;
    }

    /**
     * private grouping-method called by groupByKeys()
     *
     * @param array $source
     * @param array &$target
     * @param array &$keys
     * @param boolean $deep
     * @return int
     */
    private static function recursive($source, &$target, &$keys, $deep = false)
    {
        $group = array_pop($keys);
        if ($group) {
            if (is_array($source)) {
                self::recursive($source, $target[$source[$group]], $keys, $deep);
            } else {
                self::recursive($source, $target[$source->$group], $keys, $deep);
            }
        } else {
            if ($deep) {
                $target[] = $source;
            } else {
                $target = $source;
            }
        }
    }

    /**
     * Uses a given string ($groupKeys) to group an array ($arr)
     *
     * @param array $arr
     * @param string|array $groupKeys
     * @param boolean $deep
     * @return array
     */
    public static function groupByKeys($arr, $groupKeys, $deep = true)
    {
        $out = [];
        if (is_array($groupKeys)) {
            foreach ($arr as $k => $v) {
                $grp = array_reverse($groupKeys);
                self::recursive($v, $out, $grp, $deep);
            }
        } else {
            foreach ($arr as $k => $v) {
                if ($deep) {
                    $out[$v[$groupKeys]][] = $v;
                } else {
                    $out[$v[$groupKeys]] = $v;
                }
            }
        }
        return $out;
    }

    /**
     * private comparison-method using sortKey and self::$sortOptions
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    private static function sorter($a, $b)
    {
        if (!array_key_exists(self::$sortKey, $a) || !array_key_exists(self::$sortKey, $b)) {
            return 0;
        }
        $a = $a[self::$sortKey];
        $b = $b[self::$sortKey];
        self::transform($a, $b, self::$sortOptions);
        if (self::$sortOptions & self::SORT_NATURAL) {
            return (strnatcmp($a, $b));
        }
        if ($a === $b) {
            return 0;
        }
        if ($a < $b) {
            return -1;
        }
        return 1;
    }

    /**
     * transforming-method to prepare values to be sorted according to given $sortOptions
     *
     * @param array $a
     * @param array $b
     * @param int $options
     * @return void
     */
    public static function transform(&$a, &$b, $options = self::SORT_DEFAULT)
    {
        if ($options == self::SORT_DEFAULT) {
            return;
        }
        if ($options & self::SORT_NOCASE) {
            $a = strtolower($a);
            $b = strtolower($b);
        }
        if ($options & self::SORT_TRIM) {
            $a = trim($a);
            $b = trim($b);
        }
        if ($options & self::SORT_DESC) {
            $buf = $b;
            $b = $a;
            $a = $buf;
        }
    }

    /**
     * sets all keys to a given default-value or removes them from array if empty
     * @param array $data
     * @param array $keys defines which keys shall be treated
     * @return array
     */

    public static function setEmptyValues(array $data, array $keys = null)
    {
        foreach ($data as $column => $value) {
            if (empty($value)) {
                if (!isset($keys)) {
                    unset($data[$column]);
                } else {
                    if (in_array($column, $keys)) {
                        unset($data[$column]);
                    } else {
                        if (array_key_exists($column, $keys)) {
                            $data[$column] = $keys[$column];
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * perform a batched regExp - match on all elements of an array
     *
     * @param array $array
     * @param string $regExpPattern
     * @return array
     */
    public static function regExpMatches(array $array, $regExpPattern)
    {
        $results = [];
        foreach ($array as $entry) {
            preg_match($regExpPattern, (string) $entry, $m);
            if (array_key_exists(0, $m)) {
                $results[] = $m;
            }
        }

        return $results;
    }

    /**
     * @param string $target
     * @param array $scope
     * @param int $amount
     * @param int $threshold
     * @return array
     */
    public static function matchText($needle, array $haystack, $amount = 1, $threshold = 50)
    {
        $best = 0;
        $matches = [];

        /*  replace underscores, dashes, tabs and linefeeds with whitechars, remove consequent whitechars */
        $needle = strtolower(trim((string) preg_replace('/[ \-\t\n\r]+/', ' ', (string) $needle)));

        foreach ($haystack as $key => $value) {
            // match value with target
            similar_text($needle, strtolower(trim((string) preg_replace('/[ \-\t\n\r]+/', ' ', (string) $value))), $percent);

            // only consider value, if result is better than actual best result and above threshold
            if ($percent >= $best && $percent >= $threshold) {

                // update actual best result
                $best = $percent;

                // push match on top of matches-array
                array_unshift(
                    $matches,
                    [
                        "key" => $key,
                        "value" => $value,
                        "result" => round($percent, 2),
                    ]
                );
            }
        }
        if ($amount == 1) {
            return current($matches);
        }

        // return requested amount of matches
        return array_slice($matches, 0, $amount);
    }

    /**
     * Use a given array-key to sort an array ($array)
     * Attention: this method uses an anonymous function and needs php-version >= 5.3
     *
     * @param array &$array
     * @param string|array $sortKey
     * @param int $sortOptions
     * @return array
     */
    public static function sortByKey(&$array, $sortKey, $sortOptions = self::SORT_ASC)
    {
        usort($array, function ($cmp_a, $cmp_b) use ($sortKey, $sortOptions) {
            if (!array_key_exists($sortKey, $cmp_a) || !array_key_exists($sortKey, $cmp_b)) {
                return 0;
            }
            $a = $cmp_a[$sortKey];
            $b = $cmp_b[$sortKey];
            self::transform($a, $b, $sortOptions);

            if ($sortOptions & self::SORT_NATURAL) {
                return (strnatcmp($a, $b));
            }
            if ($a === $b) {
                return (0);
            }
            if ($a < $b) {
                return -1;
            }
            return 1;
        });

        return $array;
    }

    /**
     * use a given key to extract all respective values
     *
     * @param array $array
     * @param string $key
     * @param array|string $values
     * @return array
     */
    public static function extractValues($array, $key, $values = null)
    {
        if (is_null($values)) {
            /* if $values is empty, return only a flat array with values */
            $transform = function ($item) use ($key) {
                return $item[$key];
            };

            return array_map($transform, $array);
        } else {
            if (is_array($values)) {
                /* if $values contains an array, return an array containing all values specified by $values */
                foreach ($values as $value) {
                    foreach ($array as $v) {
                        if (isset($v[$key])) {
                            $grouped[$v[$key]][$value] = $v[$value] ?? null;
                        }
                    }
                }

                return $grouped;
            } else {
                /* if $values contains a string, return an array with the specified key - values */
                foreach ($array as $v) {
                    if (isset($v[$key])) {
                        $grouped[$v[$key]] = $v[$values] ?? null;
                    }
                }

                return $grouped;
            }
        }
    }

    /**
     * @param array $data
     * @param array|string $route
     * @return array
     */
    public static function arrayRoute($data, $route)
    {
        if (!is_array($route)) {
            $route = explode(".", $route);
        }
        foreach ($route as $key) {
            $data = $data[$key];
        }
        return $data;
    }

    /**
     * render the contents of an array as a csv
     *
     * @param array $data
     * @param bool|array $fieldNames
     * @param string $delimiter
     * @param string $enclosure
     * @param string $terminator
     * @return string
     */
    public static function toCsv($data, $fieldNames = true, $delimiter = ',', $enclosure = '"', $terminator = "\n")
    {
        $csvOutput = '';
        if ($fieldNames) {
            if (true === $fieldNames) {
                $firstRow = array_shift($data);
                if (is_array($firstRow)) {
                    $firstRow = array_values($firstRow);
                }
            } else {
                if (is_array($fieldNames)) {
                    $firstRow = array_values($fieldNames);
                }
            }
            if (is_array($firstRow) && !empty($firstRow)) {
                foreach ($firstRow as $field) {
                    if (is_string($field)) {
                        $csvOutput .= $field . $delimiter;
                    }
                }
                $csvOutput = substr($csvOutput, 0, -1) . $terminator;
            }
        }
        foreach ($data as $row => $fields) {
            if (!is_array($fields)) {
                $fields = [$row, $fields];
            }
            foreach ($fields as $value) {
                if ($value instanceof \DateTime || $value instanceof \Date) {
                    $csvOutput .= $value->format('Y-m-d H:i:s');
                } elseif (is_numeric($value)) {
                    $csvOutput .= $value . $delimiter;
                } elseif (is_string($value)) {
                    $csvOutput .= $enclosure . $value . $enclosure . $delimiter;
                }
            }
            $csvOutput = substr($csvOutput, 0, -1) . $terminator;
        }

        return $csvOutput;
    }

    /**
     * render the contents of an array as hierarchical tree string
     * @param array $data
     * @param string $terminator
     * @param string $indentation
     * @param int $indentationLimit
     * @param mixed $textLengthLimit
     * @param string $ellipsis
     * @return string
     */
    public static function toText(
        $data,
        $terminator = "\n",
        $indentation = '  ',
        $indentationLimit = null,
        $textLengthLimit = null,
        $ellipsis = '[...]'
    ) {
        $textOut = '';
        $temp = function ($data, $indent = 0) use (
            $textOut,
            $terminator,
            $indentation,
            &$temp,
            $indentationLimit,
            $textLengthLimit,
            $ellipsis
        ) {
            if (is_array($textLengthLimit)) {
                $textLengthLimitStart = reset($textLengthLimit);
                $textLengthLimitEnd = end($textLengthLimit);
            } else {
                $textLengthLimitStart = $textLengthLimit;
                $textLengthLimitEnd = 0;
            }
            foreach ($data as $key => $value) {
                $textOut .= str_repeat($indentation, $indent);
                if (is_array($value) || is_object($value)) {
                    if (!isset($indentationLimit) || $indent <= $indentationLimit) {
                        $textOut .= "$key :" . $terminator . $temp($value, $indent + 1);
                    } else {
                        $textOut .= "$key : " . $ellipsis . $terminator;
                    }
                } else {
                    if ($value instanceof \DateTime || $value instanceof \Date) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                    if (isset($textLengthLimit) && (strlen($value) > $textLengthLimit)) {
                        $value = substr(
                            $value,
                            0,
                            $textLengthLimitStart
                        ) . $ellipsis . ($textLengthLimitEnd > 0 ? substr(
                            $value,
                            -$textLengthLimitEnd
                        ) : '');
                    }
                    $textOut .= "$key : $value" . $terminator;
                }
            }

            return $textOut;
        };

        return $temp($data);
    }
}
