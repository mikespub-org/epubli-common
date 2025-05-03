<?php

namespace Epubli\Common\Tools;

class StringTools
{
    /**
     * parses a text for template vars and replaces all occurrences
     * with the respective values from the given data array resource
     *
     * usage example:
     *
     * StringTools::templateVars('this is an example text with a $placeholders.one',["placeholder"=>["one"=>"1"]]);
     *
     * @param $text
     * @param $data
     */
    public static function templateVars($text, $data, $prefix = '\$', $suffix = '')
    {
        $getArrayNode = function ($path, $buf = null) use ($data, &$getArrayNode) {
            if (!isset($buf)) {
                $buf = $data;
            }
            $seg = array_shift($path);
            if (isset($buf[$seg])) {
                if (is_array($buf[$seg])) {
                    return $getArrayNode($path, $buf[$seg]);
                } else {
                    return $buf[$seg];
                }
            }

            return ($buf[$seg] ?? "");
        };
        $pattern = '/' . $prefix . '([0-9a-zA-Z_\[\.\]]*)' . $suffix . '/is';
        preg_match_all($pattern, (string) $text, $matches);
        arsort($matches[0]);
        foreach ($matches[1] as &$match) {
            $split = preg_split('/[\]\[\.]+/is', $match);
            $match = $getArrayNode(preg_split('/[\]\[\.]+/is', $match));
        }
        foreach ($matches[0] as $key => $value) {
            $text = str_replace($value, $matches[1][$key], $text);
        }
        $text = str_replace($matches[0], $matches[1], $text);

        return $text;
    }

    /**
     * Replace only first occurrence of a substring.
     * @param string $search The substring to be replaced.
     * @param string $replace The replacement string.
     * @param string $subject The original string.
     * @return string The resulting string.
     */
    public static function replaceFirstOccurrence($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);

        return $pos === false ? $subject : substr_replace($subject, $replace, $pos, strlen($search));
    }

    /**
     * Strip non-ascii characters
     * @see http://stackoverflow.com/questions/8781911/remove-non-ascii-characters-from-string-in-php#answer-8781968
     * @param $text string with non-ascii characters
     * @return string with non-ascii characters replaced by underscores
     */
    public static function stripNonAsciiChars($text)
    {
        return preg_replace('/[[:^print:]]/', '_', (string) $text);
    }

    /**
     * Determine whether one string starts with another.
     * @param string $haystack String to examine.
     * @param string $needle String to find.
     * @return bool Whether $haystack starts with $needle.
     */
    public static function startsWith($haystack, $needle)
    {
        return '' === $needle || str_starts_with($haystack, $needle);
    }

    /**
     * Determine whether one string ends with another.
     * @param string $haystack String to examine.
     * @param string $needle String to find.
     * @return bool Whether $haystack ends with $needle.
     */
    public static function endsWith($haystack, $needle)
    {
        return '' === $needle || strrpos($haystack, $needle) === strlen($haystack) - strlen($needle);
    }

    /**
     * Determine whether one string contains another.
     * @param string $haystack String to examine.
     * @param string $needle String to find.
     * @return bool Whether $haystack contains $needle.
     */
    public static function contains($haystack, $needle)
    {
        return '' === $needle || str_contains($haystack, $needle);
    }

    /**
     * Split a text into words.
     * @param string $text The original text
     * @return array The text split into words.
     */
    public static function splitIntoWords($text)
    {
        // Prefix any sequence of capital letters with a space,
        // surround numbers with spaces,
        // and replace any sequence of non-letters with one space.
        $normalized = preg_replace(
            [
                '/\p{Lu}+/u',
                '/\p{N}+/u',
                '/[^\p{L}\p{N}]+/u',
            ],
            [
                ' \0',
                ' \0 ',
                ' ',
            ],
            $text
        );

        // Split into single words.
        return array_filter(explode(' ', (string) $normalized));
    }

    /**
     * Convert a string to Snake_case.
     * @param string $text The original text
     * @return string The re-formatted text.
     */
    public static function toSnakeCase($text)
    {
        $words = self::splitIntoWords($text);

        return implode('_', $words);
    }

    /**
     * Convert a string to lower_snake_case.
     * @param string $text The original text
     * @return string The re-formatted text.
     */
    public static function toLowerSnakeCase($text)
    {
        return mb_strtolower(self::toSnakeCase($text));
    }

    /**
     * Convert a string to UPPER_SNAKE_CASE.
     * @param string $text The original text
     * @return string The re-formatted text.
     */
    public static function toUpperSnakeCase($text)
    {
        return mb_strtoupper(self::toSnakeCase($text));
    }

    /**
     * Convert a string to CamelCase.
     * @param string $text The original text
     * @param bool $lower Whether to output lowerCamelCase
     * @return string The re-formatted text.
     */
    public static function toCamelCase($text, $lower = false)
    {
        $words = self::splitIntoWords($text);
        $titleCasedWords = array_map(function ($word, $lower = false) {
            return mb_convert_case($word, $lower ? MB_CASE_LOWER : MB_CASE_TITLE);
        }, $words, [$lower]);

        return implode('', $titleCasedWords);
    }

    /**
     * Convert a string to lowerCamelCase.
     * @param string $text The original text
     * @return string The re-formatted text.
     */
    public static function toLowerCamelCase($text)
    {
        return self::toCamelCase($text, true);
    }

    /**
     * Convert a string to UpperCamelCase.
     * @param string $text The original text
     * @return string The re-formatted text.
     */
    public static function toUpperCamelCase($text)
    {
        return self::toCamelCase($text, false);
    }
}
