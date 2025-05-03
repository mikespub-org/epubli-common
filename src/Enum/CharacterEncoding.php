<?php

namespace Epubli\Common\Enum;

use Epubli\Common\Basic\Enum;

/**
 * Encoding
 *
 * @author Simon Schrape <s.schrape@epubli.com>
 *
 * @method static CharacterEncoding ASCII()
 * @method static CharacterEncoding LATIN1()
 * @method static CharacterEncoding UTF8()
 * @method static CharacterEncoding UTF16LE()
 * @method static CharacterEncoding UTF16BE()
 * @method static CharacterEncoding UTF32LE()
 * @method static CharacterEncoding UTF32BE()
 */
class CharacterEncoding extends Enum
{
    public const ASCII = 'ASCII';
    public const LATIN1 = 'ISO 8859-1';
    public const UTF8 = 'UTF-8';
    public const UTF16LE = 'UTF-16 LE';
    public const UTF16BE = 'UTF-16 BE';
    public const UTF32LE = 'UTF-32 LE';
    public const UTF32BE = 'UTF-32 BE';
}
