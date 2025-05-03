<?php

namespace Epubli\Common\Tools;

class HtmlTools
{
    /**
     * @param $html
     * @return string
     */
    public static function convertEntitiesNamedToNumeric($html)
    {
        return strtr($html, include('htmlEntityMap.php'));
    }

    /**
     * @param $name
     * @return bool
     */
    public static function isBlockLevelElement($name)
    {
        return in_array($name, include('htmlBlockLevelElements.php'));
    }

    /**
     * performs a tag-aware truncation of (html-) strings, preserving tag integrity
     * @param array|string $html
     * @param int|string $length
     * @return bool|string
     */
    public static function truncate($html, $length = "20%")
    {
        $htmls = is_array($html) ? $html : [$html];
        foreach ($htmls as &$htmlString) {
            if (is_string($length)) {
                $length = trim($length);
                /* interpret percentage value */
                if (str_ends_with($length, '%')) {
                    $length = strlen((string) $htmlString) * substr($length, 0, -1) / 100;
                }
            }
            $htmlString = substr((string) $htmlString, 0, $length);
            /* eliminate trailing truncated tag fragment if present */
            $htmlString = preg_replace('/<[^>]*$/is', '', $htmlString);
        }

        return is_array($html) ? $htmls : array_pop($htmls);
    }

    /**
     * strips all occurring html tags from $html (which can either be a string or an array of strings),
     * preserving all content enclosed by all tags in $keep and
     * dumping the content residing in all tags listed in $drop
     * @param array|string $html
     * @param array $keep
     * @param array $drop
     * @return array|string
     */
    public static function stripHtmlTags(
        $html,
        $keep =
        ['title', 'br', 'p', 'h1','h2','h3','h4','h5','span','div','i','strong','b', 'table', 'td', 'th', 'tr'],
        $drop =
        ['head','style']
    ) {
        $htmls = is_array($html) ? $html : [$html];
        foreach ($htmls as &$htmlString) {
            foreach ($drop as $dumpTag) {
                $htmlString = preg_replace("/<$dumpTag.*$dumpTag>/is", "\n", (string) $htmlString);
            }
            $htmlString = preg_replace("/[\n\r ]{2,}/i", "\n", (string) $htmlString);
            $htmlString = preg_replace("/[\n|\r]/i", '<br />', (string) $htmlString);

            /* @TODO: remove style tags and only keep body content (drop head) */
            $tempFunc = function ($matches) use ($keep) {
                $htmlNode = "<" . $matches[1] . ">" . strip_tags((string) $matches[2]) . "</" . $matches[1] . ">";
                if (in_array($matches[1], $keep)) {
                    return " " . $htmlNode . " ";
                } else {
                    return "";
                }
            };

            $allowedTags = implode("|", array_values($keep));
            $regExp = '@<(' . $allowedTags . ')[^>]*?>(.*?)<\/\1>@i';
            $htmlString = preg_replace_callback($regExp, $tempFunc, (string) $htmlString);

            $htmlString = strip_tags((string) $htmlString, "<" . implode("><", $keep) . ">");
        }
        /* preserve injected variable cast type (string|array) when returning processed entity */
        return is_array($html) ? $htmls : array_pop($htmls);
    }
}
