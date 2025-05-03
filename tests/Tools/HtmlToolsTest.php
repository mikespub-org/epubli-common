<?php

namespace Epubli\Common\Tools;

use PHPUnit\Framework\TestCase;

class HtmlToolsTest extends TestCase
{
    public function testConvertEntitiesNamedToNumeric()
    {
        $this->assertEquals('&#160;', HtmlTools::convertEntitiesNamedToNumeric('&nbsp;'));
    }

    public function testIsBlockLevelElement()
    {
        $this->assertTrue(HtmlTools::isBlockLevelElement('div'));
        $this->assertFalse(HtmlTools::isBlockLevelElement('a'));
    }
}
