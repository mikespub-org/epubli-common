<?php

namespace Epubli\Common\Enum;

use Epubli\Common\Basic\Enum;

/**
 * Internet media type
 * See https://en.wikipedia.org/wiki/Internet_media_type
 * This enumeration is obviously not exhaustive. It will never be. Types are added when needed.
 *
 * @author Simon Schrape <s.schrape@epubli.com>
 *
 * @method static InternetMediaType EPUB()
 * @method static InternetMediaType JSON()
 * @method static InternetMediaType PDF()
 * @method static InternetMediaType XHTML()
 * @method static InternetMediaType GIF()
 * @method static InternetMediaType JPEG()
 * @method static InternetMediaType PNG()
 * @method static InternetMediaType CSS()
 * @method static InternetMediaType HTML()
 * @method static InternetMediaType MD()
 * @method static InternetMediaType TXT()
 * @method static InternetMediaType NCX()
 */
class InternetMediaType extends Enum
{
    public const EPUB = 'application/epub+zip';
    public const JSON = 'application/json';
    public const PDF = 'application/pdf';
    public const XHTML = 'application/xhtml+xml';

    public const GIF = 'image/gif';
    public const JPEG = 'image/jpeg';
    public const PNG = 'image/png';

    public const CSS = 'text/css';
    public const HTML = 'text/html';
    public const MD = 'text/markdown';
    public const TXT = 'text/plain';

    // Unregistered types (x prefix)
    public const NCX = 'application/x-dtbncx+xml';
}
