<?php

namespace Epubli\Common\Basic;

use PHPUnit\Framework\TestCase;

class IntegerSetTest extends TestCase
{
    public function testDefault()
    {
        $set = new IntegerSet();
        $this->assertEquals('', $set);
        $this->assertEquals(0, $set->count());
        $this->assertEquals([], $set->getItems());
    }

    public function testNull()
    {
        $set = new IntegerSet(null);
        $this->assertEquals('', $set);
        $this->assertEquals(0, $set->count());
        $this->assertEquals([], $set->getItems());
    }

    public function testEmpty()
    {
        $set = new IntegerSet('');
        $this->assertEquals('0', $set);
        $this->assertEquals(1, $set->count());
        $this->assertEquals([0], $set->getItems());
    }

    public function testZero()
    {
        $set = new IntegerSet(0);
        $this->assertEquals('0', $set);
        $this->assertEquals(1, $set->count());
        $this->assertEquals([0], $set->getItems());
    }

    public function testStringZero()
    {
        $set = new IntegerSet('0');
        $this->assertEquals('0', $set);
        $this->assertEquals(1, $set->count());
        $this->assertEquals([0], $set->getItems());
    }

    public function testInteger()
    {
        $set = new IntegerSet(123);
        $this->assertEquals('123', $set);
        $this->assertEquals(1, $set->count());
        $this->assertEquals([123], $set->getItems());
    }

    public function testFloat()
    {
        $set = new IntegerSet(123.321);
        $this->assertEquals('123', $set);
        $this->assertEquals(1, $set->count());
        $this->assertEquals([123], $set->getItems());
    }

    public function testStringWithSpaces()
    {
        $set = new IntegerSet('  5 -6 ,4  ,5,80  -30,   40 - 81   ');
        $this->assertEquals('4-6,30-81', $set);
        $this->assertEquals(55, $set->count());
        $this->assertEquals(
            [
                4, 5, 6,
                30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
                40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
                50, 51, 52, 53, 54, 55, 56, 57, 58, 59,
                60, 61, 62, 63, 64, 65, 66, 67, 68, 69,
                70, 71, 72, 73, 74, 75, 76, 77, 78, 79,
                80, 81,
            ],
            $set->getItems()
        );
    }

    public function testStringWithLeadingComma()
    {
        $set = new IntegerSet(',5-6,8');
        $this->assertEquals('0,5-6,8', $set);
        $this->assertEquals(4, $set->count());
        $this->assertEquals([0, 5, 6, 8], $set->getItems());
    }

    public function testStringWithTrailingComma()
    {
        $set = new IntegerSet('5-6,8,');
        $this->assertEquals('0,5-6,8', $set);
        $this->assertEquals(4, $set->count());
        $this->assertEquals([0, 5, 6, 8], $set->getItems());
    }

    public function testStringWithDoubleComma()
    {
        $set = new IntegerSet('5-6,,8');
        $this->assertEquals('0,5-6,8', $set);
        $this->assertEquals(4, $set->count());
        $this->assertEquals([0, 5, 6, 8], $set->getItems());
    }

    public function testInsert()
    {
        $set = new IntegerSet('1,4');
        $set->insert(2);
        $this->assertEquals('1-2,4', $set);
        $this->assertEquals([1, 2, 4], $set->getItems());
    }

    public function testInsertNegative()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No negative values allowed!');
        $set = new IntegerSet();
        $set->insert(-2);
    }

    public function testInsertRange()
    {
        $set = new IntegerSet('1-6,9-13');
        $set->insertRange(5, 10);
        $this->assertEquals('1-13', $set);
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13], $set->getItems());
    }

    public function testInsertRangeNegativeFrom()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No negative values allowed!');
        $set = new IntegerSet();
        $set->insertRange(-2, 10);
    }

    public function testInsertRangeNegativeTo()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No negative values allowed!');
        $set = new IntegerSet();
        $set->insertRange(2, -10);
    }

    public function testRemove()
    {
        $set = new IntegerSet('1-3');
        $set->remove(2);
        $this->assertEquals('1,3', $set);
        $this->assertEquals([1, 3], $set->getItems());
    }

    public function testRemoveRange()
    {
        $set = new IntegerSet('1-5,7-9');
        $set->removeRange(5, 8);
        $this->assertEquals('1-4,9', $set);
        $this->assertEquals([1, 2, 3, 4, 9], $set->getItems());
    }

    public function testContains()
    {
        $set = new IntegerSet('1,2,3');
        $this->assertTrue($set->contains(2));
    }


    // Iteration interface
    public function testIterationInterface()
    {
        $set = new IntegerSet('1-7');
        $set->rewind();
        $this->assertTrue($set->valid());
        $this->assertEquals(1, $set->current());
        $this->assertEquals(0, $set->key());
        $set->next();
        $this->assertTrue($set->valid());
        $this->assertEquals(2, $set->current());
        $this->assertEquals(1, $set->key());
        $set->last();
        $this->assertTrue($set->valid());
        $this->assertEquals(7, $set->current());
        $this->assertEquals(6, $set->key());
        $set->next();
        $this->assertFalse($set->valid());
        $this->assertFalse($set->current());
        $this->assertNull($set->key());
    }
}
