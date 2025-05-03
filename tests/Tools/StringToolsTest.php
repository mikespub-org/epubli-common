<?php

namespace Epubli\Common\Tools;

use PHPUnit\Framework\TestCase;

class StringToolsTest extends TestCase
{
    /**
     * @param string $search The substring to be replaced.
     * @param string $replace The replacement string.
     * @param string $subject The original string.
     * @param string $expected The expected result.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideReplaceFirstOccurrenceData')]
    public function testReplaceFirstOccurrence($search, $replace, $subject, $expected)
    {
        $result = StringTools::replaceFirstOccurrence($search, $replace, $subject);
        $this->assertEquals($expected, $result);
    }

    public static function provideReplaceFirstOccurrenceData()
    {
        return [
            ['needle', 'camel', 'Haystack containing a needle.', 'Haystack containing a camel.'],
            ['needle', 'camel', 'Needlestack containing a needle.', 'Needlestack containing a camel.'],
            ['Needle', 'Camel', 'Needlestack containing a needle.', 'Camelstack containing a needle.'],
            ['/\\$.*?()[]^', '\\1$1', '/\\$.*?()[]^/\\$.*?()[]^', '\\1$1/\\$.*?()[]^'],
        ];
    }

    public function testStripNonAsciiChars()
    {
        $this->assertEquals(
            'Sch__ne Gr____e, Sch__tzchen!',
            StringTools::stripNonAsciiChars('Schöne Grüße, Schätzchen!')
        );
    }

    public function testStartsWith()
    {
        $this->assertTrue(StringTools::startsWith('Alphabet', 'Alpha'));
        $this->assertFalse(StringTools::startsWith('Alphabet', 'Omega'));
        $this->assertFalse(StringTools::startsWith('Alphabet', 'Alphabetagamma'));
        $this->assertTrue(StringTools::startsWith('anything', 'anything'));
        $this->assertTrue(StringTools::startsWith('anything', ''));
        $this->assertTrue(StringTools::startsWith('', ''));
    }

    public function testEndsWith()
    {
        $this->assertTrue(StringTools::endsWith('This is the end, my only friend', 'end'));
        $this->assertFalse(StringTools::endsWith('Every Year', 'New Year'));
        $this->assertFalse(StringTools::endsWith('This is the end', 'This is the end, my only friend, the end'));
        $this->assertTrue(StringTools::endsWith('anything', 'anything'));
        $this->assertTrue(StringTools::endsWith('anything', ''));
        $this->assertTrue(StringTools::endsWith('', ''));
    }

    public function testContains()
    {
        $this->assertTrue(StringTools::contains('Alphabet', 'Alpha'));
        $this->assertTrue(StringTools::contains('Alphabet', 'phabe'));
        $this->assertTrue(StringTools::contains('Alpha Beta Gamma Delta', 'Delta'));
        $this->assertTrue(StringTools::contains('Delta Gamma Beta Alpha', 'Delta G'));
        $this->assertFalse(StringTools::contains('Alphabet', 'Alphabetagamma'));
        $this->assertFalse(StringTools::contains('Alpha Beta Gamma Delta', '1, 2, 3'));
        $this->assertFalse(StringTools::contains('Alpha Beta Gamma Delta', '    '));
        $this->assertTrue(StringTools::contains('anything', 'anything'));
        $this->assertTrue(StringTools::contains('anything', ''));
        $this->assertTrue(StringTools::contains('', ''));
    }
}
